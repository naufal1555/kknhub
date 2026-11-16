@extends('layouts.ugm')

@section('title', 'Review & Seleksi Pelamar - ' . $catalog->title . ' - KKNHub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('pic.catalogs.show', $catalog->id) }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:underline mb-1 inline-block">&larr; Kembali ke Detail Rekrutmen</a>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Review & Seleksi Pelamar KKN</h1>
            <p class="text-slate-500 font-sans text-xs">Tim: <span class="font-heading font-bold text-slate-800">{{ $team->name }}</span> | Rekrutmen: <span class="font-semibold text-slate-700">{{ $catalog->title }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pic.catalogs.index') }}" class="px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-heading font-semibold bg-white text-slate-700 hover:bg-slate-50 shadow-sm transition">
                Daftar Rekrutmen Tim
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

    <!-- Monitoring Kuota Divisi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($quotas as $divId => $q)
            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="text-[11px] font-heading font-bold uppercase text-slate-500 tracking-wider truncate">{{ $q['name'] }}</div>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-heading font-bold text-slate-900">{{ $q['accepted'] }} <span class="text-sm font-normal text-slate-400">/ {{ $q['quota'] }}</span></span>
                    <span class="text-xs font-heading font-bold px-2.5 py-0.5 rounded-full {{ $q['remaining'] === 0 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $q['remaining'] === 0 ? 'Penuh' : 'Sisa ' . $q['remaining'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-wrap gap-4 items-center">
        <form action="{{ route('pic.catalogs.applications.index', $catalog->id) }}" method="GET" class="flex flex-wrap gap-3 items-center w-full">
            <div class="flex items-center gap-2">
                <label class="text-xs font-heading font-bold text-slate-600">Filter Divisi:</label>
                <select name="division_id" onchange="this.form.submit()" class="text-xs font-sans border border-slate-300 rounded-xl px-3 py-1.5 bg-slate-50 focus:outline-none">
                    <option value="">Semua Divisi</option>
                    @foreach($catalog->divisions as $div)
                        <option value="{{ $div->id }}" {{ (string)$selectedDivision === (string)$div->id ? 'selected' : '' }}>
                            {{ $div->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-heading font-bold text-slate-600">Status Seleksi:</label>
                <select name="status" onchange="this.form.submit()" class="text-xs font-sans border border-slate-300 rounded-xl px-3 py-1.5 bg-slate-50 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ $selectedStatus === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            @if($selectedDivision || $selectedStatus)
                <a href="{{ route('pic.catalogs.applications.index', $catalog->id) }}" class="text-xs font-heading font-semibold text-rose-600 hover:underline">Reset Filter</a>
            @endif
        </form>
    </div>

    <!-- Tabel Pelamar -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm font-sans">
            <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 text-xs font-heading font-semibold uppercase tracking-wider">
                <tr>
                    <th class="p-4">Nama Mahasiswa & NIM</th>
                    <th class="p-4">Fakultas / Prodi</th>
                    <th class="p-4">Divisi</th>
                    <th class="p-4">Berkas Lampiran</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Keputusan Seleksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($applications as $app)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4">
                            <div class="font-heading font-bold text-slate-900">{{ $app->user->name }}</div>
                            <div class="text-xs text-slate-500 font-sans">{{ $app->user->email }}</div>
                            <div class="text-xs font-mono text-slate-600">{{ $app->user->nim ?? '-' }}</div>
                        </td>
                        <td class="p-4 text-xs font-sans text-slate-600">
                            <div>{{ $app->user->faculty ?? '-' }}</div>
                            <div class="text-slate-400">{{ $app->user->major ?? '-' }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-heading font-semibold text-slate-700">
                                {{ $app->division->name }}
                            </span>
                        </td>
                        <td class="p-4 text-xs space-y-2">
                            @forelse($app->documents as $doc)
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1.5">
                                    <a href="{{ route('applications.documents.download', [$doc->id, 'download' => 1]) }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#083d62] hover:bg-[#052942] text-white rounded-lg text-xs font-heading font-bold shadow-sm transition whitespace-nowrap" title="Unduh berkas {{ strtoupper($doc->document_type) }} pelamar">
                                        <svg class="w-3.5 h-3.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh {{ strtoupper($doc->document_type) }}</span>
                                    </a>
                                    <a href="{{ route('applications.documents.download', $doc->id) }}" target="_blank" class="text-[11px] text-slate-500 hover:text-[#083d62] hover:underline font-sans inline-block" title="Buka PDF di tab baru">
                                        Lihat
                                    </a>
                                </div>
                            @empty
                                <span class="text-xs text-slate-400 italic">Tidak ada berkas</span>
                            @endforelse
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-heading font-bold
                                @if($app->status === 'accepted') bg-emerald-100 text-emerald-800
                                @elseif($app->status === 'pending') bg-amber-100 text-amber-800
                                @elseif($app->status === 'rejected') bg-rose-100 text-rose-800
                                @elseif($app->status === 'cancelled') bg-slate-200 text-slate-600
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ strtoupper($app->status) }}
                            </span>
                            @if($app->notes)
                                <div class="text-[11px] font-sans text-slate-500 mt-1 max-w-xs">{{ $app->notes }}</div>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            @if($app->status === 'pending')
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('pic.applications.accept', $app->id) }}" method="POST" onsubmit="return confirm('Terima mahasiswa ini ke divisi {{ $app->division->name }}? Seluruh lamaran pending kandidat di tim lain akan otomatis dibatalkan.')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-heading font-bold rounded-xl shadow-sm transition">
                                            Terima (Accept)
                                        </button>
                                    </form>
                                    <button onclick="document.getElementById('reject-box-{{ $app->id }}').classList.toggle('hidden')" class="px-3 py-1.5 border border-rose-300 text-rose-600 hover:bg-rose-50 text-xs font-heading font-semibold rounded-xl transition">
                                        Tolak
                                    </button>
                                </div>
                                <div id="reject-box-{{ $app->id }}" class="hidden mt-2 text-left bg-slate-50 p-2.5 border rounded-xl">
                                    <form action="{{ route('pic.applications.reject', $app->id) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="text" name="notes" placeholder="Catatan alasan penolakan (opsional)..." class="w-full text-xs p-1.5 border border-slate-300 rounded-lg">
                                        <button type="submit" class="px-3 py-1 bg-rose-600 text-white text-xs font-heading font-semibold rounded-lg">Konfirmasi Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs font-sans text-slate-400">Keputusan Final</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500 font-sans">
                            Belum ada berkas pendaftaran yang masuk pada katalog ini.
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
