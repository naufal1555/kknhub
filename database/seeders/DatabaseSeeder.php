<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ContactPerson;
use App\Models\Division;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Models\PicRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Administrator Default KKNHub
        $admin = User::firstOrCreate(
            ['email' => 'admin@ugm.ac.id'],
            [
                'name' => 'Administrator KKNHub',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'phone_number' => '081234567890',
            ]
        );

        // 2. Akun PIC Default KKNHub (Sleman Barat)
        $pic = User::firstOrCreate(
            ['email' => 'pic.kkn@ugm.ac.id'],
            [
                'name' => 'Fathin Naufal (PIC Sleman Barat)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/470000/TK/52000',
                'faculty' => 'Fakultas Teknik',
                'major' => 'Teknologi Informasi',
                'phone_number' => '081234567891',
            ]
        );

        // 3. Akun Mahasiswa Pendaftar Default KKNHub
        $mahasiswa1 = User::firstOrCreate(
            ['email' => 'mahasiswa@ugm.ac.id'],
            [
                'name' => 'Ahmad Mahasiswa UGM',
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
                'nim' => '22/490000/TK/54000',
                'faculty' => 'Fakultas Teknik',
                'major' => 'Teknik Elektro',
                'phone_number' => '081234567892',
            ]
        );

        $mahasiswa2 = User::firstOrCreate(
            ['email' => 'sarah.mahasiswa@ugm.ac.id'],
            [
                'name' => 'Sarah Nurhaliza',
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
                'nim' => '22/495555/EB/54123',
                'faculty' => 'Fakultas Ekonomika dan Bisnis',
                'major' => 'Manajemen Bisnis',
                'phone_number' => '081234567893',
            ]
        );

        // 4. Tim 1: KKN-PPM Sleman Barat (Published)
        $teamSleman = KknTeam::firstOrCreate(
            ['pic_user_id' => $pic->id],
            [
                'name' => 'KKN-PPM UGM Unit Sleman Barat',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Desa Sendangadi, Mlati, Sleman, D.I. Yogyakarta',
                'description' => 'Program pengabdian masyarakat difokuskan pada digitalisasi pemasaran UMKM lokal, pengelolaan sampah mandiri berbasis bank sampah, serta edukasi gizi seimbang untuk pencegahan stunting.',
            ]
        );

        ContactPerson::firstOrCreate(
            ['team_id' => $teamSleman->id, 'phone_number' => '081234567891'],
            [
                'name' => 'Fathin Naufal',
                'role_title' => 'Koordinator Mahasiswa Unit (Kormanit)',
                'line_id' => 'fathin_kkn2026',
            ]
        );

        $catalogSleman = OprecCatalog::firstOrCreate(
            ['team_id' => $teamSleman->id, 'title' => 'OPREC Anggota Tim KKN-PPM Sleman Barat 2026'],
            [
                'description' => "Unit KKN-PPM Sleman Barat membuka rekrutmen bagi rekan mahasiswa UGM lintas fakultas untuk berkontribusi dalam digitalisasi desa dan ketahanan pangan berkelanjutan.\n\nKualifikasi:\n1. Mahasiswa aktif UGM minimal semester 6.\n2. Berkomitmen mengikuti seluruh tahapan KKN.",
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(14),
                'status' => 'published',
            ]
        );

        $divProg = Division::firstOrCreate(
            ['catalog_id' => $catalogSleman->id, 'name' => 'Divisi Pemrograman & Digitalisasi UMKM'],
            [
                'job_description' => 'Membangun landing page katalog produk UMKM desa, implementasi QRIS pembayaran, dan pelatihan Google Bisnisku.',
                'requirements' => 'Mahasiswa Ilmu Komputer, Teknologi Informasi, Teknik Elektro, atau Sistem Informasi.',
                'quota' => 4,
            ]
        );

        $divPubdok = Division::firstOrCreate(
            ['catalog_id' => $catalogSleman->id, 'name' => 'Divisi Publikasi & Dokumentasi (Pubdok)'],
            [
                'job_description' => 'Membuat video profil desa, fotografi produk UMKM, desain feeds media sosial, dan pengelolaan akun media sosial resmi tim KKN.',
                'requirements' => 'Menguasai software desain grafis (Canva / Adobe Photoshop / Illustrator) atau video editor. Portofolio wajib dilampirkan.',
                'quota' => 3,
            ]
        );

        $divKesehatan = Division::firstOrCreate(
            ['catalog_id' => $catalogSleman->id, 'name' => 'Divisi Kesehatan & Sanitasi'],
            [
                'job_description' => 'Melaksanakan pemeriksaan tensi gratis bagi lansia, penyuluhan pencegahan stunting pada posyandu, dan audit sanitasi air bersih desa.',
                'requirements' => 'Diutamakan mahasiswa dari Klaster Medika (Kedokteran, Gizi, Farmasi, Keperawatan).',
                'quota' => 2,
            ]
        );

        // Konten PDF Valid untuk Pengujian Unduh Berkas Pelamar
        $minimalPdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
        $disk = config('filesystems.default');

        // Seed Lamaran 1 (Pending) dari Ahmad ke Divisi Pemrograman
        $app1 = Application::firstOrCreate(
            ['user_id' => $mahasiswa1->id, 'team_id' => $teamSleman->id],
            [
                'division_id' => $divProg->id,
                'status' => 'pending',
                'submitted_at' => Carbon::now()->subHours(5),
                'notes' => 'Memiliki pengalaman dalam pengembangan web Laravel dan pembuatan landing page UMKM lokal.',
            ]
        );

        $cvPath1 = "applications/{$app1->id}/cv_ahmad_mahasiswa_ugm.pdf";
        Storage::disk($disk)->put($cvPath1, $minimalPdf);
        ApplicationDocument::firstOrCreate(
            ['application_id' => $app1->id, 'document_type' => 'cv'],
            [
                's3_key' => $cvPath1,
                'original_filename' => 'CV_Ahmad_Mahasiswa_UGM.pdf',
                'file_size_bytes' => strlen($minimalPdf),
                'mime_type' => 'application/pdf',
            ]
        );

        // Seed Lamaran 2 (Pending) dari Sarah ke Divisi Pubdok
        $app2 = Application::firstOrCreate(
            ['user_id' => $mahasiswa2->id, 'team_id' => $teamSleman->id],
            [
                'division_id' => $divPubdok->id,
                'status' => 'pending',
                'submitted_at' => Carbon::now()->subHours(2),
                'notes' => 'Berpengalaman sebagai desainer grafis BEM dan pengelola sosial media organisasi kampus.',
            ]
        );

        $cvPath2 = "applications/{$app2->id}/cv_sarah_nurhaliza.pdf";
        Storage::disk($disk)->put($cvPath2, $minimalPdf);
        ApplicationDocument::firstOrCreate(
            ['application_id' => $app2->id, 'document_type' => 'cv'],
            [
                's3_key' => $cvPath2,
                'original_filename' => 'CV_Sarah_Nurhaliza_FEB.pdf',
                'file_size_bytes' => strlen($minimalPdf),
                'mime_type' => 'application/pdf',
            ]
        );

        // ==========================================
        // KATALOG DAERAH TERKENAL KKN UGM (PUBLISHED)
        // ==========================================

        // 5. Raja Ampat, Papua Barat Daya
        $picRajaAmpat = User::firstOrCreate(
            ['email' => 'pic.rajaampat@ugm.ac.id'],
            [
                'name' => 'Bintang Samudra (PIC Raja Ampat)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/471111/PN/52111',
                'faculty' => 'Fakultas Pertanian',
                'major' => 'Manajemen Sumberdaya Akuatik',
                'phone_number' => '081234567810',
            ]
        );
        $teamRajaAmpat = KknTeam::firstOrCreate(
            ['pic_user_id' => $picRajaAmpat->id],
            [
                'name' => 'KKN-PPM UGM Unit Raja Ampat Bahari',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Distrik Misool Selatan, Kepulauan Raja Ampat, Papua Barat Daya',
                'description' => 'Fokus pengabdian konservasi terumbu karang berbasis kearifan lokal Sasi Laut, edukasi ekowisata bahari ramah lingkungan, dan instalasi penerangan panel surya mandiri.',
            ]
        );
        $catRajaAmpat = OprecCatalog::firstOrCreate(
            ['team_id' => $teamRajaAmpat->id, 'title' => 'OPREC Tim KKN-PPM Raja Ampat: Konservasi Bahari & Energi Bersih 2026'],
            [
                'description' => 'Program 3T bergengsi UGM di Kepulauan Misool. Mengintegrasikan konservasi terumbu karang, pendampingan pemandu wisata lokal, dan elektrifikasi surya.',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(25),
                'status' => 'published',
            ]
        );
        Division::firstOrCreate(['catalog_id' => $catRajaAmpat->id, 'name' => 'Divisi Konservasi Terumbu Karang'], ['job_description' => 'Transplantasi karang dan pemetaan habitat terumbu karang.', 'requirements' => 'Mahasiswa Biologi, Perikanan, atau Ilmu Kelautan.', 'quota' => 4]);
        Division::firstOrCreate(['catalog_id' => $catRajaAmpat->id, 'name' => 'Divisi Energi Surya & Kelistrikan Pesisir'], ['job_description' => 'Perawatan dan instalasi solar home system balai desa.', 'requirements' => 'Mahasiswa Teknik Elektro atau Fisika Teknik.', 'quota' => 3]);
        Division::firstOrCreate(['catalog_id' => $catRajaAmpat->id, 'name' => 'Divisi Ekowisata & Edukasi Bahasa'], ['job_description' => 'Pelatihan bahasa Inggris dasar bagi pemandu ekowisata lokal.', 'requirements' => 'Mahasiswa FIB, FISIPOL, atau FEB.', 'quota' => 3]);

        // 6. Kepulauan Karimunjawa, Jawa Tengah
        $picKarimun = User::firstOrCreate(
            ['email' => 'pic.karimunjawa@ugm.ac.id'],
            [
                'name' => 'Anindya Putri (PIC Karimunjawa)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/472222/BI/52222',
                'faculty' => 'Fakultas Biologi',
                'major' => 'Biologi Lingkungan',
                'phone_number' => '081234567820',
            ]
        );
        $teamKarimun = KknTeam::firstOrCreate(
            ['pic_user_id' => $picKarimun->id],
            [
                'name' => 'KKN-PPM UGM Unit Karimunjawa Lestari',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Desa Kemujan, Kepulauan Karimunjawa, Jepara, Jawa Tengah',
                'description' => 'Optimalisasi rantai pasok pengolahan ikan asin higienis, sistem desalinasi air tawar skala rumah tangga, dan pemulihan sabuk hijau hutan mangrove.',
            ]
        );
        $catKarimun = OprecCatalog::firstOrCreate(
            ['team_id' => $teamKarimun->id, 'title' => 'OPREC Tim KKN Karimunjawa: Pemberdayaan Maritim & Restorasi Mangrove'],
            [
                'description' => 'Mari mengabdi di gugusan pulau Karimunjawa untuk memperkuat ketahanan ekonomi nelayan dan mitigasi abrasi pantai utara Jawa.',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(18),
                'status' => 'published',
            ]
        );
        Division::firstOrCreate(['catalog_id' => $catKarimun->id, 'name' => 'Divisi Pengolahan Hasil Laut & Branding'], ['job_description' => 'Inovasi kemasan dan higienitas produk olahan laut UMKM.', 'requirements' => 'Mahasiswa Teknologi Pangan, Pertanian, atau FEB.', 'quota' => 3]);
        Division::firstOrCreate(['catalog_id' => $catKarimun->id, 'name' => 'Divisi Restorasi Mangrove & Lingkungan'], ['job_description' => 'Pembibitan dan penanaman 1.000 bibit mangrove pelindung pesisir.', 'requirements' => 'Mahasiswa Biologi, Kehutanan, atau Geografi.', 'quota' => 3]);

        // 7. Sembalun Rinjani, Lombok Timur, NTB
        $picLombok = User::firstOrCreate(
            ['email' => 'pic.lombok@ugm.ac.id'],
            [
                'name' => 'Muhammad Zaky (PIC Lombok Sembalun)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/473333/GE/52333',
                'faculty' => 'Fakultas Geografi',
                'major' => 'Geografi Lingkungan',
                'phone_number' => '081234567830',
            ]
        );
        $teamLombok = KknTeam::firstOrCreate(
            ['pic_user_id' => $picLombok->id],
            [
                'name' => 'KKN-PPM UGM Unit Sembalun Rinjani',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Desa Sembalun Lawang, Sembalun, Lombok Timur, Nusa Tenggara Barat',
                'description' => 'Agroekowisata dataran tinggi lereng Gunung Rinjani, edukasi mitigasi kebencanaan gempa bumi, serta penguatan komoditas bawang putih dan kopi Sembalun.',
            ]
        );
        $catLombok = OprecCatalog::firstOrCreate(
            ['team_id' => $teamLombok->id, 'title' => 'OPREC Tim KKN-PPM Sembalun Rinjani: Agroekowisata & Desa Tangguh Bencana'],
            [
                'description' => 'Eksplorasi pengabdian di lembah megah kaldera Rinjani. Fokus pada pertanian presisi komoditas lokal dan kesiapsiagaan bencana warga.',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(21),
                'status' => 'published',
            ]
        );
        Division::firstOrCreate(['catalog_id' => $catLombok->id, 'name' => 'Divisi Agroteknologi & Rantai Pasok'], ['job_description' => 'Peningkatan mutu bibit bawang putih dan sertifikasi organik.', 'requirements' => 'Mahasiswa Fakultas Pertanian atau Teknologi Pertanian.', 'quota' => 4]);
        Division::firstOrCreate(['catalog_id' => $catLombok->id, 'name' => 'Divisi Mitigasi Bencana & Tata Ruang'], ['job_description' => 'Pemetaan jalur evakuasi dan edukasi simulasi gempa.', 'requirements' => 'Mahasiswa Geografi atau Teknik Sipil.', 'quota' => 3]);

        // 8. Labuan Bajo & Pulau Komodo, NTT
        $picLabuan = User::firstOrCreate(
            ['email' => 'pic.labuanbajo@ugm.ac.id'],
            [
                'name' => 'Clara Novita (PIC Labuan Bajo)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/474444/SP/52444',
                'faculty' => 'Fakultas Ilmu Sosial dan Ilmu Politik',
                'major' => 'Pariwisata',
                'phone_number' => '081234567840',
            ]
        );
        $teamLabuan = KknTeam::firstOrCreate(
            ['pic_user_id' => $picLabuan->id],
            [
                'name' => 'KKN-PPM UGM Unit Labuan Bajo Komodo',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Desa Komodo, Pulau Komodo, Manggarai Barat, Nusa Tenggara Timur',
                'description' => 'Pengembangan desa wisata berbasis kebudayaan lokal Ata Modo, pengelolaan sampah plastik terpadu 3R, dan pendampingan tenun ikat khas Manggarai.',
            ]
        );
        $catLabuan = OprecCatalog::firstOrCreate(
            ['team_id' => $teamLabuan->id, 'title' => 'OPREC Tim KKN Labuan Bajo: Kebudayaan Ata Modo & Pengelolaan Sampah Terpadu'],
            [
                'description' => 'Dedikasikan ilmu Anda di destinasi pariwisata super prioritas Indonesia. Pemberdayaan masyarakat asli Pulau Komodo dalam pariwisata berkelanjutan.',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(30),
                'status' => 'published',
            ]
        );
        Division::firstOrCreate(['catalog_id' => $catLabuan->id, 'name' => 'Divisi Pengelolaan Sampah & Sanitasi Pesisir'], ['job_description' => 'Edukasi pemilahan sampah dan sistem daur ulang terpadu.', 'requirements' => 'Mahasiswa Teknik Lingkungan atau Biologi.', 'quota' => 3]);
        Division::firstOrCreate(['catalog_id' => $catLabuan->id, 'name' => 'Divisi Budaya & Pemberdayaan Tenun'], ['job_description' => 'Dokumentasi motif tenun tradisional dan digital marketing.', 'requirements' => 'Mahasiswa FIB, FISIPOL, atau FEB.', 'quota' => 4]);

        // 9. Dataran Tinggi Dieng, Jawa Tengah
        $picDieng = User::firstOrCreate(
            ['email' => 'pic.dieng@ugm.ac.id'],
            [
                'name' => 'Rizki Pratama (PIC Dieng)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/475555/TK/52555',
                'faculty' => 'Fakultas Teknik',
                'major' => 'Teknik Geologi',
                'phone_number' => '081234567850',
            ]
        );
        $teamDieng = KknTeam::firstOrCreate(
            ['pic_user_id' => $picDieng->id],
            [
                'name' => 'KKN-PPM UGM Unit Dataran Tinggi Dieng',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Desa Dieng Kulon, Batur, Banjarnegara & Wonosobo, Jawa Tengah',
                'description' => 'Konservasi lahan terasering rawan longsor, edukasi pemanfaatan energi panas bumi (geothermal), serta diversifikasi produk hortikultura carica.',
            ]
        );
        $catDieng = OprecCatalog::firstOrCreate(
            ['team_id' => $teamDieng->id, 'title' => 'OPREC Tim KKN Dieng: Konservasi Lahan Vulkanik & Edukasi Panas Bumi'],
            [
                'description' => 'Program pengabdian di negeri di atas awan Dieng. Menjaga kelestarian lingkungan dataran tinggi dan memajukan agrowisata budaya Dieng.',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(16),
                'status' => 'published',
            ]
        );
        Division::firstOrCreate(['catalog_id' => $catDieng->id, 'name' => 'Divisi Konservasi Lahan & Geologi Terapan'], ['job_description' => 'Sosialisasi pencegahan erosi lereng dan drainase ramah lingkungan.', 'requirements' => 'Mahasiswa Teknik Geologi atau Geografi.', 'quota' => 3]);
        Division::firstOrCreate(['catalog_id' => $catDieng->id, 'name' => 'Divisi Hilirisasi Agrowisata Carica'], ['job_description' => 'Pengembangan kemasan higienis dan pendaftaran izin P-IRT UMKM.', 'requirements' => 'Mahasiswa Farmasi, Teknologi Pertanian, atau FEB.', 'quota' => 3]);

        // 10. Wakatobi, Sulawesi Tenggara
        $picWakatobi = User::firstOrCreate(
            ['email' => 'pic.wakatobi@ugm.ac.id'],
            [
                'name' => 'Fadli Rahman (PIC Wakatobi)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/476666/PN/52666',
                'faculty' => 'Fakultas Pertanian',
                'major' => 'Akuakultur',
                'phone_number' => '081234567860',
            ]
        );
        $teamWakatobi = KknTeam::firstOrCreate(
            ['pic_user_id' => $picWakatobi->id],
            [
                'name' => 'KKN-PPM UGM Unit Wakatobi Maritim',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Pulau Wangi-Wangi, Wakatobi, Sulawesi Tenggara',
                'description' => 'Ekonomi sirkular pengolahan rumput laut, gerakan pengurangan sampah plastik laut kepulauan Tukangbesi, dan literasi digital sekolah pesisir.',
            ]
        );
        $catWakatobi = OprecCatalog::firstOrCreate(
            ['team_id' => $teamWakatobi->id, 'title' => 'OPREC Tim KKN Wakatobi: Ekonomi Sirkular Rumput Laut & Laut Bersih'],
            [
                'description' => 'Bekerja bersama komunitas Bajo dan warga pulau dalam melestarikan cagar biosfer dunia UNESCO Wakatobi.',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(28),
                'status' => 'published',
            ]
        );
        Division::firstOrCreate(['catalog_id' => $catWakatobi->id, 'name' => 'Divisi Budidaya Rumput Laut & Hilirisasi'], ['job_description' => 'Metode pengeringan higienis dan formulasi sabun rumput laut.', 'requirements' => 'Mahasiswa Perikanan, Kimia, atau Biologi.', 'quota' => 3]);
        Division::firstOrCreate(['catalog_id' => $catWakatobi->id, 'name' => 'Divisi Circular Economy & Literasi Pesisir'], ['job_description' => 'Pemberdayaan bank sampah dan kelas alam literasi bagi anak pesisir.', 'requirements' => 'Mahasiswa FISIPOL, Psikologi, atau FIB.', 'quota' => 3]);

        // 11. Katalog Kulon Progo Mandiri (Pending Approval untuk Dites di Panel Admin)
        $pic2 = User::firstOrCreate(
            ['email' => 'pic.kulonprogo@ugm.ac.id'],
            [
                'name' => 'Rian Hidayat (PIC Kulon Progo)',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/480123/TP/51234',
                'faculty' => 'Fakultas Teknologi Pertanian',
                'major' => 'Teknik Pertanian',
                'phone_number' => '081234567899',
            ]
        );

        $team2 = KknTeam::firstOrCreate(
            ['pic_user_id' => $pic2->id],
            [
                'name' => 'KKN-PPM UGM Unit Kulon Progo Mandiri',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Desa Giripurwo, Girimulyo, Kulon Progo, D.I. Yogyakarta',
                'description' => 'Fokus pengabdian pada optimasi rantai pasok kopi Menoreh, instalasi mikrohidro ramah lingkungan, dan program sanitasi desa terpadu.',
            ]
        );

        $catalog2 = OprecCatalog::firstOrCreate(
            ['team_id' => $team2->id, 'title' => 'OPREC Anggota Tim KKN-PPM Kulon Progo Mandiri 2026'],
            [
                'description' => "Halo rekan-rekan mahasiswa UGM! Tim KKN-PPM Kulon Progo Mandiri 2026 mengundang rekan mahasiswa lintas klaster untuk berkolaborasi mengembangkan potensi perbukitan Menoreh di Giripurwo, Girimulyo.\n\nKualifikasi Umum:\n1. Mahasiswa aktif UGM angkatan 2021/2022.\n2. Bersedia tinggal di posko selama periode penugasan KKN.\n3. Antusias terhadap pemberdayaan masyarakat desa.",
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(20),
                'status' => 'pending_approval',
            ]
        );

        Division::firstOrCreate(
            ['catalog_id' => $catalog2->id, 'name' => 'Divisi Teknologi & Energi Terbarukan'],
            [
                'job_description' => 'Merancang dan memelihara prototipe instalasi mikrohidro dan pompa air tenaga surya untuk irigasi terasering.',
                'requirements' => 'Mahasiswa Teknik Elektro, Teknik Mesin, atau Fisika Teknik.',
                'quota' => 3,
            ]
        );

        Division::firstOrCreate(
            ['catalog_id' => $catalog2->id, 'name' => 'Divisi Agroindustri & UMKM Kopi'],
            [
                'job_description' => 'Mendampingi kelompok tani dalam standarisasi pascapanen kopi dan strategi branding digital UMKM lokal.',
                'requirements' => 'Mahasiswa Pertanian, Teknologi Pertanian, atau Ekonomika dan Bisnis.',
                'quota' => 4,
            ]
        );

        // 12. Contoh Registrasi PIC Pending untuk Dites di Panel Admin
        $calonPic = User::firstOrCreate(
            ['email' => 'calonpic@ugm.ac.id'],
            [
                'name' => 'Dwi Prasetyo',
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
                'nim' => '22/501234/SP/24567',
                'faculty' => 'Fakultas Ilmu Sosial dan Ilmu Politik',
                'major' => 'Manajemen dan Kebijakan Publik',
                'phone_number' => '082199887766',
            ]
        );

        PicRegistration::firstOrCreate(
            ['user_id' => $calonPic->id, 'proposed_team_name' => 'KKN-PPM Bantul Lestari'],
            [
                'target_location' => 'Desa Sriharjo, Imogiri, Bantul, D.I. Yogyakarta',
                'period' => 'Antar Semester 2026',
                'description' => 'Program ekowisata lembah Oya dan mitigasi kesiapsiagaan bencana tanah longsor berbasis komunitas.',
                'status' => 'pending',
            ]
        );

        $calonPic2 = User::firstOrCreate(
            ['email' => 'calonpic2@ugm.ac.id'],
            [
                'name' => 'Dina Wahyuni',
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
                'nim' => '22/502345/GE/24678',
                'faculty' => 'Fakultas Geografi',
                'major' => 'Pembangunan Wilayah',
                'phone_number' => '082199887755',
            ]
        );

        PicRegistration::firstOrCreate(
            ['user_id' => $calonPic2->id, 'proposed_team_name' => 'KKN-PPM Gunungkidul Hijau 2026'],
            [
                'target_location' => 'Desa Tepus, Tepus, Gunungkidul, D.I. Yogyakarta',
                'period' => 'Antar Semester 2026',
                'description' => 'Program ketahanan air bersih dan konservasi pesisir pantai selatan berbasis partisipasi warga.',
                'status' => 'pending',
            ]
        );

        // 13. Akun Mahasiswa Baru Bersih (Belum Melamar & Belum Mengajukan Tim)
        User::firstOrCreate(
            ['email' => 'mhs.bebas@ugm.ac.id'],
            [
                'name' => 'Budi Santoso',
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
                'nim' => '22/498888/SV/21888',
                'faculty' => 'Sekolah Vokasi',
                'major' => 'Sistem Informasi Akuntansi',
                'phone_number' => '081299112233',
            ]
        );
    }
}
