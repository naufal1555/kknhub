<?php

namespace App\Http\Controllers\PIC;

use App\Http\Controllers\Controller;
use App\Http\Requests\PIC\RegisterPicRequest;
use App\Models\PicRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PicRegistrationController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isPic()) {
            return redirect()->route('pic.dashboard')->with('info', 'Anda sudah terdaftar sebagai PIC tim KKN.');
        }

        $existingPending = PicRegistration::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        return view('pic.register', [
            'existingPending' => $existingPending,
        ]);
    }

    public function store(RegisterPicRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isPic()) {
            return redirect()->route('pic.dashboard');
        }

        $hasPending = PicRegistration::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->withErrors([
                'proposed_team_name' => 'Anda masih memiliki pengajuan PIC yang sedang menunggu verifikasi administrator.',
            ]);
        }

        $validated = $request->validated();

        PicRegistration::create([
            'user_id' => $user->id,
            'proposed_team_name' => $validated['proposed_team_name'],
            'target_location' => $validated['target_location'],
            'period' => $validated['period'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('catalog.index')->with('success', 'Pengajuan registrasi PIC berhasil dikirim. Menunggu verifikasi administrator.');
    }
}
