<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use App\Http\Requests\PIC\StoreCatalogRequest;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogManagementController extends Controller
{
    protected function getPicTeam(): KknTeam
    {
        $team = KknTeam::where('pic_user_id', Auth::id())->first();

        if (!$team) {
            abort(404, 'Data tim KKN tidak ditemukan untuk akun PIC ini.');
        }

        return $team;
    }

    public function index(): View
    {
        $team = $this->getPicTeam();
        $catalogs = $team->catalogs()->with('divisions')->latest()->get();

        return view('pic.catalogs.index', [
            'team' => $team,
            'catalogs' => $catalogs,
        ]);
    }

    public function create(): View
    {
        $team = $this->getPicTeam();

        return view('pic.catalogs.create', [
            'team' => $team,
        ]);
    }

    public function store(StoreCatalogRequest $request): RedirectResponse
    {
        $team = $this->getPicTeam();
        $validated = $request->validated();

        $catalog = $team->catalogs()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'deadline' => $validated['deadline'],
            'status' => 'draft', // Selalu mulai dari status Draft (FR-09)
        ]);

        return redirect()->route('pic.catalogs.show', $catalog->id)
            ->with('success', 'Draf katalog OPREC berhasil dibuat. Silakan tambahkan divisi dan kuota.');
    }

    public function show(int $id): View
    {
        $team = $this->getPicTeam();
        $catalog = $team->catalogs()->with(['divisions.applications'])->findOrFail($id);

        return view('pic.catalogs.show', [
            'team' => $team,
            'catalog' => $catalog,
        ]);
    }

    public function edit(int $id): View
    {
        $team = $this->getPicTeam();
        $catalog = $team->catalogs()->findOrFail($id);

        return view('pic.catalogs.edit', [
            'team' => $team,
            'catalog' => $catalog,
        ]);
    }

    public function update(StoreCatalogRequest $request, int $id): RedirectResponse
    {
        $team = $this->getPicTeam();
        $catalog = $team->catalogs()->findOrFail($id);
        $validated = $request->validated();

        $originalStatus = $catalog->status;
        $newStatus = $originalStatus;

        // Aturan FR-09: Perubahan pada katalog yang telah Published harus kembali melalui persetujuan administrator
        if ($originalStatus === 'published') {
            $newStatus = 'pending_approval';
        }

        $catalog->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'deadline' => $validated['deadline'],
            'status' => $newStatus,
        ]);

        $message = 'Katalog berhasil diperbarui.';
        if ($originalStatus === 'published') {
            $message .= ' Karena terdapat perubahan pada katalog yang telah aktif, status dikembalikan ke Menunggu Persetujuan (Pending Approval).';
        }

        return redirect()->route('pic.catalogs.show', $catalog->id)->with('success', $message);
    }

    public function submitApproval(int $id): RedirectResponse
    {
        $team = $this->getPicTeam();
        $catalog = $team->catalogs()->with('divisions')->findOrFail($id);

        if (!in_array($catalog->status, ['draft', 'rejected'], true)) {
            return back()->with('error', 'Hanya katalog berstatus Draft atau Rejected yang dapat diajukan untuk persetujuan.');
        }

        if ($catalog->divisions->isEmpty()) {
            return back()->withErrors([
                'divisions' => 'Katalog harus memiliki minimal satu divisi dengan kuota sebelum diajukan ke administrator.',
            ]);
        }

        $catalog->update([
            'status' => 'pending_approval',
            'rejection_note' => null,
        ]);

        return back()->with('success', 'Katalog berhasil diajukan kepada administrator untuk persetujuan publikasi.');
    }

    public function closeManually(int $id): RedirectResponse
    {
        $team = $this->getPicTeam();
        $catalog = $team->catalogs()->findOrFail($id);

        if ($catalog->status !== 'published') {
            return back()->with('error', 'Hanya katalog berstatus Published yang dapat ditutup.');
        }

        $catalog->update(['status' => 'closed']);

        return back()->with('success', 'Katalog OPREC berhasil ditutup.');
    }
}
