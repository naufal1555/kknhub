<?php

namespace Tests\Feature\Core;

use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AutoCloseExpiredCatalogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_artisan_command_closes_only_expired_published_catalogs(): void
    {
        $pic = User::factory()->create([
            'role' => 'pic',
            'email' => 'pic.scheduler@ugm.ac.id',
        ]);

        $team = KknTeam::create([
            'name' => 'KKN Tim Scheduler',
            'period' => '2026',
            'target_location' => 'Gunungkidul',
            'pic_user_id' => $pic->id,
        ]);

        // 1. Katalog Published yang sudah lewat deadline (seharusnya ditutup)
        $expiredCatalog = OprecCatalog::create([
            'team_id' => $team->id,
            'title' => 'Katalog Kadaluarsa',
            'status' => 'published',
            'deadline' => Carbon::now()->subHours(2),
        ]);

        // 2. Katalog Published yang deadline-nya masih di masa depan (tetap published)
        $activeCatalog = OprecCatalog::create([
            'team_id' => $team->id,
            'title' => 'Katalog Masih Berjalan',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(3),
        ]);

        // 3. Katalog Draft yang sudah lewat waktu (tidak boleh diubah menjadi closed)
        $draftCatalog = OprecCatalog::create([
            'team_id' => $team->id,
            'title' => 'Katalog Draft Lampau',
            'status' => 'draft',
            'deadline' => Carbon::now()->subDays(1),
        ]);

        // Jalankan artisan command
        $this->artisan('catalogs:close-expired')
            ->expectsOutputToContain('Berhasil')
            ->assertExitCode(0);

        // Verifikasi status pada database
        $expiredCatalog->refresh();
        $this->assertEquals('closed', $expiredCatalog->status);

        $activeCatalog->refresh();
        $this->assertEquals('published', $activeCatalog->status);

        $draftCatalog->refresh();
        $this->assertEquals('draft', $draftCatalog->status);
    }
}
