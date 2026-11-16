<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login_when_accessing_protected_routes(): void
    {
        $response = $this->get('/my-applications');
        $response->assertRedirect(route('login'));

        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect(route('login'));

        $responsePic = $this->get('/pic/dashboard');
        $responsePic->assertRedirect(route('login'));
    }

    public function test_mahasiswa_cannot_access_admin_or_pic_routes(): void
    {
        $mahasiswa = User::create([
            'name' => 'Mahasiswa User',
            'email' => 'mhs@ugm.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $this->actingAs($mahasiswa)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($mahasiswa)
            ->get('/pic/dashboard')
            ->assertStatus(403);
    }

    public function test_pic_can_access_pic_dashboard_but_not_admin(): void
    {
        $pic = User::factory()->create([
            'email' => 'pic.rbac@ugm.ac.id',
            'role' => 'pic',
        ]);

        \App\Models\KknTeam::create([
            'name' => 'KKN Tim RBAC',
            'period' => '2026',
            'target_location' => 'Sleman',
            'pic_user_id' => $pic->id,
        ]);

        $this->actingAs($pic)
            ->get('/pic/dashboard')
            ->assertStatus(200);

        $this->actingAs($pic)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin.rbac@ugm.ac.id',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertStatus(200);
    }
}
