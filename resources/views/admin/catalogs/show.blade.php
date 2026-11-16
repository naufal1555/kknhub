@extends('layouts.ugm')

@section('title', 'Detail Moderasi Katalog - ' . $catalog->title . ' - Admin KKNHub')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.catalogs.index') }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:underline flex items-center gap-1">
            &larr; Kembali ke Daftar Moderasi
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-heading font-bold
            @if($catalog->status === 'published') bg-emerald-100 text-emerald-800
            @elseif($catalog->status === 'pending_approval') bg-amber-100 text-amber-800
            @elseif($catalog->status === 'rejected') bg-rose-100 text-rose-800
            @elseif($catalog->status === 'closed') bg-slate-200 text-slate-700
            @else bg-slate-100 text-slate-700 @endif">
            STATUS: {{ strtoupper(str_replace('_', ' ', $catalog->status)) }}
        </span>
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
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-sans">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($catalog->rejection_note)
        <div class="p-4 bg-rose-50 border border-rose-300 rounded-2xl font-sans">
            <h4 class="text-xs font-heading font-bold uppercase tracking-wider text-rose-800 mb-1">Catatan Penolakan Sebelumnya:</h4>
            <p class="text-sm text-rose-900 whitespace-pre-line">{{ $catalog->rejection_note }}</p>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-slate-900">{{ $catalog->title }}</h1>
            <p class="text-sm font-sans text-slate-500 mt-1">Diajukan oleh Tim: <span class="font-heading font-bold text-slate-800">{{ $catalog->team->name }}</span> | PIC: <span class="font-medium text-slate-700">{{ $catalog->team->pic->name ?? '-' }} ({{ $catalog->team->pic->email ?? '-' }})</span></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-sm font-sans">
            <div>
                <span class="text-xs font-heading font-bold text-slate-500 block uppercase">Lokasi KKN</span>
                <span class="font-medium text-slate-800">{{ $catalog->team->target_location }}</span>
            </div>
            <div>
                <span class="text-xs font-heading font-bold text-slate-500 block uppercase">Periode KKN</span>
                <span class="font-medium text-slate-800">{{ $catalog->team->period }}</span>
            </div>
            <div>
                <span class="text-xs font-heading font-bold text-slate-500 block uppercase">Batas Waktu Rekrutmen</span>
                <span class="font-medium text-slate-800">{{ $catalog->deadline ? $catalog->deadline->format('d M Y, H:i') . ' WIB' : '-' }}</span>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <span class="text-xs font-heading font-bold text-slate-500 block uppercase mb-1">Deskripsi & Ruang Lingkup Rekrutmen</span>
            <p class="text-sm font-sans text-slate-700 whitespace-pre-line leading-relaxed">{{ $catalog->description }}</p>
        </div>

        @if($catalog->team->theme)
            <div class="pt-2">
                <span class="text-xs font-heading font-bold text-slate-500 block uppercase mb-1">Tema Kegiatan KKN</span>
                <p class="text-sm font-sans text-slate-700">{{ $catalog->team->theme }}</p>
            </div>
        @endif

        @if($catalog->team->contactPersons && $catalog->team->contactPersons->isNotEmpty())
            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs font-heading font-bold text-slate-500 block uppercase mb-2">Kontak Narahubung Tim</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($catalog->team->contactPersons as $cp)
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-sans">
                            <span class="font-heading font-bold text-slate-800">{{ $cp->name }}</span>
                            <span class="text-slate-500">({{ $cp->role_label }})</span>
                            <div class="text-slate-600 mt-0.5 font-mono">{{ $cp->phone }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-heading font-bold text-slate-900 text-base">Daftar Divisi dan Kuota Rekrutmen</h3>
            <span class="text-xs font-heading font-semibold bg-slate-100 text-slate-700 px-3 py-1 rounded-full border border-slate-200">
                Total Kuota: {{ $catalog->divisions->sum('quota') }} Mahasiswa
            </span>
        </div>

        <table class="w-full text-left text-sm font-sans">
            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200 text-xs font-heading font-semibold uppercase">
                <tr>
                    <th class="p-3 font-semibold">Nama Divisi</th>
                    <th class="p-3 font-semibold">Deskripsi Tugas & Kualifikasi</th>
                    <th class="p-3 font-semibold text-center w-28">Kuota</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($catalog->divisions as $division)
                    <tr>
                        <td class="p-3 font-heading font-bold text-slate-900 align-top">{{ $division->name }}</td>
                        <td class="p-3 text-slate-600 text-xs align-top whitespace-pre-line">{{ $division->job_description ?: '-' }}</td>
                        <td class="p-3 text-center align-top font-heading font-bold text-slate-800">{{ $division->quota }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-slate-400 text-xs">Tidak ada divisi yang didaftarkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($catalog->status === 'pending_approval')
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-slate-900 text-base">Keputusan Moderasi Administrator</h3>
            <p class="text-xs font-sans text-slate-500">Setujui proposal untuk langsung menayangkan katalog ini di portal publik, atau tolak dengan menyertakan catatan evaluasi terperinci untuk diperbaiki oleh PIC.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <div class="p-5 border border-emerald-200 bg-emerald-50 rounded-2xl flex flex-col justify-between">
                    <div>
                        <h4 class="font-heading font-bold text-emerald-900 mb-1">Setujui Rekrutmen</h4>
                        <p class="text-xs font-sans text-emerald-700 mb-4">Katalog akan berstatus PUBLISHED dan dapat diakses publik oleh seluruh mahasiswa UGM.</p>
                    </div>
                    <form action="{{ route('admin.catalogs.approve', $catalog->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-heading font-bold text-xs shadow-sm transition">
                            Setujui & Publikasikan Sekarang
                        </button>
                    </form>
                </div>

                <div class="p-5 border border-rose-200 bg-rose-50 rounded-2xl">
                    <h4 class="font-heading font-bold text-rose-900 mb-1">Tolak Proposal Rekrutmen</h4>
                    <p class="text-xs font-sans text-rose-700 mb-3">Katalog akan berstatus REJECTED dan PIC akan menerima catatan evaluasi.</p>
                    <form action="{{ route('admin.catalogs.reject', $catalog->id) }}" method="POST" class="space-y-3 font-sans text-xs">
                        @csrf
                        <div>
                            <textarea name="rejection_note" required rows="3" placeholder="Masukkan alasan penolakan dan instruksi perbaikan..." class="w-full p-2.5 border border-rose-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-rose-500"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-heading font-bold text-xs shadow-sm transition">
                            Kirim Keputusan Penolakan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="p-4 bg-slate-100 rounded-2xl text-center text-xs font-sans text-slate-600">
            Katalog ini saat ini berstatus <span class="font-heading font-bold uppercase">{{ str_replace('_', ' ', $catalog->status) }}</span>. Aksi moderasi hanya aktif saat status PENDING APPROVAL.
        </div>
    @endif
</div>
@endsection
