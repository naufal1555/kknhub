@extends('layouts.ugm')

@section('title', 'Riwayat Lamaran Rekrutmen KKN - KKNHub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Riwayat Lamaran Rekrutmen KKN</h1>
            <p class="text-slate-500 font-sans text-sm">Pantau status seleksi berkas pendaftaran dan pembatalan otomatis secara berkala.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('catalog.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-heading font-semibold bg-white text-slate-700 hover:bg-slate-100 shadow-sm">
                Lihat Katalog OPREC
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

    {{-- Kartu Sinkronisasi Status Pengajuan Tim KKN / PIC --}}
    @if(!empty($picRegistration))
        <div class="border rounded-2xl p-5 shadow-sm space-y-3
            @if($picRegistration->status === 'approved') bg-emerald-50/70 border-emerald-300
            @elseif($picRegistration->status === 'pending') bg-amber-50/70 border-amber-300
            @else bg-rose-50/70 border-rose-300 @endif">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b
                @if($picRegistration->status === 'approved') border-emerald-200
                @elseif($picRegistration->status === 'pending') border-amber-200
                @else border-rose-200 @endif">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-heading font-bold uppercase
                        @if($picRegistration->status === 'approved') bg-emerald-600 text-white
                        @elseif($picRegistration->status === 'pending') bg-amber-500 text-white
                        @else bg-rose-600 text-white @endif">
                        @if($picRegistration->status === 'approved') Disetujui Admin
                        @elseif($picRegistration->status === 'pending') Menunggu Verifikasi Admin
                        @else Ditolak Admin @endif
                    </span>
                    <h2 class="font-heading font-bold text-slate-900 text-sm">Status Pengajuan Tim KKN Saya</h2>
                </div>
                <div class="text-[11px] font-sans text-slate-500">
                    Diajukan pada {{ $picRegistration->created_at->format('d M Y H:i') }} WIB
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="space-y-1">
                    <div class="text-base font-heading font-bold text-slate-900">{{ $picRegistration->proposed_team_name }}</div>
                    <div class="text-xs font-sans text-slate-600">
                        Lokasi Target: <span class="font-semibold text-slate-800">{{ $picRegistration->target_location }}</span> | Periode: <span class="font-semibold text-slate-800">{{ $picRegistration->period }}</span>
                    </div>
                    @if($picRegistration->status === 'approved')
                        <p class="text-xs font-sans text-emerald-800 mt-1">
                            Selamat! Proposal tim Anda telah disetujui oleh Administrator DPkM. Anda kini aktif sebagai PIC tim dan dapat mulai membuat katalog rekrutmen terbuka.
                        </p>
                    @elseif($picRegistration->status === 'pending')
                        <p class="text-xs font-sans text-amber-800 mt-1">
                            Pengajuan tim Anda sedang dalam proses verifikasi oleh Administrator DPkM UGM. Status approval akan diperbarui secara otomatis setelah diverifikasi.
                        </p>
                    @elseif($picRegistration->status === 'rejected')
                        <p class="text-xs font-sans text-rose-800 mt-1">
                            Alasan Penolakan: {{ $picRegistration->rejection_reason ?? 'Kualifikasi proposal belum sesuai.' }}
                        </p>
                    @endif
                </div>

                <div class="flex-shrink-0">
                    @if($picRegistration->status === 'approved')
                        <a href="{{ route('pic.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#083d62] hover:bg-[#052942] text-white rounded-xl text-xs font-heading font-bold shadow-sm transition">
                            <span>Buka Dasbor PIC Tim</span>
                            <svg class="w-3.5 h-3.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </a>
                    @elseif($picRegistration->status === 'rejected')
                        <a href="{{ route('pic.register') }}" class="inline-flex items-center gap-1 px-4 py-2 border border-rose-300 hover:bg-rose-100 text-rose-800 rounded-xl text-xs font-heading font-bold transition">
                            Ajukan Ulang Tim KKN
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm font-sans">
            <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 text-xs font-heading font-semibold uppercase tracking-wider">
                <tr>
                    <th class="p-4">Tim KKN dan Lokasi</th>
                    <th class="p-4">Divisi Dilamar</th>
                    <th class="p-4">Berkas Lampiran</th>
                    <th class="p-4">Waktu Pengajuan</th>
                    <th class="p-4">Status Seleksi</th>
                    <th class="p-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($applications as $app)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4">
                            <div class="font-heading font-bold text-slate-900">{{ $app->team->name }}</div>
                            <div class="text-xs text-slate-500">{{ $app->team->target_location }} ({{ $app->team->period }})</div>
                        </td>
                        <td class="p-4">
                            <span class="font-medium text-slate-800">{{ $app->division->name }}</span>
                        </td>
                        <td class="p-4 text-xs space-y-1.5">
                            @foreach($app->documents as $doc)
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('applications.documents.download', [$doc->id, 'download' => 1]) }}" download class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-[#083d62] text-slate-700 hover:text-white border border-slate-200 rounded-lg text-xs font-heading font-semibold transition" title="Unduh {{ strtoupper($doc->document_type) }}">
                                        <svg class="w-3 h-3 fill-none stroke-current stroke-2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh {{ strtoupper($doc->document_type) }}</span>
                                    </a>
                                    <a href="{{ route('applications.documents.download', $doc->id) }}" target="_blank" class="text-[11px] text-slate-400 hover:text-[#083d62] hover:underline">
                                        Lihat
                                    </a>
                                </div>
                            @endforeach
                        </td>
                        <td class="p-4 text-xs text-slate-500">
                            {{ $app->submitted_at ? $app->submitted_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="p-4">
                            <div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-heading font-bold
                                    @if($app->status === 'accepted') bg-emerald-100 text-emerald-800
                                    @elseif($app->status === 'pending') bg-amber-100 text-amber-800
                                    @elseif($app->status === 'rejected') bg-rose-100 text-rose-800
                                    @elseif($app->status === 'cancelled') bg-slate-200 text-slate-600
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ strtoupper($app->status) }}
                                </span>
                            </div>
                            @if($app->notes)
                                <div class="text-[11px] text-slate-500 mt-1 max-w-xs">{{ $app->notes }}</div>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            @if($app->status === 'pending')
                                <form action="{{ route('applications.cancel', $app->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan lamaran ini?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 border border-rose-300 text-rose-600 hover:bg-rose-50 text-xs font-heading font-semibold rounded-lg transition">
                                        Batalkan
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400 font-sans">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 font-sans">
                            Anda belum mengajukan lamaran ke tim KKN mana pun.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $applications->links() }}
    </div>
</div>
@endsection
