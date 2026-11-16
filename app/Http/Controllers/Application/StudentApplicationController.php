<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\SubmitApplicationRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\OprecCatalog;
use App\Services\ApplicationSelectionService;
use App\Services\DocumentStorageService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentApplicationController extends Controller
{
    public function store(
        SubmitApplicationRequest $request,
        int $catalogId,
        DocumentStorageService $storageService
    ): RedirectResponse {
        $catalog = OprecCatalog::findOrFail($catalogId);
        $user = Auth::user();

        if (!$user->isMahasiswa()) {
            return back()->with('error', 'Hanya akun mahasiswa yang memiliki hak akses untuk melamar ke tim KKN.');
        }

        try {
            DB::transaction(function () use ($request, $catalog, $user, $storageService) {
                // 1. Buat record lamaran
                $application = Application::create([
                    'user_id' => $user->id,
                    'team_id' => $catalog->team_id,
                    'division_id' => (int) $request->input('division_id'),
                    'status' => 'pending',
                    'submitted_at' => now(),
                    'notes' => $request->input('notes'),
                ]);

                // 2. Simpan file CV (Wajib)
                $cvFile = $request->file('cv_file');
                $storageService->storeDocument($application, $cvFile, 'cv');

                // 3. Simpan file Portofolio jika ada (Opsional)
                if ($request->hasFile('portfolio_file')) {
                    $portfolioFile = $request->file('portfolio_file');
                    $storageService->storeDocument($application, $portfolioFile, 'portfolio');
                }
            });

            return redirect()->route('applications.history')
                ->with('success', 'Lamaran Anda berhasil dikirimkan ke tim KKN.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal mengirimkan lamaran: ' . $e->getMessage());
        }
    }

    public function history(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('info', 'Akun Administrator tidak memiliki riwayat lamaran mahasiswa.');
        }

        if ($user->isPic()) {
            return redirect()->route('pic.dashboard')->with('info', 'Akun PIC tidak memiliki riwayat lamaran mahasiswa.');
        }

        $applications = Application::with(['team', 'division', 'documents'])
            ->where('user_id', $user->id)
            ->latest('submitted_at')
            ->paginate(15);

        return view('application.history', [
            'applications' => $applications,
        ]);
    }

    public function cancel(int $id, ApplicationSelectionService $selectionService): RedirectResponse
    {
        try {
            $selectionService->cancelByStudent($id, Auth::id());
            return back()->with('success', 'Lamaran berhasil dibatalkan.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function viewDocument(Request $request, int $documentId, DocumentStorageService $storageService)
    {
        $document = ApplicationDocument::with('application.team')->findOrFail($documentId);
        $user = Auth::user();

        // Otorisasi akses dokumen: Mahasiswa pemilik lamaran, PIC tim terkait, atau Admin
        $isOwner = $document->application->user_id === $user->id;
        $isPic = $document->application->team->pic_user_id === $user->id;
        $isAdmin = $user->isAdmin();

        if (!$isOwner && !$isPic && !$isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat dokumen ini.');
        }

        $disk = config('filesystems.default');

        // Jika S3 digunakan, arahkan langsung ke Pre-signed S3 URL
        if ($disk === 's3') {
            return redirect()->away($storageService->getTemporaryUrl($document));
        }

        // Pastikan file fisik ada; jika belum ada (misal dari data seeder pengujian), buat berkas PDF placeholder valid
        if (!Storage::disk($disk)->exists($document->s3_key)) {
            $minimalPdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
            Storage::disk($disk)->put($document->s3_key, $minimalPdf);
        }

        // Untuk driver lokal, stream atau unduh file PDF
        $disposition = $request->has('download') ? 'attachment' : 'inline';
        return Storage::disk($disk)->response($document->s3_key, $document->original_filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $document->original_filename . '"',
        ]);
    }
}
