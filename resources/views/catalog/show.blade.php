@extends('layouts.ugm')

@section('title', $catalog->title . ' - KKNHub UGM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('catalog.index') }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:text-[#08253f] flex items-center gap-1">
            &larr; Kembali ke Katalog OPREC
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kolom Kiri: Informasi Rinci Tim & OPREC (8 Kolom) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Header Info -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-heading font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        Lokasi: {{ $catalog->team->target_location }}
                    </span>
                    <span class="text-xs font-heading font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        Periode: {{ $catalog->team->period }}
                    </span>
                    <span class="text-xs font-heading font-semibold px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                        Batas Pendaftaran: {{ $catalog->deadline->format('d M Y, H:i') }} WIB
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-heading font-bold text-slate-900 leading-tight">
                    {{ $catalog->title }}
                </h1>
                <div class="text-sm font-sans text-slate-600">
                    Tim KKN: <span class="font-heading font-bold text-[#0c365b]">{{ $catalog->team->name }}</span>
                </div>

                @if($catalog->team->theme)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-sans text-slate-700">
                        <span class="font-heading font-bold text-slate-800 uppercase tracking-wide text-[11px] block mb-1">Tema Kegiatan:</span>
                        {{ $catalog->team->theme }}
                    </div>
                @endif

                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-heading font-bold uppercase tracking-wider text-slate-500 mb-2">
                        Deskripsi Rekrutmen & Ruang Lingkup
                    </h3>
                    <p class="text-sm font-sans text-slate-700 whitespace-pre-line leading-relaxed">
                        {{ $catalog->description }}
                    </p>
                </div>

                @if($catalog->team->contactPersons && $catalog->team->contactPersons->isNotEmpty())
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-heading font-bold uppercase tracking-wider text-slate-500 mb-2">
                            Narahubung Resmi Tim KKN
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($catalog->team->contactPersons as $cp)
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-sans">
                                    <div class="font-heading font-bold text-slate-800">
                                        {{ $cp->name }}
                                        <span class="font-sans font-normal text-slate-500">({{ $cp->role_label }})</span>
                                    </div>
                                    <div class="text-slate-600 mt-1 font-mono text-[11px]">
                                        Telepon: {{ $cp->phone }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Daftar Rinci Divisi & Persyaratan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-heading font-bold text-slate-900">
                        Divisi yang Dibuka & Kualifikasi
                    </h2>
                    <span class="text-xs font-heading font-semibold bg-slate-100 text-slate-700 px-3 py-1 rounded-full border border-slate-200">
                        Total Kuota: {{ $catalog->divisions->sum('quota') }} Mahasiswa
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($catalog->divisions as $div)
                        @php
                            $stats = $divisionStats[$div->id] ?? ['accepted' => 0, 'remaining' => $div->quota];
                        @endphp
                        <div class="py-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <h3 class="font-heading font-bold text-base text-slate-800">{{ $div->name }}</h3>
                                <span class="text-xs font-heading font-semibold px-2.5 py-0.5 rounded-full {{ $stats['remaining'] === 0 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $stats['remaining'] === 0 ? 'Kuota Penuh' : 'Tersisa ' . $stats['remaining'] . ' dari ' . $div->quota . ' kuota' }}
                                </span>
                            </div>

                            @if($div->job_description)
                                <div class="text-xs font-sans text-slate-600">
                                    <span class="font-heading font-semibold text-slate-700">Tugas & Tanggung Jawab:</span>
                                    <p class="whitespace-pre-line mt-0.5">{{ $div->job_description }}</p>
                                </div>
                            @endif

                            @if($div->requirements)
                                <div class="text-xs font-sans text-slate-600">
                                    <span class="font-heading font-semibold text-slate-700">Kualifikasi yang Dicari:</span>
                                    <p class="whitespace-pre-line mt-0.5">{{ $div->requirements }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Widget Pendaftaran Mahasiswa (4 Kolom) -->
        <div class="lg:col-span-4 space-y-6 sticky top-24">
            @guest
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4 text-center">
                    <h3 class="text-base font-heading font-bold text-slate-900">Tertarik Mendaftar?</h3>
                    <p class="text-xs font-sans text-slate-600 leading-relaxed">
                        Silakan masuk menggunakan akun email resmi @ugm.ac.id Anda untuk mengajukan berkas pendaftaran ke divisi tim KKN ini.
                    </p>
                    <a href="{{ route('login') }}" class="block w-full py-2.5 bg-[#083d62] hover:bg-[#052942] text-white rounded-xl font-heading font-bold text-xs transition shadow-sm">
                        Masuk Akun UGM
                    </a>
                </div>
            @elseif(auth()->user()->isAdmin())
                <!-- Panel Khusus Administrator (Tidak Dapat Melamar) -->
                <div class="bg-amber-50 border border-amber-300 rounded-2xl p-6 space-y-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="bg-amber-400 text-slate-900 font-heading font-bold px-2 py-0.5 rounded text-[10px] uppercase">
                            Admin KKNHub
                        </span>
                        <h3 class="font-heading font-bold text-slate-900 text-base">Mode Administrator</h3>
                    </div>
                    <p class="text-xs font-sans text-slate-700 leading-relaxed">
                        Anda saat ini login sebagai Administrator. Sebagai pengelola sistem, Anda bertugas memoderasi dan mengawasi proposal ini. Formulir lamaran dinonaktifkan untuk peran Admin.
                    </p>
                    <div class="pt-2 border-t border-amber-200 flex flex-col gap-2">
                        <a href="{{ route('admin.catalogs.show', $catalog->id) }}" class="block w-full text-center py-2.5 bg-[#083d62] hover:bg-[#052942] text-white rounded-xl font-heading font-bold text-xs transition shadow-sm">
                            Buka Moderasi Proposal Ini
                        </a>
                        <a href="{{ route('admin.catalogs.index') }}" class="block w-full text-center py-2 border border-slate-300 hover:bg-white rounded-xl text-xs font-heading font-semibold text-slate-700 transition">
                            Kembali ke Daftar Moderasi
                        </a>
                    </div>
                </div>
            @elseif(auth()->user()->isPic())
                @if(auth()->id() === $catalog->team->pic_user_id)
                    <div class="bg-sky-50 border border-sky-300 rounded-2xl p-6 text-center space-y-3 shadow-sm">
                        <div class="flex justify-center items-center gap-2">
                            <span class="bg-sky-500 text-white font-heading font-bold px-2 py-0.5 rounded text-[10px] uppercase">
                                PIC Tim Ini
                            </span>
                            <h3 class="font-heading font-bold text-[#083d62] text-base">Panel Tim Anda</h3>
                        </div>
                        <p class="text-xs font-sans text-slate-600 leading-relaxed">
                            Sebagai penanggung jawab tim ini, Anda dapat meninjau berkas pelamar dan menetapkan keputusan seleksi.
                        </p>
                        <a href="{{ route('pic.catalogs.applications.index', $catalog->id) }}" class="block w-full py-2.5 bg-[#083d62] hover:bg-[#052942] text-white rounded-xl font-heading font-bold text-xs transition shadow-sm">
                            Kelola Pelamar Tim Ini
                        </a>
                    </div>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center space-y-3">
                        <span class="bg-slate-300 text-slate-800 font-heading font-bold px-2 py-0.5 rounded text-[10px] uppercase">
                            PIC Tim KKN
                        </span>
                        <h3 class="font-heading font-bold text-slate-800 text-base">Akun PIC Tim</h3>
                        <p class="text-xs font-sans text-slate-600 leading-relaxed">
                            Anda terdaftar sebagai PIC untuk tim KKN lain. Akun PIC tidak dapat mendaftar sebagai pelamar ke tim KKN ini.
                        </p>
                        <a href="{{ route('pic.dashboard') }}" class="block w-full py-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl font-heading font-bold text-xs transition">
                            Buka Dasbor Tim Anda
                        </a>
                    </div>
                @endif
            @elseif($existingApplication)
                <!-- Mahasiswa Sudah Mendaftar (FR-12 Single Submission Enforced) -->
                <div class="bg-white border-2 border-[#0c365b]/30 rounded-2xl p-6 shadow-sm space-y-4">
                    <div>
                        <h3 class="font-heading font-bold text-slate-900 text-base">Lamaran Anda Terkirim</h3>
                        <p class="text-xs font-sans text-slate-600 mt-1">
                            Anda telah mengajukan pendaftaran ke tim ini untuk posisi:
                        </p>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                        <div class="text-xs font-sans text-slate-500">Divisi yang dilamar:</div>
                        <div class="font-heading font-bold text-sm text-slate-800">{{ $existingApplication->division->name }}</div>
                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-sans text-slate-500">Status Seleksi:</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-heading font-bold
                                @if($existingApplication->status === 'accepted') bg-emerald-100 text-emerald-800
                                @elseif($existingApplication->status === 'pending') bg-amber-100 text-amber-800
                                @elseif($existingApplication->status === 'rejected') bg-rose-100 text-rose-800
                                @else bg-slate-200 text-slate-700 @endif">
                                {{ strtoupper($existingApplication->status) }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('applications.history') }}" class="block w-full text-center py-2.5 border border-slate-300 hover:bg-slate-50 rounded-xl text-xs font-heading font-semibold text-slate-700 transition">
                        Buka Riwayat Lamaran
                    </a>
                </div>
            @elseif(auth()->user()->isMahasiswa())
                <!-- Form Pendaftaran Resmi Mahasiswa -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div>
                        <h3 class="text-lg font-heading font-bold text-slate-900">Formulir Pendaftaran</h3>
                        <p class="text-xs font-sans text-slate-500 mt-0.5">Pilih tepat satu divisi yang sesuai kualifikasi Anda.</p>
                    </div>

                    <form action="{{ route('applications.store', $catalog->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-heading font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Pilih Divisi *
                            </label>
                            <select name="division_id" required class="w-full text-xs font-sans border border-slate-300 rounded-xl p-2.5 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                                <option value="">-- Pilih Salah Satu Divisi --</option>
                                @foreach($catalog->divisions as $div)
                                    @php
                                        $stats = $divisionStats[$div->id] ?? ['remaining' => $div->quota];
                                    @endphp
                                    <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }} {{ $stats['remaining'] === 0 ? 'disabled' : '' }}>
                                        {{ $div->name }} (Sisa: {{ $stats['remaining'] }} / {{ $div->quota }}) {{ $stats['remaining'] === 0 ? '- Penuh' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-heading font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Unggah CV (Wajib PDF, Maksimal 5 MB) *
                            </label>
                            <input type="file" name="cv_file" required accept=".pdf" class="w-full text-xs font-sans border border-slate-300 rounded-xl p-2 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-heading file:font-bold file:bg-[#0c365b]/10 file:text-[#0c365b] hover:file:bg-[#0c365b]/20">
                        </div>

                        <div>
                            <label class="block text-xs font-heading font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Unggah Portofolio (Opsional PDF, Maksimal 10 MB)
                            </label>
                            <input type="file" name="portfolio_file" accept=".pdf" class="w-full text-xs font-sans border border-slate-300 rounded-xl p-2 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-heading file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        </div>

                        <div>
                            <label class="block text-xs font-heading font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Catatan Pengalaman / Motivasi
                            </label>
                            <textarea name="notes" rows="3" placeholder="Sampaikan alasan ketertarikan atau keahlian pendukung Anda..." class="w-full text-xs font-sans border border-slate-300 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-[#0c365b]">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-3 bg-[#0c365b] hover:bg-[#08253f] text-white rounded-xl font-heading font-bold text-xs shadow-md transition">
                            Kirim Lamaran Sekarang
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
