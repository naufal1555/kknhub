@extends('layouts.ugm')

@section('title', 'Dasbor PIC Tim KKN - KKNHub UGM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header Dasbor PIC -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-heading font-bold bg-[#083d62] text-white uppercase tracking-wider">
                    Koordinator Tim KKN
                </span>
                <span class="text-xs font-sans text-slate-500">Periode: {{ $team->period }}</span>
            </div>
            <h1 class="text-2xl font-heading font-bold text-slate-900 mt-1">{{ $team->name }}</h1>
            <p class="text-slate-600 font-sans text-xs mt-0.5">Lokasi Target Pengabdian: <span class="font-semibold text-slate-800">{{ $team->target_location }}</span></p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('pic.team.edit') }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-heading font-semibold text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition">
                Profil Tim & Narahubung
            </a>
            <a href="{{ route('pic.catalogs.create') }}" class="px-4 py-2 bg-[#083d62] hover:bg-[#052942] text-white rounded-xl text-xs font-heading font-bold shadow-sm transition">
                Buat Katalog OPREC Baru
            </a>
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

    <!-- 4 Kartu Metrik Sinkronisasi Real-Time -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kartu 1: Status Publikasi Katalog -->
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
            <div class="text-xs font-heading font-bold uppercase text-slate-500 tracking-wider">Total Katalog OPREC</div>
            <div class="text-2xl font-heading font-bold text-slate-900 mt-1.5">{{ $catalogs->count() }}</div>
            <div class="text-[11px] font-sans text-slate-500 mt-1">
                {{ $catalogs->where('status', 'published')->count() }} Terpublikasi Aktif
            </div>
        </div>

        <!-- Kartu 2: Total Kuota Anggota -->
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
            <div class="text-xs font-heading font-bold uppercase text-slate-500 tracking-wider">Kapasitas Kuota Divisi</div>
            <div class="text-2xl font-heading font-bold text-slate-900 mt-1.5">{{ $summary['total_quota'] ?? 0 }}</div>
            <div class="text-[11px] font-sans text-slate-500 mt-1">
                Kebutuhan mahasiswa lintas klaster
            </div>
        </div>

        <!-- Kartu 3: Total Pelamar Masuk -->
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
            <div class="text-xs font-heading font-bold uppercase text-slate-500 tracking-wider">Total Pelamar Masuk</div>
            <div class="text-2xl font-heading font-bold text-[#083d62] mt-1.5">{{ $summary['total_applicants'] ?? 0 }}</div>
            <div class="text-[11px] font-sans text-emerald-700 mt-1 font-semibold">
                {{ $summary['total_accepted'] ?? 0 }} Telah Diterima di Tim
            </div>
        </div>

        <!-- Kartu 4: Pelamar Menunggu Seleksi (Pending Alert) -->
        <div class="border p-5 rounded-2xl shadow-sm transition
            @if(($summary['total_pending'] ?? 0) > 0) bg-amber-50/80 border-amber-300 @else bg-white border-slate-200 @endif">
            <div class="flex items-center justify-between">
                <span class="text-xs font-heading font-bold uppercase tracking-wider
                    @if(($summary['total_pending'] ?? 0) > 0) text-amber-900 @else text-slate-500 @endif">
                    Menunggu Seleksi
                </span>
                @if(($summary['total_pending'] ?? 0) > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-heading font-bold bg-amber-500 text-white animate-pulse">
                        Perlu Review
                    </span>
                @endif
            </div>
            <div class="text-2xl font-heading font-bold mt-1.5
                @if(($summary['total_pending'] ?? 0) > 0) text-amber-900 @else text-slate-900 @endif">
                {{ $summary['total_pending'] ?? 0 }}
            </div>
            <div class="text-[11px] font-sans mt-1
                @if(($summary['total_pending'] ?? 0) > 0) text-amber-800 font-semibold @else text-slate-500 @endif">
                @if(($summary['total_pending'] ?? 0) > 0)
                    Ada pelamar baru menunggu keputusan Anda!
                @else
                    Semua berkas telah ditinjau
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Katalog & Permintaan Pelamar -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-heading font-bold text-sm text-slate-800">Daftar Katalog Rekrutmen Terbuka</h2>
            <span class="text-xs font-sans text-slate-500">Sinkronisasi status real-time</span>
        </div>
        <table class="w-full text-left text-sm font-sans">
            <thead class="bg-slate-100/70 text-slate-700 border-b border-slate-200 text-xs font-heading font-semibold uppercase tracking-wider">
                <tr>
                    <th class="p-4">Judul Rekrutmen</th>
                    <th class="p-4">Batas Pendaftaran</th>
                    <th class="p-4">Status Moderasi</th>
                    <th class="p-4">Divisi & Kuota</th>
                    <th class="p-4">Pelamar Masuk</th>
                    <th class="p-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($catalogs as $catalog)
                    @php
                        $stat = $catalogStats[$catalog->id] ?? ['total_quota' => 0, 'total_applicants' => 0, 'pending_applicants' => 0, 'accepted_applicants' => 0];
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4">
                            <div class="font-heading font-bold text-slate-900">{{ $catalog->title }}</div>
                            <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $catalog->description }}</div>
                        </td>
                        <td class="p-4 text-xs text-slate-600 whitespace-nowrap">
                            <div>{{ $catalog->deadline->format('d M Y H:i') }} WIB</div>
                            @if($catalog->deadline->isPast())
                                <span class="text-rose-600 text-[11px] font-heading font-bold">Ditutup</span>
                            @endif
                        </td>
                        <td class="p-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-heading font-bold
                                @if($catalog->status === 'published') bg-emerald-100 text-emerald-800
                                @elseif($catalog->status === 'pending_approval') bg-amber-100 text-amber-800
                                @elseif($catalog->status === 'draft') bg-slate-100 text-slate-800
                                @elseif($catalog->status === 'rejected') bg-rose-100 text-rose-800
                                @else bg-slate-200 text-slate-700 @endif">
                                @if($catalog->status === 'published') Published (Aktif)
                                @elseif($catalog->status === 'pending_approval') Menunggu Review Admin
                                @elseif($catalog->status === 'draft') Draft PIC
                                @elseif($catalog->status === 'rejected') Ditolak Admin
                                @else Ditutup @endif
                            </span>
                            @if($catalog->status === 'rejected' && $catalog->rejection_note)
                                <div class="text-[11px] text-rose-700 mt-1 max-w-xs">{{ $catalog->rejection_note }}</div>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-slate-600 whitespace-nowrap">
                            <div class="font-semibold text-slate-800">{{ $catalog->divisions->count() }} Divisi</div>
                            <div class="text-slate-500">Kuota: {{ $stat['total_quota'] }} Mahasiswa</div>
                        </td>
                        <td class="p-4 whitespace-nowrap">
                            <div class="space-y-1">
                                @if($stat['pending_applicants'] > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-heading font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span>{{ $stat['pending_applicants'] }} Baru Menunggu</span>
                                    </span>
                                @else
                                    <span class="text-xs font-sans text-slate-400">Tidak ada pending</span>
                                @endif
                                <div class="text-[11px] text-slate-500">
                                    {{ $stat['accepted_applicants'] }} diterima / {{ $stat['total_applicants'] }} total pelamar
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('pic.catalogs.applications.index', $catalog->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#083d62] text-white hover:bg-[#052942] rounded-lg text-xs font-heading font-semibold transition shadow-sm">
                                    <span>Review Pelamar</span>
                                    @if($stat['pending_applicants'] > 0)
                                        <span class="w-4 h-4 rounded-full bg-amber-400 text-slate-900 text-[10px] font-bold flex items-center justify-center">
                                            {{ $stat['pending_applicants'] }}
                                        </span>
                                    @endif
                                </a>
                                <a href="{{ route('pic.catalogs.show', $catalog->id) }}" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-heading font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    Kelola Divisi
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 font-sans">
                            Belum ada katalog rekrutmen. Silakan buat katalog baru untuk memulai OPREC tim Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
