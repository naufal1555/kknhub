<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Division;
use App\Models\KknTeam;
use DomainException;
use Illuminate\Support\Facades\DB;

class ApplicationSelectionService
{
    /**
     * Menerima pelamar ke divisi dengan pessimistic locking dan pencegahan over-quota.
     * Menerapkan Cascade Auto-Cancellation pada seluruh lamaran pending mahasiswa di tim lain.
     *
     * @param int $applicationId
     * @param int $picUserId
     * @return Application
     * @throws DomainException
     */
    public function acceptApplication(int $applicationId, int $picUserId): Application
    {
        return DB::transaction(function () use ($applicationId, $picUserId) {
            // 1. Lock record lamaran untuk pembaruan transaksional
            $application = Application::where('id', $applicationId)->lockForUpdate()->firstOrFail();

            // 2. Verifikasi otorisasi: pastikan PIC berhak atas tim terkait
            $team = KknTeam::where('id', $application->team_id)
                ->where('pic_user_id', $picUserId)
                ->first();

            if (!$team) {
                throw new DomainException("Anda tidak memiliki hak otorisasi untuk mengelola seleksi tim ini.");
            }

            // 3. Validasi status saat ini
            if ($application->status !== 'pending') {
                throw new DomainException("Hanya lamaran berstatus Menunggu Seleksi (Pending) yang dapat diterima.");
            }

            // 4. Pessimistic lock pada divisi untuk mengunci kuota
            $division = Division::where('id', $application->division_id)->lockForUpdate()->firstOrFail();

            // 5. Hitung jumlah kandidat yang telah berstatus accepted pada divisi ini
            $acceptedCount = Application::where('division_id', $division->id)
                ->where('status', 'accepted')
                ->lockForUpdate()
                ->count();

            // 6. Pencegahan Over-Quota (FR-07 & Section 3.2 PRD)
            if ($acceptedCount >= $division->quota) {
                throw new DomainException("Kuota untuk divisi {$division->name} telah penuh ({$division->quota}/{$division->quota}).");
            }

            // 7. Terima kandidat
            $application->update([
                'status' => 'accepted',
                'decided_at' => now(),
            ]);

            // 8. CASCADE AUTO-CANCELLATION (FR-13 & Section 3.2 PRD):
            // Batalkan seluruh lamaran pending milik mahasiswa ini pada tim manapun
            Application::where('user_id', $application->user_id)
                ->where('id', '!=', $application->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                    'decided_at' => now(),
                    'notes' => 'Otomatis dibatalkan oleh sistem karena telah diterima pada tim ' . $team->name,
                ]);

            return $application;
        });
    }

    /**
     * Menolak pelamar
     */
    public function rejectApplication(int $applicationId, int $picUserId, ?string $notes = null): Application
    {
        return DB::transaction(function () use ($applicationId, $picUserId, $notes) {
            $application = Application::where('id', $applicationId)->lockForUpdate()->firstOrFail();

            $team = KknTeam::where('id', $application->team_id)
                ->where('pic_user_id', $picUserId)
                ->first();

            if (!$team) {
                throw new DomainException("Anda tidak memiliki hak otorisasi untuk mengelola seleksi tim ini.");
            }

            if ($application->status !== 'pending') {
                throw new DomainException("Hanya lamaran berstatus Menunggu Seleksi (Pending) yang dapat ditolak.");
            }

            $application->update([
                'status' => 'rejected',
                'decided_at' => now(),
                'notes' => $notes,
            ]);

            return $application;
        });
    }

    /**
     * Pembatalan sukarela oleh mahasiswa pelamar
     */
    public function cancelByStudent(int $applicationId, int $studentUserId): Application
    {
        return DB::transaction(function () use ($applicationId, $studentUserId) {
            $application = Application::where('id', $applicationId)
                ->where('user_id', $studentUserId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($application->status !== 'pending') {
                throw new DomainException("Hanya lamaran berstatus Pending yang dapat dibatalkan.");
            }

            $application->update([
                'status' => 'cancelled',
                'decided_at' => now(),
                'notes' => 'Dibatalkan oleh mahasiswa pemohon.',
            ]);

            return $application;
        });
    }
}
