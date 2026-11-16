<?php

namespace Tests\Feature\Application;

use App\Models\Application;
use App\Models\Division;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Models\User;
use App\Services\ApplicationSelectionService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationSubmissionAndSelectionTest extends TestCase
{
    use DatabaseTransactions;

    private User $student;
    private User $pic1;
    private KknTeam $team1;
    private OprecCatalog $catalog1;
    private Division $division1;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('filesystems.default'));

        $this->student = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa.pelamar@ugm.ac.id',
            'name' => 'Budi Santoso',
            'nim' => '21/471111/TK/52111',
            'faculty' => 'Fakultas Teknik',
            'major' => 'Teknologi Informasi',
        ]);

        $this->pic1 = User::factory()->create([
            'role' => 'pic',
            'email' => 'pic.team1@ugm.ac.id',
        ]);

        $this->team1 = KknTeam::create([
            'name' => 'KKN PPM Sleman Bersih',
            'period' => '2026',
            'target_location' => 'Sleman, DIY',
            'pic_user_id' => $this->pic1->id,
        ]);

        $this->catalog1 = OprecCatalog::create([
            'team_id' => $this->team1->id,
            'title' => 'OPREC Tim Sleman 2026',
            'status' => 'published',
            'deadline' => Carbon::now()->addDays(5),
        ]);

        $this->division1 = Division::create([
            'catalog_id' => $this->catalog1->id,
            'name' => 'Divisi Humas',
            'quota' => 1, // Kuota dibatasi 1 orang untuk uji over-quota
        ]);
    }

    public function test_student_can_submit_application_with_valid_pdf_documents(): void
    {
        $cvFile = UploadedFile::fake()->create('curriculum_vitae.pdf', 1024, 'application/pdf');
        $portfolioFile = UploadedFile::fake()->create('portfolio.pdf', 2048, 'application/pdf');

        $response = $this->actingAs($this->student)->post(route('applications.store', $this->catalog1->id), [
            'division_id' => $this->division1->id,
            'cv_file' => $cvFile,
            'portfolio_file' => $portfolioFile,
            'notes' => 'Sangat berminat pada program pemberdayaan masyarakat Sleman.',
        ]);

        $response->assertRedirect(route('applications.history'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'user_id' => $this->student->id,
            'team_id' => $this->team1->id,
            'division_id' => $this->division1->id,
            'status' => 'pending',
        ]);

        $application = Application::where('user_id', $this->student->id)
            ->where('team_id', $this->team1->id)
            ->first();

        $this->assertNotNull($application);
        $this->assertDatabaseHas('application_documents', [
            'application_id' => $application->id,
            'document_type' => 'cv',
            'original_filename' => 'curriculum_vitae.pdf',
        ]);

        $this->assertDatabaseHas('application_documents', [
            'application_id' => $application->id,
            'document_type' => 'portfolio',
            'original_filename' => 'portfolio.pdf',
        ]);
    }

    public function test_application_fails_without_mandatory_cv(): void
    {
        $response = $this->actingAs($this->student)->post(route('applications.store', $this->catalog1->id), [
            'division_id' => $this->division1->id,
        ]);

        $response->assertSessionHasErrors('cv_file');
    }

    public function test_application_fails_if_cv_is_not_pdf(): void
    {
        $docxFile = UploadedFile::fake()->create('cv.docx', 1000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->student)->post(route('applications.store', $this->catalog1->id), [
            'division_id' => $this->division1->id,
            'cv_file' => $docxFile,
        ]);

        $response->assertSessionHasErrors('cv_file');
    }

    public function test_application_fails_if_cv_exceeds_5mb(): void
    {
        // 6 MB = 6144 KB (Batas FR-11 adalah 5 MB / 5120 KB)
        $largeCv = UploadedFile::fake()->create('large_cv.pdf', 6144, 'application/pdf');

        $response = $this->actingAs($this->student)->post(route('applications.store', $this->catalog1->id), [
            'division_id' => $this->division1->id,
            'cv_file' => $largeCv,
        ]);

        $response->assertSessionHasErrors('cv_file');
    }

    public function test_application_fails_if_portfolio_exceeds_10mb(): void
    {
        $validCv = UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf');
        // 12 MB = 12288 KB (Batas FR-11 adalah 10 MB / 10240 KB)
        $largePortfolio = UploadedFile::fake()->create('large_portfolio.pdf', 12288, 'application/pdf');

        $response = $this->actingAs($this->student)->post(route('applications.store', $this->catalog1->id), [
            'division_id' => $this->division1->id,
            'cv_file' => $validCv,
            'portfolio_file' => $largePortfolio,
        ]);

        $response->assertSessionHasErrors('portfolio_file');
    }

    public function test_prevent_duplicate_application_to_same_team_fr12(): void
    {
        // 1. Buat lamaran pertama
        Application::create([
            'user_id' => $this->student->id,
            'team_id' => $this->team1->id,
            'division_id' => $this->division1->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Buat divisi kedua pada tim yang sama
        $division2 = Division::create([
            'catalog_id' => $this->catalog1->id,
            'name' => 'Divisi Acara',
            'quota' => 2,
        ]);

        $cvFile = UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf');

        // 2. Ajukan lamaran kedua ke tim yang sama (berbeda divisi)
        $response = $this->actingAs($this->student)->post(route('applications.store', $this->catalog1->id), [
            'division_id' => $division2->id,
            'cv_file' => $cvFile,
        ]);

        // Harus ditolak sesuai FR-12
        $response->assertSessionHasErrors('division_id');
    }

    public function test_student_can_cancel_pending_application(): void
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'team_id' => $this->team1->id,
            'division_id' => $this->division1->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->student)->post(route('applications.cancel', $application->id));

        $response->assertSessionHas('success');
        $application->refresh();
        $this->assertEquals('cancelled', $application->status);
    }

    public function test_pic_can_accept_applicant_within_quota(): void
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'team_id' => $this->team1->id,
            'division_id' => $this->division1->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->pic1)->post(route('pic.applications.accept', $application->id));

        $response->assertSessionHas('success');
        $application->refresh();
        $this->assertEquals('accepted', $application->status);
        $this->assertNotNull($application->decided_at);
    }

    public function test_pessimistic_lock_prevents_over_quota_acceptance(): void
    {
        // Kuota divisi1 adalah 1
        // Terima kandidat pertama
        $app1 = Application::create([
            'user_id' => $this->student->id,
            'team_id' => $this->team1->id,
            'division_id' => $this->division1->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $service = app(ApplicationSelectionService::class);
        $service->acceptApplication($app1->id, $this->pic1->id);

        $app1->refresh();
        $this->assertEquals('accepted', $app1->status);

        // Kandidat kedua mencoba diterima ke divisi yang kuotanya sudah penuh
        $student2 = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'student2@ugm.ac.id',
        ]);

        $app2 = Application::create([
            'user_id' => $student2->id,
            'team_id' => $this->team1->id,
            'division_id' => $this->division1->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Percobaan menerima app2 harus gagal karena kuota divisi1 (1) sudah habis
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Kuota untuk divisi Divisi Humas telah penuh');

        $service->acceptApplication($app2->id, $this->pic1->id);
    }

    public function test_cascade_auto_cancellation_when_accepted_in_one_team_fr13(): void
    {
        // Setup 3 Tim KKN berbeda
        $pic2 = User::factory()->create(['role' => 'pic', 'email' => 'pic2@ugm.ac.id']);
        $team2 = KknTeam::create([
            'name' => 'KKN Kulon Progo Hebat',
            'period' => '2026',
            'target_location' => 'Kulon Progo',
            'pic_user_id' => $pic2->id,
        ]);
        $catalog2 = OprecCatalog::create(['team_id' => $team2->id, 'title' => 'OPREC 2', 'status' => 'published', 'deadline' => Carbon::now()->addDays(5)]);
        $division2 = Division::create(['catalog_id' => $catalog2->id, 'name' => 'Divisi Acara', 'quota' => 2]);

        $pic3 = User::factory()->create(['role' => 'pic', 'email' => 'pic3@ugm.ac.id']);
        $team3 = KknTeam::create([
            'name' => 'KKN Bantul Berdaya',
            'period' => '2026',
            'target_location' => 'Bantul',
            'pic_user_id' => $pic3->id,
        ]);
        $catalog3 = OprecCatalog::create(['team_id' => $team3->id, 'title' => 'OPREC 3', 'status' => 'published', 'deadline' => Carbon::now()->addDays(5)]);
        $division3 = Division::create(['catalog_id' => $catalog3->id, 'name' => 'Divisi Logistik', 'quota' => 2]);

        // Mahasiswa melamar ke 3 tim berbeda (status semuanya pending)
        $appTeam1 = Application::create([
            'user_id' => $this->student->id,
            'team_id' => $this->team1->id,
            'division_id' => $this->division1->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $appTeam2 = Application::create([
            'user_id' => $this->student->id,
            'team_id' => $team2->id,
            'division_id' => $division2->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $appTeam3 = Application::create([
            'user_id' => $this->student->id,
            'team_id' => $team3->id,
            'division_id' => $division3->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // PIC Tim 1 menyetujui pelamar
        $response = $this->actingAs($this->pic1)->post(route('pic.applications.accept', $appTeam1->id));
        $response->assertSessionHas('success');

        // Verifikasi hasil:
        // 1. Lamaran pada Tim 1 berstatus ACCEPTED
        $appTeam1->refresh();
        $this->assertEquals('accepted', $appTeam1->status);

        // 2. Cascade Auto-Cancellation (FR-13): Lamaran pada Tim 2 dan Tim 3 seketika menjadi CANCELLED
        $appTeam2->refresh();
        $this->assertEquals('cancelled', $appTeam2->status);
        $this->assertStringContainsString('Otomatis dibatalkan oleh sistem', $appTeam2->notes);

        $appTeam3->refresh();
        $this->assertEquals('cancelled', $appTeam3->status);
        $this->assertStringContainsString('Otomatis dibatalkan oleh sistem', $appTeam3->notes);
    }
}
