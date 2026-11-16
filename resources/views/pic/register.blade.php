@extends('layouts.ugm')

@section('title', 'Pengajuan Tim KKN Baru - KKNHub UGM')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div>
            <a href="{{ route('catalog.index') }}" class="text-xs font-heading font-semibold text-[#083d62] hover:underline mb-2 inline-flex items-center gap-1">
                &larr; Kembali ke Katalog OPREC
            </a>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Pengajuan Tim KKN & Registrasi PIC</h1>
            <p class="text-xs font-sans text-slate-500 mt-1">
                Daftarkan tim KKN Anda dari wilayah mana saja di Indonesia. Pengajuan akan diverifikasi oleh Administrator DPkM UGM sebelum Anda dapat mempublikasikan katalog rekrutmen.
            </p>
        </div>

        @if($existingPending)
            <div class="p-5 bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl text-xs font-sans space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-amber-500 text-white font-heading font-bold rounded text-[10px] uppercase">Status Pending</span>
                    <h2 class="font-heading font-bold text-sm text-amber-900">Pengajuan Tim Anda Sedang Menunggu Verifikasi</h2>
                </div>
                <p class="text-xs text-amber-800">
                    Tim: <strong>{{ $existingPending->proposed_team_name }}</strong> (Lokasi: {{ $existingPending->target_location }} | {{ $existingPending->period }}).
                </p>
                <p class="text-[11px] text-amber-700">
                    Administrator DPkM sedang meninjau proposal Anda. Anda akan mendapatkan notifikasi status setelah disetujui.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('applications.history') }}" class="block text-center flex-1 py-2.5 bg-[#083d62] text-white rounded-xl text-xs font-heading font-bold hover:bg-[#052942] transition">
                    Lihat Status di Riwayat Saya
                </a>
                <a href="{{ route('catalog.index') }}" class="block text-center py-2.5 px-4 border border-slate-300 rounded-xl text-xs font-heading font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Kembali ke Katalog
                </a>
            </div>
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
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Tim KKN yang Diajukan *</label>
                    <input type="text" name="proposed_team_name" required value="{{ old('proposed_team_name') }}" placeholder="Contoh: KKN-PPM Unit Dlingo Bantul / Unit Sembalun Lombok" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                    <p class="text-[11px] text-slate-500 mt-1">Terbuka untuk unit KKN dari daerah mana saja di Indonesia.</p>
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Lokasi Target Pengabdian *</label>
                    <input type="text" name="target_location" required value="{{ old('target_location') }}" placeholder="Contoh: Dlingo, Bantul, D.I. Yogyakarta / Misool, Raja Ampat, Papua Barat Daya" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Periode KKN *</label>
                    <input type="text" name="period" required value="{{ old('period', 'Antar Semester 2026') }}" placeholder="Contoh: Antar Semester 2026" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi Singkat Rencana Program</label>
                    <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]" placeholder="Fokus kegiatan pengabdian masyarakat, tema keilmuan, dan sasaran kemitraan warga...">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-[#083d62] hover:bg-[#052942] text-white font-heading font-bold rounded-xl text-xs transition shadow-sm">
                    Kirim Pengajuan Tim ke Administrator
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
