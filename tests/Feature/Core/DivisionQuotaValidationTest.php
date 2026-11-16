<?php

namespace Tests\Feature\Core;

use App\Models\Division;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DivisionQuotaValidationTest extends TestCase
{
    use DatabaseTransactions;

    private User $pic;
    private KknTeam $team;
    private OprecCatalog $catalog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pic = User::factory()->create([
            'role' => 'pic',
            'email' => 'pic.divisi@ugm.ac.id',
        ]);

        $this->team = KknTeam::create([
            'name' => 'KKN Tim Divisi',
            'period' => '2026',
            'target_location' => 'Bantul, DIY',
            'pic_user_id' => $this->pic->id,
        ]);

        $this->catalog = OprecCatalog::create([
            'team_id' => $this->team->id,
            'title' => 'Katalog Uji Divisi',
            'status' => 'draft',
            'deadline' => Carbon::now()->addDays(5),
        ]);
    }

    public function test_division_quota_valid_succeeds(): void
    {
        $response = $this->actingAs($this->pic)->post(route('pic.divisions.store', $this->catalog->id), [
            'name' => 'Divisi Publikasi dan Dokumentasi',
            'job_description' => 'Mengelola media sosial dan dokumentasi foto/video kegiatan.',
            'requirements' => 'Menguasai kamera dan software editing.',
            'quota' => 4,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('divisions', [
            'catalog_id' => $this->catalog->id,
            'name' => 'Divisi Publikasi dan Dokumentasi',
            'quota' => 4,
        ]);
    }

    public function test_division_quota_zero_or_negative_fails_validation(): void
    {
        // Uji kuota bernilai 0 (FR-07: Kuota tidak boleh bernilai negatif atau nol)
        $responseZero = $this->actingAs($this->pic)->post(route('pic.divisions.store', $this->catalog->id), [
            'name' => 'Divisi Nol',
            'quota' => 0,
        ]);
        $responseZero->assertSessionHasErrors('quota');

        // Uji kuota bernilai negatif -5
        $responseNegative = $this->actingAs($this->pic)->post(route('pic.divisions.store', $this->catalog->id), [
            'name' => 'Divisi Minus',
            'quota' => -5,
        ]);
        $responseNegative->assertSessionHasErrors('quota');

        // Uji kuota non-integer
        $responseString = $this->actingAs($this->pic)->post(route('pic.divisions.store', $this->catalog->id), [
            'name' => 'Divisi String',
            'quota' => 'banyak',
        ]);
        $responseString->assertSessionHasErrors('quota');
    }

    public function test_division_cannot_be_deleted_if_has_applications(): void
    {
        $division = Division::create([
            'catalog_id' => $this->catalog->id,
            'name' => 'Divisi Perlengkapan',
            'quota' => 3,
        ]);

        $applicant = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'pelamar.divisi@ugm.ac.id',
        ]);

        // Simulasikan ada lamaran masuk
        $division->applications()->create([
            'user_id' => $applicant->id,
            'team_id' => $this->team->id,
            'status' => 'pending',
            'submitted_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->pic)->delete(route('pic.divisions.destroy', [
            'catalogId' => $this->catalog->id,
            'divisionId' => $division->id,
        ]));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('divisions', [
            'id' => $division->id,
        ]);
    }
}
