@extends('layouts.ugm')

@section('title', 'Katalog Pendaftaran Terbuka (OPREC) KKN UGM - KKNHub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Hero Banner dengan Background Gedung Pusat/Balairung UGM -->
    <div class="relative overflow-hidden rounded-2xl shadow-xl border border-slate-300 bg-[#083d62]">
        <!-- Foto Gedung Balairung UGM (https://share.google/XHU7sZYuljYj1pgkH) -->
        <img src="/images/balairung-ugm.jpg" alt="Gedung Pusat Balairung Universitas Gadjah Mada" class="absolute inset-0 w-full h-full object-cover object-center opacity-60">
        
        <!-- Gradient Overlay Presisi untuk Keterbacaan Teks -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#083d62] via-[#083d62]/85 to-[#083d62]/40"></div>

        <div class="relative z-10 p-8 sm:p-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-3 max-w-2xl text-white">
                <span class="inline-block text-[11px] font-heading font-bold tracking-widest uppercase text-amber-300 bg-black/30 px-3 py-1 rounded-full border border-white/20 backdrop-blur-sm">
                    PUSAT INFORMASI TERINTEGRASI KKN UGM
                </span>
                <h1 class="text-3xl sm:text-4xl font-heading font-extrabold tracking-tight leading-tight text-white drop-shadow-md">
                    Katalog Pendaftaran Terbuka (OPREC) Tim KKN
                </h1>
                <p class="text-slate-100 text-sm font-sans leading-relaxed max-w-xl drop-shadow-sm">
                    Eksplorasi posisi divisi tim KKN, persyaratan kualifikasi, kuota real-time, dan ajukan lamaran resmi Anda menggunakan akun email resmi @ugm.ac.id.
                </p>
            </div>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.catalogs.index') }}" class="px-5 py-3 bg-amber-400 hover:bg-amber-300 text-slate-900 font-heading font-bold text-xs rounded-xl shadow-lg transition whitespace-nowrap border border-amber-200">
                        Panel Moderasi Admin
                    </a>
                @elseif(auth()->user()->isPic())
                    <a href="{{ route('pic.dashboard') }}" class="px-5 py-3 bg-sky-400 hover:bg-sky-300 text-slate-900 font-heading font-bold text-xs rounded-xl shadow-lg transition whitespace-nowrap border border-sky-200">
                        Kelola OPREC Tim PIC
                    </a>
                @else
                    <a href="{{ route('applications.history') }}" class="px-5 py-3 bg-white hover:bg-slate-100 text-[#083d62] font-heading font-bold text-xs rounded-xl shadow-lg transition whitespace-nowrap border border-white/60">
                        Cek Status Lamaran Anda
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="px-5 py-3 bg-white hover:bg-slate-100 text-[#083d62] font-heading font-bold text-xs rounded-xl shadow-lg transition whitespace-nowrap border border-white/60">
                    Masuk Akun UGM
                </a>
            @endauth
        </div>
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

    <!-- Search & Filter Bar -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <form action="{{ route('catalog.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari judul OPREC, tema, nama tim, atau kualifikasi..." class="w-full text-sm font-sans border border-slate-300 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
            </div>
            <div class="md:col-span-4">
                <input type="text" name="location" value="{{ $location ?? '' }}" placeholder="Filter lokasi penempatan (misal: Sleman, Bantul)..." class="w-full text-sm font-sans border border-slate-300 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2.5 bg-[#0c365b] hover:bg-[#08253f] text-white rounded-lg text-xs font-heading font-bold tracking-wide transition shadow-sm">
                    Cari Tim
                </button>
                @if($search || $location)
                    <a href="{{ route('catalog.index') }}" class="px-3 py-2.5 border border-slate-300 rounded-lg text-xs font-heading font-semibold text-slate-700 hover:bg-slate-100 flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Section List OPREC: Card Grid 2-Kolom Sesuai PRD Section 4.3 -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-heading font-bold text-slate-900">
                Daftar Rekrutmen Aktif ({{ $catalogs->total() }})
            </h2>
            <span class="text-xs font-sans text-slate-500">
                Katalog resmi terverifikasi dan belum melewati batas waktu
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($catalogs as $catalog)
                <div class="bg-white border border-slate-200 hover:border-[#0c365b]/50 rounded-2xl p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="text-[11px] font-heading font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    Lokasi: {{ $catalog->team->target_location }}
                                </span>
                                <h3 class="text-lg font-heading font-bold text-slate-900 mt-2 leading-snug hover:text-[#0c365b] transition">
                                    <a href="{{ route('catalog.show', $catalog->id) }}">{{ $catalog->title }}</a>
                                </h3>
                                <div class="text-xs font-sans font-semibold text-[#0c365b] mt-0.5">
                                    {{ $catalog->team->name }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] font-heading font-semibold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded">
                                    Batas: {{ $catalog->deadline->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        @if($catalog->description)
                            <p class="text-xs font-sans text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $catalog->description }}
                            </p>
                        @endif

                        <!-- Divisi dan Kuota Ringkas -->
                        <div class="pt-2">
                            <span class="text-[11px] font-heading font-semibold uppercase tracking-wider text-slate-500 block mb-1.5">
                                Divisi yang Dibutuhkan:
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($catalog->divisions as $div)
                                    <span class="text-xs font-sans bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-lg text-slate-700 flex items-center gap-1.5">
                                        <span>{{ $div->name }}</span>
                                        <span class="text-[10px] font-heading bg-slate-200 text-slate-800 font-bold px-1.5 py-0.2 rounded-full">
                                            {{ $div->quota }}
                                        </span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-xs font-sans text-slate-500">
                            Total Kuota: <span class="font-heading font-bold text-slate-800">{{ $catalog->divisions->sum('quota') }} Mahasiswa</span>
                        </div>
                        <a href="{{ route('catalog.show', $catalog->id) }}" class="px-4 py-2 bg-[#0c365b] hover:bg-[#08253f] text-white rounded-xl text-xs font-heading font-bold transition shadow-sm">
                            Lihat Detail dan Daftar
                        </a>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 bg-white border border-dashed border-slate-300 rounded-2xl p-12 text-center space-y-3">
                    <h3 class="font-heading font-bold text-slate-800 text-base">
                        Belum ada katalog OPREC yang sesuai kriteria pencarian
                    </h3>
                    <p class="text-xs font-sans text-slate-500 max-w-sm mx-auto">
                        Silakan sesuaikan kata kunci pencarian Anda atau periksa kembali secara berkala saat tim KKN mempublikasikan rekrutmen baru.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $catalogs->links() }}
        </div>
    </div>
</div>
@endsection
