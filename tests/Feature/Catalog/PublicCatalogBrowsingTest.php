<?php

namespace Tests\Feature\Catalog;

use App\Models\Division;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicCatalogBrowsingTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mhs.browser@ugm.ac.id',
        ]);
    }

    public function test_only_published_and_non_expired_catalogs_are_visible(): void
    {
        $pic = User::factory()->create(['role' => 'pic', 'email' => 'pic.browse@ugm.ac.id']);
        $team = KknTeam::create(['name' => 'KKN Tim Browse', 'period' => '2026', 'target_location' => 'Sleman', 'pic_user_id' => $pic->id]);

        // 1. Published & aktif
        $activeCatalog = OprecCatalog::create([
            'team_id' => $team->id,
            'title' => 'Katalog Aktif Tampil',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(3),
        ]);

        // 2. Published tapi sudah kadaluarsa
        $expiredCatalog = OprecCatalog::create([
            'team_id' => $team->id,
            'title' => 'Katalog Kadaluarsa Sembunyi',
            'status' => 'published',
            'deadline' => Carbon::now()->subDays(1),
        ]);

        // 3. Draft
        $draftCatalog = OprecCatalog::create([
            'team_id' => $team->id,
            'title' => 'Katalog Draft Sembunyi',
            'status' => 'draft',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        $response = $this->actingAs($this->user)->get(route('catalog.index'));

        $response->assertOk();
        $response->assertSee('Katalog Aktif Tampil');
        $response->assertDontSee('Katalog Kadaluarsa Sembunyi');
        $response->assertDontSee('Katalog Draft Sembunyi');
    }

    public function test_search_filter_by_keyword_and_location(): void
    {
        $pic = User::factory()->create(['role' => 'pic', 'email' => 'pic.search@ugm.ac.id']);
        $teamSleman = KknTeam::create(['name' => 'KKN Agro Sleman', 'period' => '2026', 'target_location' => 'Sleman Barat', 'pic_user_id' => $pic->id]);
        $teamBantul = KknTeam::create(['name' => 'KKN Maritim Bantul', 'period' => '2026', 'target_location' => 'Bantul Selatan', 'pic_user_id' => $pic->id]);

        $cat1 = OprecCatalog::create([
            'team_id' => $teamSleman->id,
            'title' => 'Pemberdayaan Tani Merapi',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(3),
        ]);

        $cat2 = OprecCatalog::create([
            'team_id' => $teamBantul->id,
            'title' => 'Konservasi Pantai Parangtritis',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(4),
        ]);

        // Cari berdasarkan keyword 'Merapi'
        $responseKeyword = $this->actingAs($this->user)->get(route('catalog.index', ['q' => 'Merapi']));
        $responseKeyword->assertSee('Pemberdayaan Tani Merapi');
        $responseKeyword->assertDontSee('Konservasi Pantai Parangtritis');

        // Filter berdasarkan lokasi 'Bantul'
        $responseLocation = $this->actingAs($this->user)->get(route('catalog.index', ['location' => 'Bantul']));
        $responseLocation->assertSee('Konservasi Pantai Parangtritis');
        $responseLocation->assertDontSee('Pemberdayaan Tani Merapi');
    }

    public function test_catalog_detail_page_renders_with_team_divisions_and_quota(): void
    {
        $pic = User::factory()->create(['role' => 'pic', 'email' => 'pic.detail@ugm.ac.id']);
        $team = KknTeam::create(['name' => 'KKN Detail Tim', 'period' => '2026', 'target_location' => 'Kulon Progo', 'pic_user_id' => $pic->id]);

        $catalog = OprecCatalog::create([
            'team_id' => $team->id,
            'title' => 'OPREC Terbuka Kulon Progo',
            'description' => 'Program pengentasan stunting dan gizi anak.',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        Division::create([
            'catalog_id' => $catalog->id,
            'name' => 'Divisi Kesehatan',
            'job_description' => 'Edukasi pola hidup bersih dan gizi seimbang.',
            'quota' => 4,
        ]);

        $response = $this->actingAs($this->user)->get(route('catalog.show', $catalog->id));

        $response->assertOk();
        $response->assertSee('OPREC Terbuka Kulon Progo');
        $response->assertSee('KKN Detail Tim');
        $response->assertSee('Divisi Kesehatan');
        $response->assertSee('4');
        $response->assertSee('Formulir Pendaftaran');
    }
}
