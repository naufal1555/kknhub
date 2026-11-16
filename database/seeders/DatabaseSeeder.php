<?php

namespace Database\Seeders;

use App\Models\ContactPerson;
use App\Models\Division;
use App\Models\KknTeam;
use App\Models\OprecCatalog;
use App\Models\PicRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        // 2. Akun PIC Default KKNHub
        $pic = User::firstOrCreate(
            ['email' => 'pic.kkn@ugm.ac.id'],
            [
                'name' => 'PIC KKN Unit Sleman',
                'password' => bcrypt('password123'),
                'role' => 'pic',
                'nim' => '21/470000/TK/52000',
                'faculty' => 'Fakultas Teknik',
                'major' => 'Teknologi Informasi',
                'phone_number' => '081234567891',
            ]
        );

        // 3. Akun Mahasiswa Pendaftar Default KKNHub
        $mahasiswa = User::firstOrCreate(
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

        // 4. Buat Tim KKN untuk PIC
        $team = KknTeam::firstOrCreate(
            ['pic_user_id' => $pic->id],
            [
                'name' => 'KKN-PPM UGM Unit Sleman Barat',
                'period' => 'Antar Semester 2026',
                'target_location' => 'Desa Sendangadi, Mlati, Sleman, D.I. Yogyakarta',
                'description' => 'Program pengabdian masyarakat difokuskan pada digitalisasi pemasaran UMKM lokal, pengelolaan sampah mandiri berbasis bank sampah, serta edukasi gizi seimbang untuk pencegahan stunting.',
            ]
        );

        // Kontak Narahubung Tim
        ContactPerson::firstOrCreate(
            ['team_id' => $team->id, 'phone_number' => '081234567891'],
            [
                'name' => 'Fathin Naufal',
                'role_title' => 'Koordinator Mahasiswa Unit (Kormanit)',
                'line_id' => 'fathin_kkn2026',
            ]
        );

        ContactPerson::firstOrCreate(
            ['team_id' => $team->id, 'phone_number' => '081398765432'],
            [
                'name' => 'Siti Rahmawati',
                'role_title' => 'Bendahara & Humas',
                'line_id' => 'siti_rahma',
            ]
        );

        // 5. Buat Katalog OPREC Aktif (Published)
        $catalog = OprecCatalog::firstOrCreate(
            ['team_id' => $team->id, 'title' => 'OPREC Anggota Tim KKN-PPM Sleman Barat 2026'],
            [
                'description' => "Halo Gadjah Mada Muda! Tim KKN-PPM Sleman Barat 2026 membuka kesempatan bergabung bagi mahasiswa aktif UGM lintas disiplin ilmu. Bersama kami, Anda akan terjun langsung mengabdi dan memberdayakan masyarakat di Sendangadi, Mlati, Sleman.\n\nPersyaratan Umum:\n1. Mahasiswa aktif UGM yang telah memenuhi syarat SKS KKN-PPM.\n2. Berkomitmen penuh mengikuti seluruh rangkaian pembekalan hingga penarikan.\n3. Mampu bekerja sama dalam tim lintas fakultas.",
                'start_date' => Carbon::now()->subDays(2),
                'deadline' => Carbon::now()->addDays(14),
                'status' => 'published',
            ]
        );

        // Tambahkan Divisi-Divisi pada Katalog
        Division::firstOrCreate(
            ['catalog_id' => $catalog->id, 'name' => 'Divisi Acara & Sosialisasi'],
            [
                'job_description' => 'Merancang kurikulum pelatihan digital marketing bagi pelaku UMKM desa, menyusun rundown kegiatan penyuluhan, dan memfasilitasi workshop warga.',
                'requirements' => 'Komunikatif, percaya diri tampil di depan umum, memiliki pengalaman public speaking atau fasilitator pelatihan.',
                'quota' => 4,
            ]
        );

        Division::firstOrCreate(
            ['catalog_id' => $catalog->id, 'name' => 'Divisi Publikasi & Dokumentasi (Pubdok)'],
            [
                'job_description' => 'Membuat video profil desa, fotografi produk UMKM, desain feeds media sosial, dan pengelolaan akun Instagram resmi tim KKN.',
                'requirements' => 'Menguasai software desain grafis (Canva / Adobe Photoshop / Illustrator) atau video editor (CapCut / Premiere Pro). Portofolio wajib dilampirkan.',
                'quota' => 3,
            ]
        );

        Division::firstOrCreate(
            ['catalog_id' => $catalog->id, 'name' => 'Divisi Logistik & Perlengkapan'],
            [
                'job_description' => 'Mengkoordinasikan kebutuhan transportasi, inventaris peralatan, izin tempat balai desa, konsumsi, dan posko KKN.',
                'requirements' => 'Cekatan, disiplin, teliti, diutamakan memiliki SIM C atau SIM A.',
                'quota' => 3,
            ]
        );

        Division::firstOrCreate(
            ['catalog_id' => $catalog->id, 'name' => 'Divisi Kesehatan & Sanitasi'],
            [
                'job_description' => 'Melaksanakan pemeriksaan tensi gratis bagi lansia, penyuluhan pencegahan stunting pada posyandu, dan audit sanitasi air bersih desa.',
                'requirements' => 'Diutamakan mahasiswa dari Klaster Medika (Kedokteran, Kedokteran Gigi, Keperawatan, Gizi, Farmasi).',
                'quota' => 2,
            ]
        );

        // 6. Buat Contoh Registrasi PIC Pending untuk Dites di Panel Admin
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

        // 7. Buat Contoh PIC & Tim dengan Katalog Pending Approval untuk Moderasi Admin
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

        Division::firstOrCreate(
            ['catalog_id' => $catalog2->id, 'name' => 'Divisi Lingkungan & Pariwisata Berkelanjutan'],
            [
                'job_description' => 'Mengembangkan jalur trekking desa wisata dan mengedukasi konservasi lereng bukit rawan erosi.',
                'requirements' => 'Mahasiswa Geografi, Kehutanan, atau Biologi.',
                'quota' => 3,
            ]
        );
    }
}
