@extends('layouts.ugm')

@section('title', 'Edit Katalog - ' . $catalog->title . ' - KKNHub')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div>
            <a href="{{ route('pic.catalogs.show', $catalog->id) }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:underline mb-2 inline-block">
                &larr; Kembali ke Detail Rekrutmen
            </a>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Edit Informasi Rekrutmen</h1>
            <p class="text-xs font-sans text-slate-500 mt-1">Perbarui judul, batas pendaftaran, dan informasi program.</p>
        </div>

        @if($catalog->status === 'published')
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs font-sans">
                <span class="font-heading font-bold block mb-1">Perhatian Aturan Bisnis (FR-09):</span>
                Katalog ini saat ini berstatus PUBLISHED. Menyimpan perubahan akan secara otomatis mengembalikan status katalog menjadi PENDING APPROVAL untuk diverifikasi ulang oleh administrator sebelum ditampilkan kembali ke mahasiswa.
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-sans">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pic.catalogs.update', $catalog->id) }}" method="POST" class="space-y-4 font-sans text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Rekrutmen *</label>
                <input type="text" name="title" required value="{{ old('title', $catalog->title) }}" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
            </div>
            <div>
                <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Kegiatan Tim</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">{{ old('description', $catalog->description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Mulai (Opsional)</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date', $catalog->start_date?->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Batas Akhir Pendaftaran *</label>
                    <input type="datetime-local" name="deadline" required value="{{ old('deadline', $catalog->deadline->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-[#0c365b] hover:bg-[#08253f] text-white font-heading font-bold rounded-xl text-xs transition shadow-md">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
