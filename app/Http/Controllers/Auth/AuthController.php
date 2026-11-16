<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\PicRegistration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->isPic()) {
                return redirect()->intended(route('pic.dashboard'));
            }

            return redirect()->intended(route('catalog.index'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isPicRegistration = ($validated['registration_type'] ?? 'mahasiswa') === 'pic';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa', // Mulai sebagai mahasiswa sambil menunggu approval PIC oleh administrator
            'nim' => $validated['nim'] ?? null,
            'faculty' => $validated['faculty'] ?? null,
            'major' => $validated['major'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        Auth::login($user);

        if ($isPicRegistration) {
            PicRegistration::create([
                'user_id' => $user->id,
                'proposed_team_name' => $validated['proposed_team_name'],
                'target_location' => $validated['target_location'],
                'period' => $validated['period'],
                'description' => $validated['team_description'] ?? null,
                'status' => 'pending',
            ]);

            return redirect()->route('applications.history')
                ->with('success', "Pendaftaran akun dan pengajuan Tim KKN '{$validated['proposed_team_name']}' berhasil! Tim Anda sedang dalam antrean verifikasi Administrator DPkM.");
        }

        return redirect()->route('catalog.index')->with('success', 'Pendaftaran akun mahasiswa berhasil!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari akun.');
    }
}
