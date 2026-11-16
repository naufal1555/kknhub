@extends('layouts.ugm')

@section('title', 'Pengajuan PIC Tim KKN - KKNHub UGM')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div>
            <a href="{{ route('catalog.index') }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:underline mb-2 inline-block">
                &larr; Kembali ke Katalog OPREC
            </a>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Pengajuan Hak Akses PIC Tim KKN</h1>
            <p class="text-xs font-sans text-slate-500 mt-1">Daftarkan tim KKN Anda untuk diverifikasi oleh Administrator sebelum membuka rekrutmen terbuka.</p>
        </div>

        @if($existingPending)
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm font-sans space-y-2">
                <p class="font-heading font-bold text-amber-900">Pengajuan Anda Sedang Menunggu Verifikasi</p>
                <p class="text-xs">Tim yang diajukan: <strong>{{ $existingPending->proposed_team_name }}</strong> ({{ $existingPending->target_location }}).</p>
                <p class="text-[11px] text-amber-700">Administrator sedang meninjau kelayakan berkas pengajuan Anda.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="block text-center w-full py-2.5 border border-slate-300 rounded-xl text-xs font-heading font-semibold text-slate-700 hover:bg-slate-50">
                Kembali ke Beranda
            </a>
        @else
            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-sans">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pic.register.post') }}" method="POST" class="space-y-4 font-sans text-xs">
                @csrf
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Tim KKN *</label>
                    <input type="text" name="proposed_team_name" required value="{{ old('proposed_team_name') }}" placeholder="Contoh: KKN-PPM Unit Sleman Barat" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Lokasi Target Pengabdian *</label>
                    <input type="text" name="target_location" required value="{{ old('target_location') }}" placeholder="Contoh: Desa Seyegan, Sleman, D.I. Yogyakarta" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Periode KKN *</label>
                    <input type="text" name="period" required value="{{ old('period') }}" placeholder="Contoh: Periode 2 Tahun 2026" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Singkat Rencana Program</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]" placeholder="Fokus program kerja, tema pengabdian, dan sasaran kemitraan warga...">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-[#0c365b] hover:bg-[#08253f] text-white font-heading font-bold rounded-xl text-xs transition shadow-md">
                    Kirim Pengajuan ke Administrator
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
