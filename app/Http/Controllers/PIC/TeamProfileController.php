<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use App\Models\ContactPerson;
use App\Models\KknTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamProfileController extends Controller
{
    protected function getPicTeam(): KknTeam
    {
        $team = KknTeam::where('pic_user_id', Auth::id())->first();

        if (!$team) {
            abort(404, 'Data tim KKN tidak ditemukan untuk akun PIC ini.');
        }

        return $team;
    }

    public function edit(): View
    {
        $team = $this->getPicTeam();
        $team->load('contactPersons');

        return view('pic.team.edit', [
            'team' => $team,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $team = $this->getPicTeam();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_location' => ['required', 'string', 'max:255'],
            'period' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:3000'],
        ]);

        $team->update($validated);

        return back()->with('success', 'Profil tim KKN berhasil diperbarui.');
    }

    public function storeContactPerson(Request $request): RedirectResponse
    {
        $team = $this->getPicTeam();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role_title' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:25'],
            'line_id' => ['nullable', 'string', 'max:100'],
        ]);

        $team->contactPersons()->create($validated);

        return back()->with('success', 'Contact person berhasil ditambahkan.');
    }

    public function destroyContactPerson(int $id): RedirectResponse
    {
        $team = $this->getPicTeam();
        $contact = $team->contactPersons()->findOrFail($id);
        $contact->delete();

        return back()->with('success', 'Contact person berhasil dihapus.');
    }
}
