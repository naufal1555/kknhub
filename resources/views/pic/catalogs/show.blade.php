@extends('layouts.ugm')

@section('title', 'Kelola Divisi - ' . $catalog->title . ' - KKNHub PIC')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
        <div>
            <a href="{{ route('pic.catalogs.index') }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:underline mb-1 inline-block">&larr; Kembali ke Daftar Katalog</a>
            <h1 class="text-2xl font-heading font-bold text-slate-900 mt-1">{{ $catalog->title }}</h1>
            <p class="text-slate-500 font-sans text-xs">Batas Waktu: {{ $catalog->deadline->format('d M Y H:i') }} WIB | Status: <strong class="font-heading uppercase">{{ str_replace('_', ' ', $catalog->status) }}</strong></p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(in_array($catalog->status, ['draft', 'rejected']))
                <form action="{{ route('pic.catalogs.submit-approval', $catalog->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-heading font-bold shadow-sm transition">
                        Ajukan Persetujuan Admin
                    </button>
                </form>
            @elseif($catalog->status === 'published')
                <form action="{{ route('pic.catalogs.close', $catalog->id) }}" method="POST" onsubmit="return confirm('Tutup katalog OPREC ini?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-heading font-bold shadow-sm transition">
                        Tutup Katalog
                    </button>
                </form>
            @endif
            <a href="{{ route('pic.catalogs.applications.index', $catalog->id) }}" class="px-4 py-2 bg-[#0c365b] hover:bg-[#08253f] text-white rounded-xl text-xs font-heading font-bold shadow-sm transition">
                Review Pelamar
            </a>
            <a href="{{ route('pic.catalogs.edit', $catalog->id) }}" class="px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-heading font-semibold text-slate-700 hover:bg-slate-50 bg-white transition shadow-sm">
                Edit Info
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
    @if($catalog->status === 'rejected' && $catalog->rejection_note)
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-sans">
            <span class="font-heading font-bold text-rose-900 block mb-1">Catatan Penolakan Administrator:</span>
            {{ $catalog->rejection_note }}
        </div>
    @endif

    <!-- Daftar Divisi & Kuota -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-heading font-bold text-slate-900">Divisi & Alokasi Kuota</h2>
                <p class="text-xs font-sans text-slate-500">Tentukan rincian formasi dan batas kapasitas penerimaan mahasiswa.</p>
            </div>
            <span class="text-xs font-heading font-semibold bg-slate-100 text-slate-700 px-3 py-1 rounded-full border border-slate-200">
                Total Kuota: {{ $catalog->divisions->sum('quota') }} Mahasiswa
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($catalog->divisions as $div)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-bold text-sm text-slate-900">{{ $div->name }}</span>
                            <span class="text-xs font-heading font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-[#0c365b] border border-indigo-200">
                                Kuota: {{ $div->quota }}
                            </span>
                            <span class="text-xs font-sans text-slate-500">
                                (Diterima: {{ $div->acceptedCount() }})
                            </span>
                        </div>
                        @if($div->job_description)
                            <p class="text-xs font-sans text-slate-600 mt-1.5 leading-relaxed">{{ $div->job_description }}</p>
                        @endif
                    </div>
                    @if($catalog->status !== 'closed')
                        <form action="{{ route('pic.divisions.destroy', [$catalog->id, $div->id]) }}" method="POST" onsubmit="return confirm('Hapus divisi ini?');" class="self-end sm:self-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-heading font-semibold text-rose-600 hover:underline">
                                Hapus Divisi
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-sm font-sans text-slate-500 py-6 text-center">
                    Belum ada divisi yang didaftarkan. Tambahkan minimal satu divisi agar proposal katalog dapat diajukan ke administrator.
                </p>
            @endforelse
        </div>

        @if($catalog->status !== 'closed')
            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-heading font-bold text-slate-900 mb-3">Tambah Divisi Baru</h3>
                <form action="{{ route('pic.divisions.store', $catalog->id) }}" method="POST" class="space-y-4 font-sans text-xs">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Divisi *</label>
                            <input type="text" name="name" required placeholder="Contoh: Divisi Publikasi & Dokumentasi" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                        </div>
                        <div>
                            <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Kuota (Orang) *</label>
                            <input type="number" name="quota" min="1" required placeholder="1" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                        </div>
                    </div>
                    <div>
                        <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Tugas dan Tanggung Jawab</label>
                        <input type="text" name="job_description" placeholder="Rincian lingkup tugas divisi..." class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                    </div>
                    <div>
                        <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Kualifikasi Khusus (Opsional)</label>
                        <input type="text" name="requirements" placeholder="Keahlian atau latar belakang jurusan yang diharapkan..." class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                    </div>
                    <button type="submit" class="py-2.5 px-5 bg-[#0c365b] hover:bg-[#08253f] text-white text-xs font-heading font-bold rounded-xl transition shadow-sm">
                        Simpan Divisi
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
