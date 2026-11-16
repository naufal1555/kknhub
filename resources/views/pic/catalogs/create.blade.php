@extends('layouts.ugm')

@section('title', 'Buat Draf Katalog OPREC - KKNHub UGM')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div>
            <a href="{{ route('pic.catalogs.index') }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:underline mb-2 inline-block">
                &larr; Kembali ke Daftar Katalog
            </a>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Buat Draf Rekrutmen KKN</h1>
            <p class="text-xs font-sans text-slate-500 mt-1">Buat draf proposal pembukaan rekrutmen terbuka untuk diajukan ke administrator.</p>
        </div>

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-sans">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pic.catalogs.store') }}" method="POST" class="space-y-4 font-sans text-xs">
            @csrf
            <div>
                <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Judul Rekrutmen *</label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: Rekrutmen Terbuka Tim KKN-PPM Seyegan 2026" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
            </div>
            <div>
                <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Kegiatan & Gambaran Tim</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]" placeholder="Jelaskan fokus tema pengabdian, gambaran kegiatan, dan kriteria umum...">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Mulai (Opsional)</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Batas Akhir Pendaftaran *</label>
                    <input type="datetime-local" name="deadline" required value="{{ old('deadline') }}" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-[#0c365b] hover:bg-[#08253f] text-white font-heading font-bold rounded-xl text-xs transition shadow-md">
                Simpan Sebagai Draf
            </button>
        </form>
    </div>
</div>
@endsection
