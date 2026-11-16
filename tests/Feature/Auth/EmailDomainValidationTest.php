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
}
