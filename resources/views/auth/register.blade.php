@extends('layouts.ugm')

@section('title', 'Registrasi Mahasiswa - KKNHub Universitas Gadjah Mada')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="w-full max-w-lg mx-auto bg-white border border-slate-200 rounded-2xl shadow-md p-6 sm:p-8">
        <div class="text-center mb-6">
            <img src="/images/ugm-emblem.svg" alt="Universitas Gadjah Mada" class="w-14 h-14 mx-auto mb-2">
            <h1 class="text-xl font-heading font-bold text-[#083d62] uppercase tracking-wide">UNIVERSITAS GADJAH MADA</h1>
            <h2 class="text-sm font-heading font-semibold text-slate-700 mt-1">Registrasi Mahasiswa Baru</h2>
            <p class="text-xs text-slate-500 font-sans mt-0.5">Sistem Informasi Pengabdian KKN UGM</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4 text-xs font-sans">
            @csrf
            <div>
                <label for="name" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ahmad Fathin"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
            </div>

            <div>
                <label for="email" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Email Resmi UGM (@ugm.ac.id) *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@ugm.ac.id"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                <p class="text-[11px] text-slate-500 mt-1">Hanya menerima format alamat email @ugm.ac.id</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="nim" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">NIM Mahasiswa</label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}" placeholder="21/xxxxxx/xx/xxxxx"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label for="phone_number" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp/HP</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="faculty" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Fakultas</label>
                    <input type="text" id="faculty" name="faculty" value="{{ old('faculty') }}" placeholder="Fakultas Teknik"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label for="major" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Program Studi / Departemen</label>
                    <input type="text" id="major" name="major" value="{{ old('major') }}" placeholder="Teknologi Informasi"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi *</label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
                <div>
                    <label for="password_confirmation" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Ulangi Kata Sandi *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi kata sandi"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-[#083d62] hover:bg-[#052942] text-white font-heading font-bold rounded-xl text-xs transition shadow-sm mt-2">
                Daftar Akun Mahasiswa
            </button>
        </form>

        <div class="mt-6 text-center text-xs font-sans text-slate-600 border-t border-slate-100 pt-4">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-heading font-bold text-[#083d62] hover:underline">
                Masuk di Sini
            </a>
        </div>
    </div>
</div>
@endsection
