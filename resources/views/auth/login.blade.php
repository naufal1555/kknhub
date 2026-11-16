@extends('layouts.ugm')

@section('title', 'Masuk - KKNHub Universitas Gadjah Mada')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="w-full max-w-md mx-auto bg-white border border-slate-200 rounded-2xl shadow-md p-6 sm:p-8">
        <div class="text-center mb-6">
            <img src="/images/ugm-emblem.svg" alt="Universitas Gadjah Mada" class="w-14 h-14 mx-auto mb-2">
            <h1 class="text-xl font-heading font-bold text-[#083d62] uppercase tracking-wide">UNIVERSITAS GADJAH MADA</h1>
            <h2 class="text-sm font-heading font-semibold text-slate-700 mt-1">KKNHub - Portal Masuk</h2>
            <p class="text-xs text-slate-500 font-sans mt-0.5">Sistem Manajemen Informasi Rekrutmen KKN</p>
        </div>

        @if(session('info'))
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-xl">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs font-sans">
            @csrf
            <div>
                <label for="email" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Email Resmi UGM</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@ugm.ac.id"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#083d62]">
                <p class="text-[11px] text-slate-500 mt-1">Hanya menerima format alamat email @ugm.ac.id</p>
            </div>

            <div>
                <label for="password" class="block font-heading font-bold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#083d62]">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#083d62] focus:ring-[#083d62]">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-[#083d62] hover:bg-[#052942] text-white font-heading font-bold rounded-xl text-xs transition shadow-sm">
                Masuk ke Sistem
            </button>
        </form>

        <div class="mt-6 text-center text-xs font-sans text-slate-600 border-t border-slate-100 pt-4">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-heading font-bold text-[#083d62] hover:underline">
                Daftar Mahasiswa Baru
            </a>
        </div>
    </div>
</div>
@endsection
