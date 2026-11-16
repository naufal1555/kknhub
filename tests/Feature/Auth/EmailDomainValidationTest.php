<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailDomainValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_fails_for_non_ugm_email_domains(): void
    {
        $invalidEmails = [
            'mahasiswa@gmail.com',
            'mahasiswa@yahoo.com',
            'mahasiswa@mail.ugm.ac.id', // PRD Section 4.1 & FR-01: domain tepat @ugm.ac.id
            'mahasiswa@ugm.com',
            'mahasiswa@ugm.ac.id.attacker.com',
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->post(route('register.post'), [
                'name' => 'Test User',
                'email' => $email,
                'password' => 'secret1234',
                'password_confirmation' => 'secret1234',
            ]);

            $response->assertSessionHasErrors('email');
            $this->assertDatabaseMissing('users', ['email' => $email]);
        }
    }

    public function test_registration_succeeds_for_valid_ugm_email_domain(): void
    {
        $validEmail = 'fathin.naufal@ugm.ac.id';

        $response = $this->post(route('register.post'), [
            'name' => 'Ahmad Fathin Naufal',
            'email' => $validEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nim' => '21/470000/TK/52000',
            'faculty' => 'Fakultas Teknik',
            'major' => 'Teknologi Informasi',
        ]);

        $response->assertRedirect(route('catalog.index'));
        $this->assertDatabaseHas('users', [
            'email' => $validEmail,
            'role' => 'mahasiswa',
        ]);

        // Verifikasi kata sandi di-hash
        $user = User::where('email', $validEmail)->first();
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_login_rejects_non_ugm_domain(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'attacker@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_as_pic_from_any_region_creates_pending_pic_registration(): void
    {
        $email = 'pic.flores@ugm.ac.id';

        $response = $this->post(route('register.post'), [
            'registration_type' => 'pic',
            'name' => 'Maria Flores',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nim' => '21/480000/GE/53000',
            'faculty' => 'Fakultas Geografi',
            'major' => 'Pembangunan Wilayah',
            'phone_number' => '081234567899',
            'proposed_team_name' => 'KKN-PPM Unit Flores Timur 2026',
            'target_location' => 'Larantuka, Flores Timur, NTT',
            'period' => 'Antar Semester 2026',
            'team_description' => 'Program ekowisata bahari dan mitigasi kekeringan pesisir Flores Timur.',
        ]);

        $response->assertRedirect(route('applications.history'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'mahasiswa',
        ]);

        $user = User::where('email', $email)->first();
        $this->assertDatabaseHas('pic_registrations', [
            'user_id' => $user->id,
            'proposed_team_name' => 'KKN-PPM Unit Flores Timur 2026',
            'target_location' => 'Larantuka, Flores Timur, NTT',
            'status' => 'pending',
        ]);
    }

    public function test_admin_approving_pic_registration_upgrades_role_and_creates_team(): void
    {
        $admin = User::create([
            'name' => 'Admin Testing',
            'email' => 'admin.test@ugm.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $applicant = User::create([
            'name' => 'Bintang Raja',
            'email' => 'bintang.raja@ugm.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $reg = \App\Models\PicRegistration::create([
            'user_id' => $applicant->id,
            'proposed_team_name' => 'KKN-PPM Raja Ampat Lestari',
            'target_location' => 'Misool, Raja Ampat, Papua Barat Daya',
            'period' => 'Antar Semester 2026',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.pic-registrations.approve', $reg->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pic_registrations', [
            'id' => $reg->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('kkn_teams', [
            'pic_user_id' => $applicant->id,
            'name' => 'KKN-PPM Raja Ampat Lestari',
            'target_location' => 'Misool, Raja Ampat, Papua Barat Daya',
        ]);

        $applicant->refresh();
        $this->assertEquals('pic', $applicant->role);
    }
}
