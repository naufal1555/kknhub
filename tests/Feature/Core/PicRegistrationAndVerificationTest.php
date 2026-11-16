<?php

namespace Tests\Feature\Core;

use App\Models\PicRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PicRegistrationAndVerificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_mahasiswa_can_submit_pic_registration_with_pending_status(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'pemohon.kkn.baru@ugm.ac.id',
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('pic.register.post'), [
            'proposed_team_name' => 'KKN-PPM Bantul Lestari',
            'target_location' => 'Desa Sriharjo, Imogiri, Bantul, D.I. Yogyakarta',
            'period' => 'Antar Semester 2026',
            'description' => 'Pemberdayaan UMKM lokal dan mitigasi bencana banjir.',
        ]);

        $response->assertRedirect(route('catalog.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pic_registrations', [
            'user_id' => $mahasiswa->id,
            'proposed_team_name' => 'KKN-PPM Bantul Lestari',
            'status' => 'pending',
        ]);
    }

    public function test_pic_registration_requires_mandatory_fields(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mhs.invalid@ugm.ac.id',
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('pic.register.post'), [
            'proposed_team_name' => '',
            'target_location' => '',
            'period' => '',
        ]);

        $response->assertSessionHasErrors(['proposed_team_name', 'target_location', 'period']);
    }

    public function test_admin_can_approve_pic_registration_and_creates_team_and_upgrades_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'superadmin@ugm.ac.id',
        ]);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'pemohon.pic@ugm.ac.id',
        ]);

        $registration = PicRegistration::create([
            'user_id' => $mahasiswa->id,
            'proposed_team_name' => 'KKN Kulon Progo Sejahtera',
            'target_location' => 'Girimulyo, Kulon Progo',
            'period' => 'Periode 2 Tahun 2026',
            'description' => 'Agrowisata kopi dan teh.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.pic-registrations.approve', $registration->id));

        $response->assertSessionHas('success');

        // Verifikasi status pendaftaran berubah menjadi approved
        $registration->refresh();
        $this->assertEquals('approved', $registration->status);
        $this->assertEquals($admin->id, $registration->verified_by_admin_id);
        $this->assertNotNull($registration->team_id);

        // Verifikasi entitas KknTeam berhasil dibuat
        $this->assertDatabaseHas('kkn_teams', [
            'id' => $registration->team_id,
            'name' => 'KKN Kulon Progo Sejahtera',
            'pic_user_id' => $mahasiswa->id,
        ]);

        // Verifikasi role pemohon naik menjadi PIC
        $mahasiswa->refresh();
        $this->assertEquals('pic', $mahasiswa->role);
    }

    public function test_admin_can_reject_pic_registration_with_reason(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'moderator@ugm.ac.id',
        ]);

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'ditolak@ugm.ac.id',
        ]);

        $registration = PicRegistration::create([
            'user_id' => $mahasiswa->id,
            'proposed_team_name' => 'KKN Belum Jelas',
            'target_location' => 'DIY',
            'period' => '2026',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.pic-registrations.reject', $registration->id), [
            'rejection_reason' => 'Dokumen proposal kegiatan belum lengkap dan lokasi belum spesifik.',
        ]);

        $response->assertSessionHas('success');

        $registration->refresh();
        $this->assertEquals('rejected', $registration->status);
        $this->assertEquals('Dokumen proposal kegiatan belum lengkap dan lokasi belum spesifik.', $registration->rejection_reason);

        // Role pemohon tetap mahasiswa
        $mahasiswa->refresh();
        $this->assertEquals('mahasiswa', $mahasiswa->role);
    }

    public function test_non_admin_cannot_approve_or_reject_pic_registration(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'regular@ugm.ac.id',
        ]);

        $registration = PicRegistration::create([
            'user_id' => $mahasiswa->id,
            'proposed_team_name' => 'KKN Gunungkidul Berdaya',
            'target_location' => 'Tepus, Gunungkidul',
            'period' => '2026',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('admin.pic-registrations.approve', $registration->id));
        $response->assertStatus(403);

        $registration->refresh();
        $this->assertEquals('pending', $registration->status);
    }
}
