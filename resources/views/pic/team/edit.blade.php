@extends('layouts.ugm')

@section('title', 'Profil Tim & Narahubung - KKNHub PIC')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('pic.dashboard') }}" class="text-xs font-heading font-semibold text-[#0c365b] hover:underline">&larr; Kembali ke Dasbor PIC</a>
            <h1 class="text-2xl font-heading font-bold text-slate-900 mt-1">Profil Tim & Narahubung</h1>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <!-- Profil Tim -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-lg font-heading font-bold text-slate-900 border-b pb-3">Informasi Utama Tim KKN</h2>
            <form action="{{ route('pic.team.update') }}" method="POST" class="space-y-4 font-sans text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Tim KKN *</label>
                    <input type="text" name="name" required value="{{ old('name', $team->name) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Lokasi Target Pengabdian *</label>
                    <input type="text" name="target_location" required value="{{ old('target_location', $team->target_location) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Periode KKN *</label>
                    <input type="text" name="period" required value="{{ old('period', $team->period) }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                </div>
                <div>
                    <label class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Deskripsi dan Visi Program</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">{{ old('description', $team->description) }}</textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#0c365b] hover:bg-[#08253f] text-white font-heading font-bold rounded-xl text-xs transition shadow-sm">
                    Perbarui Profil Tim
                </button>
            </form>
        </div>

        <!-- Contact Persons -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-lg font-heading font-bold text-slate-900 border-b pb-3">Daftar Narahubung Resmi</h2>
            <div class="divide-y divide-slate-100 mb-4">
                @forelse($team->contactPersons as $cp)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <span class="font-heading font-bold text-sm text-slate-900">{{ $cp->name }}</span>
                            <span class="text-xs font-sans text-slate-500">({{ $cp->role_label }})</span>
                            <div class="text-xs font-sans text-slate-600 mt-0.5">Telepon/WA: {{ $cp->phone_number }} @if($cp->line_id) | Line: {{ $cp->line_id }} @endif</div>
                        </div>
                        <form action="{{ route('pic.team.contacts.destroy', $cp->id) }}" method="POST" onsubmit="return confirm('Hapus narahubung ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-heading font-semibold text-rose-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs font-sans text-slate-500 py-3">Belum ada narahubung terdaftar. Tambahkan kontak di bawah.</p>
                @endforelse
            </div>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="text-xs font-heading font-bold uppercase tracking-wider text-slate-700 mb-2">Tambah Narahubung Baru</h3>
                <form action="{{ route('pic.team.contacts.store') }}" method="POST" class="space-y-3 font-sans text-xs">
                    @csrf
                    <div>
                        <label class="block font-heading font-semibold text-slate-600 mb-0.5">Nama Lengkap *</label>
                        <input type="text" name="name" required placeholder="Contoh: Ahmad Rizky" class="w-full px-3 py-1.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-heading font-semibold text-slate-600 mb-0.5">Jabatan / Peran</label>
                            <input type="text" name="role_title" placeholder="Kormanit / Humas" class="w-full px-3 py-1.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                        </div>
                        <div>
                            <label class="block font-heading font-semibold text-slate-600 mb-0.5">No. Telepon/WA *</label>
                            <input type="text" name="phone_number" required placeholder="08xxxxxxxxxx" class="w-full px-3 py-1.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                        </div>
                    </div>
                    <div>
                        <label class="block font-heading font-semibold text-slate-600 mb-0.5">ID Line (Opsional)</label>
                        <input type="text" name="line_id" placeholder="id_line" class="w-full px-3 py-1.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0c365b]">
                    </div>
                    <button type="submit" class="py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-heading font-bold transition shadow-sm">
                        Simpan Narahubung
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
