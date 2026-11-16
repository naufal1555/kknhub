@extends('layouts.ugm')

@section('title', 'Dasbor PIC Tim KKN - KKNHub UGM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Dasbor PIC Tim: {{ $team->name }}</h1>
            <p class="text-slate-500 font-sans text-sm">Lokasi: {{ $team->target_location }} | Periode: {{ $team->period }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pic.team.edit') }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-heading font-semibold text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition">
                Profil Tim & Narahubung
            </a>
            <a href="{{ route('pic.catalogs.create') }}" class="px-4 py-2 bg-[#0c365b] hover:bg-[#08253f] text-white rounded-xl text-xs font-heading font-bold shadow-sm transition">
                Buat Katalog Baru
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

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm font-sans">
            <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 text-xs font-heading font-semibold uppercase tracking-wider">
                <tr>
                    <th class="p-4">Judul Rekrutmen</th>
                    <th class="p-4">Batas Pendaftaran</th>
                    <th class="p-4">Status Moderasi</th>
                    <th class="p-4">Jumlah Divisi</th>
                    <th class="p-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($catalogs as $catalog)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4 font-heading font-bold text-slate-900">{{ $catalog->title }}</td>
                        <td class="p-4 text-xs text-slate-600">{{ $catalog->deadline->format('d M Y H:i') }} WIB</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-heading font-bold
                                @if($catalog->status === 'published') bg-emerald-100 text-emerald-800
                                @elseif($catalog->status === 'pending_approval') bg-amber-100 text-amber-800
                                @elseif($catalog->status === 'draft') bg-slate-100 text-slate-800
                                @elseif($catalog->status === 'rejected') bg-rose-100 text-rose-800
                                @else bg-slate-200 text-slate-700 @endif">
                                {{ strtoupper(str_replace('_', ' ', $catalog->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-xs text-slate-600">{{ $catalog->divisions->count() }} Divisi</td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('pic.catalogs.applications.index', $catalog->id) }}" class="px-3 py-1.5 bg-[#0c365b] text-white hover:bg-[#08253f] rounded-lg text-xs font-heading font-semibold transition">
                                    Review Pelamar
                                </a>
                                <a href="{{ route('pic.catalogs.show', $catalog->id) }}" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-heading font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    Kelola Divisi
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500 font-sans">
                            Belum ada katalog rekrutmen. Silakan buat katalog baru untuk memulai OPREC.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
