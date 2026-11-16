@extends('layouts.ugm')

@section('title', 'Moderasi Katalog OPREC - Admin KKNHub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Admin: Moderasi Katalog OPREC KKN</h1>
            <p class="text-slate-500 font-sans text-xs">Tinjau proposal rekrutmen terbuka sebelum dipublikasikan ke mahasiswa.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pic-registrations.index') }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-heading font-semibold bg-white text-slate-700 hover:bg-slate-50 shadow-sm transition">
                Verifikasi PIC
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

    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3 font-heading text-xs">
        <a href="{{ route('admin.catalogs.index') }}" class="px-3.5 py-1.5 rounded-xl font-semibold {{ empty($currentStatus) ? 'bg-[#0c365b] text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' }}">
            Semua Status
        </a>
        <a href="{{ route('admin.catalogs.index', ['status' => 'pending_approval']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold {{ $currentStatus === 'pending_approval' ? 'bg-amber-600 text-white' : 'bg-white border border-slate-200 text-amber-800 hover:bg-amber-50' }}">
            Menunggu Persetujuan (Pending)
        </a>
        <a href="{{ route('admin.catalogs.index', ['status' => 'published']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold {{ $currentStatus === 'published' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-emerald-800 hover:bg-emerald-50' }}">
            Published
        </a>
        <a href="{{ route('admin.catalogs.index', ['status' => 'rejected']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold {{ $currentStatus === 'rejected' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-rose-800 hover:bg-rose-50' }}">
            Ditolak (Rejected)
        </a>
        <a href="{{ route('admin.catalogs.index', ['status' => 'draft']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold {{ $currentStatus === 'draft' ? 'bg-slate-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
            Draft PIC
        </a>
        <a href="{{ route('admin.catalogs.index', ['status' => 'closed']) }}" class="px-3.5 py-1.5 rounded-xl font-semibold {{ $currentStatus === 'closed' ? 'bg-slate-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
            Ditutup (Closed)
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm font-sans">
            <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 text-xs font-heading font-semibold uppercase tracking-wider">
                <tr>
                    <th class="p-4">Judul Rekrutmen dan Tim</th>
                    <th class="p-4">PIC Pengaju</th>
                    <th class="p-4">Divisi dan Total Kuota</th>
                    <th class="p-4">Batas Waktu</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($catalogs as $catalog)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4">
                            <div class="font-heading font-bold text-slate-900">{{ $catalog->title }}</div>
                            <div class="text-xs text-slate-500">{{ $catalog->team->name }} ({{ $catalog->team->target_location }})</div>
                        </td>
                        <td class="p-4">
                            <div class="font-heading font-semibold text-slate-800">{{ $catalog->team->pic->name ?? '-' }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $catalog->team->pic->email ?? '-' }}</div>
                        </td>
                        <td class="p-4 text-xs text-slate-600">
                            <div>{{ $catalog->divisions->count() }} Divisi</div>
                            <div class="font-heading font-bold text-slate-800">Total Kuota: {{ $catalog->divisions->sum('quota') }}</div>
                        </td>
                        <td class="p-4 text-xs text-slate-600">
                            <div>{{ $catalog->deadline ? $catalog->deadline->format('d M Y H:i') : '-' }} WIB</div>
                            @if($catalog->deadline && $catalog->deadline->isPast())
                                <span class="text-rose-600 font-heading font-bold text-[11px]">Lewat batas waktu</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-heading font-bold
                                @if($catalog->status === 'published') bg-emerald-100 text-emerald-800
                                @elseif($catalog->status === 'pending_approval') bg-amber-100 text-amber-800
                                @elseif($catalog->status === 'rejected') bg-rose-100 text-rose-800
                                @elseif($catalog->status === 'closed') bg-slate-200 text-slate-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ strtoupper(str_replace('_', ' ', $catalog->status)) }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            @if($catalog->status === 'pending_approval')
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.catalogs.show', $catalog->id) }}" class="inline-block px-3 py-1.5 border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-heading font-semibold rounded-lg transition">
                                        Tinjau
                                    </a>
                                    <form action="{{ route('admin.catalogs.approve', $catalog->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-heading font-bold rounded-lg shadow-sm transition">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            @elseif($catalog->status === 'published')
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('catalog.show', $catalog->id) }}" class="inline-block px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-heading font-semibold rounded-lg transition">
                                        Lihat di Publik
                                    </a>
                                    <a href="{{ route('admin.catalogs.show', $catalog->id) }}" class="inline-block px-3 py-1.5 bg-[#083d62] hover:bg-[#052942] text-white text-xs font-heading font-bold rounded-lg shadow-sm transition">
                                        Detail
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('admin.catalogs.show', $catalog->id) }}" class="inline-block px-3.5 py-1.5 bg-[#083d62] hover:bg-[#052942] text-white text-xs font-heading font-bold rounded-lg shadow-sm transition">
                                    Tinjau Proposal
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 font-sans">
                            Tidak ada katalog OPREC dalam filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $catalogs->links() }}
    </div>
</div>
@endsection
