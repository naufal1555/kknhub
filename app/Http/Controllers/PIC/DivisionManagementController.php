<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use App\Http\Requests\PIC\StoreDivisionRequest;
use App\Models\Division;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DivisionManagementController extends Controller
{
    protected function getPicCatalog(int $catalogId): OprecCatalog
    {
        $team = KknTeam::where('pic_user_id', Auth::id())->firstOrFail();
        return $team->catalogs()->findOrFail($catalogId);
    }

    public function store(StoreDivisionRequest $request, int $catalogId): RedirectResponse
    {
        $catalog = $this->getPicCatalog($catalogId);

        if ($catalog->status === 'closed') {
            return back()->with('error', 'Tidak dapat menambahkan divisi pada katalog yang telah ditutup.');
        }

        $validated = $request->validated();

        $catalog->divisions()->create([
            'name' => $validated['name'],
            'job_description' => $validated['job_description'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'quota' => $validated['quota'],
        ]);

        return back()->with('success', "Divisi {$validated['name']} dengan kuota {$validated['quota']} berhasil ditambahkan.");
    }

    public function destroy(int $catalogId, int $divisionId): RedirectResponse
    {
        $catalog = $this->getPicCatalog($catalogId);
        $division = $catalog->divisions()->withCount('applications')->findOrFail($divisionId);

        if ($division->applications_count > 0) {
            return back()->with('error', 'Divisi tidak dapat dihapus karena sudah memiliki mahasiswa yang mendaftar.');
        }

        $division->delete();

        return back()->with('success', 'Divisi berhasil dihapus dari katalog.');
    }
}
