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
                        <td class="p-4 text-xs space-y-1">
                            @foreach($app->documents as $doc)
                                <div>
                                    <a href="{{ route('applications.documents.download', $doc->id) }}" target="_blank" class="text-[#0c365b] font-heading font-semibold hover:underline flex items-center gap-1">
                                        <span class="px-1.5 py-0.5 bg-slate-100 border rounded text-[10px] text-slate-700">PDF</span>
                                        <span>{{ strtoupper($doc->document_type) }}</span>
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
