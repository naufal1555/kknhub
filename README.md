# KKNHub - Platform Rekrutmen Terbuka KKN Universitas Gadjah Mada

KKNHub adalah sistem informasi berbasis cloud untuk manajemen pendaftaran terbuka (*Open Recruitment / OPREC*) anggota tim Kuliah Kerja Nyata Pembelajaran Pemberdayaan Masyarakat (KKN-PPM) Universitas Gadjah Mada.

Aplikasi ini dibangun menggunakan arsitektur modern berbasis **Laravel 12**, **Tailwind CSS**, dan **Docker**, serta dirancang *cloud-ready* untuk kemudahan *deployment* ke **Amazon Web Services (AWS)** (Amazon ECS/EC2, AWS RDS MySQL 8.0, dan AWS S3 Private Storage dengan Temporary Pre-signed URLs).

---

## Fitur Utama Sistem

1. **Autentikasi Domain Ketat UGM**:
   - Validasi regex ketat hanya menerima alamat surel resmi `@ugm.ac.id`.
   - Pencegahan pendaftaran surel non-resmi atau subdomain tidak valid.
2. **Role-Based Access Control (RBAC 3 Peran)**:
   - **Administrator**: Memverifikasi pengajuan registrasi PIC tim KKN baru dan memoderasi proposal katalog rekrutmen terbuka (*Approve/Reject*).
   - **PIC Tim KKN**: Mengelola profil tim, kontak narahubung, membuat draf katalog, mengatur divisi & alokasi kuota, serta menyeleksi lamaran masuk (*Accept/Reject*).
   - **Mahasiswa**: Mengeksplorasi katalog OPREC aktif, mengajukan berkas pendaftaran ke divisi spesifik (1 lamaran per tim), memantau riwayat seleksi, dan membatalkan lamaran yang masih berstatus pending.
3. **Logika Transaksional & Pencegahan Balapan Data (*Concurrency Control*)**:
   - *Pessimistic locking* (`lockForUpdate`) untuk mencegah kuota divisi terlampaui (*over-quota*) saat seleksi bersamaan.
   - Pembatalan otomatis berkas lamaran lain secara kaskade (*auto-cancellation*) ketika mahasiswa telah resmi diterima di satu tim KKN.
4. **Penyimpanan Berkas Privat & Aman**:
   - Unggah CV (PDF maks 5 MB) dan Portofolio (PDF maks 10 MB).
   - Akses berkas melalui tautan bertenggat waktu (*Pre-signed Temporary URLs*, TTL 15 menit), menjaga kerahasiaan dokumen pribadi pelamar.
5. **Penutupan Otomatis Katalog**:
   - Scheduler/Artisan command berkala untuk menutup katalog OPREC yang telah melewati batas waktu pendaftaran (*deadline*).
6. **Antarmuka Standar Resmi UGM**:
   - Desain presisi warna UGM `#083d62`, tipografi Plus Jakarta Sans & Inter, latar belakang resmi Balairung UGM, dan sepenuhnya bebas emotikon.

---

## Kredensial Akun Pengujian (Seeder Default)

Seluruh akun di bawah ini telah disiapkan melalui Database Seeder untuk pengujian alur peran secara langsung:

| Peran | Alamat Email | Kata Sandi | Deskripsi Pengujian |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@ugm.ac.id` | `password123` | Akses panel admin, verifikasi PIC, dan moderasi proposal katalog. |
| **PIC Tim Sleman** | `pic.kkn@ugm.ac.id` | `password123` | PIC tim aktif dengan katalog terpublikasi (Unit Sleman Barat). |
| **PIC Tim Kulon Progo** | `pic.kulonprogo@ugm.ac.id` | `password123` | PIC tim dengan katalog berstatus *Pending Approval* (siap di-approve Admin). |
| **Mahasiswa Pelamar** | `mahasiswa@ugm.ac.id` | `password123` | Mahasiswa aktif yang telah melamar dan dapat menguji alur pembatalan. |
| **Calon PIC (Pending)** | `calonpic2@ugm.ac.id` | `password123` | Mahasiswa pengaju PIC tim Gunungkidul (siap diverifikasi Admin). |

---

## Panduan Instalasi & Menjalankan Proyek

Pilih salah satu metode di bawah ini untuk menjalankan aplikasi di komputer lokal:

### Metode 1: Menggunakan PHP & Composer Lokal

#### Prasyarat:
- PHP >= 8.2 (ekstensi: `pdo_sqlite`, `mbstring`, `openssl`, `curl`, `fileinfo`)
- Composer
- Node.js & NPM

#### Langkah-langkah:
```bash
# 1. Kloning repository
git clone https://github.com/naufal1555/kknhub.git
cd kknhub

# 2. Gandakan konfigurasi environment
cp .env.example .env

# 3. Instal dependensi PHP dan Node.js
composer install
npm install
npm run build

# 4. Generate Application Encryption Key
php artisan key:generate

# 5. Buat berkas database SQLite dan jalankan migrasi beserta seeder
touch database/database.sqlite
php artisan migrate --seed

# 6. Jalankan server pengembangan
php artisan serve
```
Aplikasi dapat langsung diakses melalui peramban web di: **http://localhost:8000**

---

### Metode 2: Menggunakan Docker & Docker Compose

#### Prasyarat:
- Docker Desktop aktif

#### Langkah-langkah:
```bash
# 1. Kloning repository
git clone https://github.com/naufal1555/kknhub.git
cd kknhub

# 2. Gandakan konfigurasi environment
cp .env.example .env

# 3. Jalankan container aplikasi
docker compose up -d --build

# 4. Generate key & migrasi database di dalam container
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```
Aplikasi dapat diakses di: **http://localhost:8000**

---

## Menjalankan Pengujian Otomatis (Automated Testing)

Aplikasi dilengkapi dengan rangkaian pengujian komprehensif (*Unit & Feature Tests*) yang mencakup pengujian validasi regex domain, RBAC, single-submission, kuota divisi, dan status transaksional:

```bash
# Menggunakan vendor PHPUnit
./vendor/bin/phpunit

# Atau menggunakan Artisan test runner
php artisan test
```
**Status Pengujian**: `38 tests, 136 assertions` — 100% Lulus.

---

## Lisensi

Proyek ini dikembangkan di bawah lisensi [MIT License](LICENSE).
Universitas Gadjah Mada - Direktorat Pengabdian kepada Masyarakat.
