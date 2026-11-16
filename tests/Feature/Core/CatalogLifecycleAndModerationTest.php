<?php

namespace Tests\Feature\Core;

use App\Models\Division;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CatalogLifecycleAndModerationTest extends TestCase
{
    use DatabaseTransactions;

    private User $pic;
    private KknTeam $team;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pic = User::factory()->create([
            'role' => 'pic',
            'email' => 'pic.katalog@ugm.ac.id',
        ]);

        $this->team = KknTeam::create([
            'name' => 'KKN Tim Uji Siklus',
            'period' => '2026',
            'target_location' => 'Sleman, DIY',
            'pic_user_id' => $this->pic->id,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.moderator@ugm.ac.id',
        ]);
    }

    public function test_pic_can_create_draft_catalog(): void
    {
        $deadline = Carbon::now()->addDays(7)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->pic)->post(route('pic.catalogs.store'), [
            'title' => 'OPREC Tim KKN Sleman 2026',
            'description' => 'Rekrutmen mahasiswa KKN untuk divisi humas, acara, dan logistik.',
            'start_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'deadline' => $deadline,
        ]);

        $this->assertDatabaseHas('oprec_catalogs', [
            'team_id' => $this->team->id,
            'title' => 'OPREC Tim KKN Sleman 2026',
            'status' => 'draft',
        ]);

        $catalog = OprecCatalog::where('title', 'OPREC Tim KKN Sleman 2026')->first();
        $response->assertRedirect(route('pic.catalogs.show', $catalog->id));
    }

    public function test_pic_cannot_submit_approval_without_any_divisions(): void
    {
        $catalog = OprecCatalog::create([
            'team_id' => $this->team->id,
            'title' => 'Katalog Kosong Divisi',
            'description' => 'Deskripsi',
            'status' => 'draft',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        $response = $this->actingAs($this->pic)->post(route('pic.catalogs.submit-approval', $catalog->id));

        $response->assertSessionHasErrors('divisions');
        $catalog->refresh();
        $this->assertEquals('draft', $catalog->status);
    }

    public function test_pic_can_submit_approval_with_divisions(): void
    {
        $catalog = OprecCatalog::create([
            'team_id' => $this->team->id,
            'title' => 'Katalog Dengan Divisi',
            'description' => 'Deskripsi',
            'status' => 'draft',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        Division::create([
            'catalog_id' => $catalog->id,
            'name' => 'Divisi Acara',
            'quota' => 5,
        ]);

        $response = $this->actingAs($this->pic)->post(route('pic.catalogs.submit-approval', $catalog->id));

        $response->assertSessionHas('success');
        $catalog->refresh();
        $this->assertEquals('pending_approval', $catalog->status);
    }

    public function test_admin_can_approve_catalog(): void
    {
        $catalog = OprecCatalog::create([
            'team_id' => $this->team->id,
            'title' => 'Katalog Siap Publikasi',
            'status' => 'pending_approval',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        Division::create([
            'catalog_id' => $catalog->id,
            'name' => 'Divisi Media',
            'quota' => 3,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.catalogs.approve', $catalog->id));

        $response->assertSessionHas('success');
        $catalog->refresh();
        $this->assertEquals('published', $catalog->status);
    }

    public function test_admin_can_reject_catalog_with_rejection_note(): void
    {
        $catalog = OprecCatalog::create([
            'team_id' => $this->team->id,
            'title' => 'Katalog Ditolak',
            'status' => 'pending_approval',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.catalogs.reject', $catalog->id), [
            'rejection_note' => 'Kuota tidak realistis dan deskripsi tidak jelas.',
        ]);

        $response->assertSessionHas('success');
        $catalog->refresh();
        $this->assertEquals('rejected', $catalog->status);
        $this->assertEquals('Kuota tidak realistis dan deskripsi tidak jelas.', $catalog->rejection_note);
    }

    public function test_editing_published_catalog_reverts_status_to_pending_approval(): void
    {
        // Aturan FR-09: Perubahan katalog berstatus 'published' otomatis kembali ke 'pending_approval'
        $catalog = OprecCatalog::create([
            'team_id' => $this->team->id,
            'title' => 'Katalog Aktif',
            'description' => 'Deskripsi Awal',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        $newDeadline = Carbon::now()->addDays(10)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->pic)->put(route('pic.catalogs.update', $catalog->id), [
            'title' => 'Katalog Aktif Telah Direvisi',
            'description' => 'Deskripsi yang telah dimutakhirkan',
            'deadline' => $newDeadline,
        ]);

        $response->assertRedirect(route('pic.catalogs.show', $catalog->id));
        $catalog->refresh();
        $this->assertEquals('pending_approval', $catalog->status);
        $this->assertEquals('Katalog Aktif Telah Direvisi', $catalog->title);
    }

    public function test_pic_can_manually_close_published_catalog(): void
    {
        $catalog = OprecCatalog::create([
            'team_id' => $this->team->id,
            'title' => 'Katalog Akan Ditutup',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        $response = $this->actingAs($this->pic)->post(route('pic.catalogs.close', $catalog->id));

        $response->assertSessionHas('success');
        $catalog->refresh();
        $this->assertEquals('closed', $catalog->status);
    }
}
