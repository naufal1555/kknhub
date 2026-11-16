<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Services\ApplicationSelectionService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PicApplicationReviewController extends Controller
{
    protected function getPicTeam(): KknTeam
    {
        return KknTeam::where('pic_user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request, int $catalogId): View
    {
        $team = $this->getPicTeam();
        $catalog = $team->catalogs()->with(['divisions'])->findOrFail($catalogId);

        $divisionId = $request->query('division_id');
        $status = $request->query('status');

        $query = Application::with(['user', 'division', 'documents'])
            ->where('team_id', $team->id)
            ->whereIn('division_id', $catalog->divisions->pluck('id'));

        if ($divisionId) {
            $query->where('division_id', (int) $divisionId);
        }

        if ($status && in_array($status, ['pending', 'accepted', 'rejected', 'cancelled'], true)) {
            $query->where('status', $status);
        }

        $applications = $query->latest('submitted_at')->paginate(20);

        // Hitung kuota terisi per divisi
        $quotas = [];
        foreach ($catalog->divisions as $div) {
            $acceptedCount = Application::where('division_id', $div->id)->where('status', 'accepted')->count();
            $quotas[$div->id] = [
                'name' => $div->name,
                'quota' => $div->quota,
                'accepted' => $acceptedCount,
                'remaining' => max(0, $div->quota - $acceptedCount),
            ];
        }

        return view('pic.applications.index', [
            'team' => $team,
            'catalog' => $catalog,
            'applications' => $applications,
            'quotas' => $quotas,
            'selectedDivision' => $divisionId,
            'selectedStatus' => $status,
        ]);
    }

    public function accept(int $applicationId, ApplicationSelectionService $selectionService): RedirectResponse
    {
        try {
            $selectionService->acceptApplication($applicationId, Auth::id());
            return back()->with('success', 'Kandidat berhasil diterima. Seluruh lamaran pending kandidat di tim lain telah otomatis dibatalkan.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, int $applicationId, ApplicationSelectionService $selectionService): RedirectResponse
    {
        $notes = $request->input('notes');

        try {
            $selectionService->rejectApplication($applicationId, Auth::id(), $notes);
            return back()->with('success', 'Lamaran kandidat telah ditolak.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
