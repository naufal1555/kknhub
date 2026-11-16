<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KknTeam;
use App\Models\PicRegistration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPicVerificationController extends Controller
{
    public function index(): View
    {
        $registrations = PicRegistration::with(['user', 'verifiedBy', 'team'])
            ->latest()
            ->paginate(15);

        return view('admin.pic_registrations.index', [
            'registrations' => $registrations,
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $registration = PicRegistration::findOrFail($id);

        if ($registration->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($registration) {
            // 1. Buat tim KKN resmi dengan PIC utama user pemohon
            $team = KknTeam::create([
                'name' => $registration->proposed_team_name,
                'period' => $registration->period,
                'target_location' => $registration->target_location,
                'description' => $registration->description,
                'pic_user_id' => $registration->user_id,
            ]);

            // 2. Update status registrasi
            $registration->update([
                'status' => 'approved',
                'team_id' => $team->id,
                'verified_by_admin_id' => Auth::id(),
            ]);

            // 3. Naikkan role pengguna menjadi PIC
            $user = User::findOrFail($registration->user_id);
            $user->update(['role' => 'pic']);
        });

        return back()->with('success', "Registrasi PIC untuk tim {$registration->proposed_team_name} berhasil disetujui.");
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan registrasi PIC wajib diisi.',
        ]);

        $registration = PicRegistration::findOrFail($id);

        if ($registration->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
            'verified_by_admin_id' => Auth::id(),
        ]);

        return back()->with('success', "Registrasi PIC telah ditolak.");
    }
}
