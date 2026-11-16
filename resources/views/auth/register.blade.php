@extends('layouts.ugm')

@section('title', 'Registrasi Akun - KKNHub Universitas Gadjah Mada')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="w-full max-w-xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-md p-6 sm:p-8">
        <div class="text-center mb-6">
            <img src="/images/ugm-emblem.svg" alt="Universitas Gadjah Mada" class="w-14 h-14 mx-auto mb-2">
            <h1 class="text-xl font-heading font-bold text-[#083d62] uppercase tracking-wide">UNIVERSITAS GADJAH MADA</h1>
            <h2 class="text-sm font-heading font-semibold text-slate-700 mt-1">Registrasi Akun KKNHub</h2>
            <p class="text-xs text-slate-500 font-sans mt-0.5">Sistem Informasi Pengabdian & Rekrutmen KKN UGM</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4 text-xs font-sans">
            @csrf

            <!-- Pilihan Tipe Akun / Tujuan Pendaftaran -->
            <div>
                <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-2">Pilih Peran Pendaftaran *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <label class="flex items-start gap-2.5 p-3 border rounded-xl cursor-pointer transition hover:bg-slate-50 border-slate-300" id="label-role-mhs">
                        <input type="radio" name="registration_type" value="mahasiswa" {{ old('registration_type', 'mahasiswa') === 'mahasiswa' ? 'checked' : '' }} onchange="togglePicFields(false)" class="mt-0.5 text-[#083d62] focus:ring-[#083d62]">
                        <div>
                            <div class="font-heading font-bold text-slate-900 text-xs">Mahasiswa Pelamar</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Mencari tim KKN dan mendaftar pada divisi rekrutmen terbuka.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-2.5 p-3 border rounded-xl cursor-pointer transition hover:bg-slate-50 border-slate-300" id="label-role-pic">
                        <input type="radio" name="registration_type" value="pic" {{ old('registration_type') === 'pic' ? 'checked' : '' }} onchange="togglePicFields(true)" class="mt-0.5 text-[#083d62] focus:ring-[#083d62]">
                        <div>
                            <div class="font-heading font-bold text-slate-900 text-xs">Koordinator / PIC Tim</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Mengajukan tim KKN baru dari daerah mana saja dan membuka OPREC.</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form Data Pengguna -->
            <div>
                <label for="name" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ahmad Fathin Naufal"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
            </div>

            <div>
                <label for="email" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Email Resmi UGM (@ugm.ac.id) *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@ugm.ac.id"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                <p class="text-[11px] text-slate-500 mt-1">Hanya menerima format alamat email resmi @ugm.ac.id</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="nim" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">NIM Mahasiswa</label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}" placeholder="21/xxxxxx/xx/xxxxx"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label for="phone_number" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp/HP</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="faculty" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Fakultas</label>
                    <input type="text" id="faculty" name="faculty" value="{{ old('faculty') }}" placeholder="Fakultas Teknik"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label for="major" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Program Studi / Departemen</label>
                    <input type="text" id="major" name="major" value="{{ old('major') }}" placeholder="Teknologi Informasi"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
            </div>

            <!-- Field Khusus Jika Mendaftar Sebagai Koordinator / PIC Tim KKN Baru -->
            <div id="pic-fields" class="{{ old('registration_type') === 'pic' ? 'block' : 'hidden' }} p-4 bg-sky-50/70 border border-sky-200 rounded-2xl space-y-3.5">
                <div class="flex items-center gap-2 pb-2 border-b border-sky-200">
                    <span class="px-2 py-0.5 bg-[#083d62] text-white font-heading font-bold rounded text-[10px] uppercase">Form PIC</span>
                    <h3 class="font-heading font-bold text-[#083d62] text-xs">Informasi Pengajuan Tim KKN Baru</h3>
                </div>

                <div>
                    <label for="proposed_team_name" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Tim KKN yang Diajukan *</label>
                    <input type="text" id="proposed_team_name" name="proposed_team_name" value="{{ old('proposed_team_name') }}" placeholder="Contoh: KKN-PPM Unit Dlingo Bantul / Unit Labuan Bajo"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                    <p class="text-[11px] text-slate-500 mt-0.5">Bisa untuk tim KKN dari daerah mana saja di Indonesia.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="target_location" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Lokasi Target Pengabdian *</label>
                        <input type="text" id="target_location" name="target_location" value="{{ old('target_location') }}" placeholder="Contoh: Dlingo, Bantul, D.I. Yogyakarta"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                    </div>
                    <div>
                        <label for="period" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Periode KKN *</label>
                        <input type="text" id="period" name="period" value="{{ old('period', 'Antar Semester 2026') }}" placeholder="Contoh: Antar Semester 2026"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                    </div>
                </div>

                <div>
                    <label for="team_description" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Singkat Program</label>
                    <textarea id="team_description" name="team_description" rows="2" placeholder="Fokus kegiatan pengabdian, kemitraan desa, atau tema unggulan tim..."
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">{{ old('team_description') }}</textarea>
                </div>

                <p class="text-[11px] text-slate-600 bg-white/80 p-2.5 rounded-lg border border-sky-100">
                    Pengajuan tim KKN baru akan diverifikasi oleh Administrator DPkM UGM sebelum katalog rekrutmen dapat dipublikasikan.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi *</label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label for="password_confirmation" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Ulangi Kata Sandi *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi kata sandi"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
            </div>

            <button type="submit" id="btn-submit" class="w-full py-3 bg-[#083d62] hover:bg-[#052942] text-white font-heading font-bold rounded-xl text-xs transition shadow-sm mt-2">
                Daftar Akun KKNHub
            </button>
        </form>

        <div class="mt-6 text-center text-xs font-sans text-slate-600 border-t border-slate-100 pt-4">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-heading font-bold text-[#083d62] hover:underline">
                Masuk di Sini
            </a>
        </div>
    </div>
</div>

<script>
function togglePicFields(isPic) {
    const picFields = document.getElementById('pic-fields');
    const labelMhs = document.getElementById('label-role-mhs');
    const labelPic = document.getElementById('label-role-pic');
    const btnSubmit = document.getElementById('btn-submit');

    if (isPic) {
        picFields.classList.remove('hidden');
        labelPic.classList.add('border-[#083d62]', 'bg-sky-50/40');
        labelMhs.classList.remove('border-[#083d62]', 'bg-sky-50/40');
        btnSubmit.innerText = 'Daftar & Ajukan Tim KKN';
    } else {
        picFields.classList.add('hidden');
        labelMhs.classList.add('border-[#083d62]', 'bg-sky-50/40');
        labelPic.classList.remove('border-[#083d62]', 'bg-sky-50/40');
        btnSubmit.innerText = 'Daftar Akun Mahasiswa';
    }
}

// Inisialisasi status border radio saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const picRadio = document.querySelector('input[name="registration_type"][value="pic"]');
    if (picRadio && picRadio.checked) {
        togglePicFields(true);
    } else {
        togglePicFields(false);
    }
});
</script>
@endsection
