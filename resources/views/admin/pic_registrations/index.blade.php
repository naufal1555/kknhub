@extends('layouts.ugm')

@section('title', 'Verifikasi Registrasi PIC KKN - Admin KKNHub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-slate-900">Admin: Verifikasi Registrasi PIC KKN</h1>
            <p class="text-slate-500 font-sans text-xs">Meninjau dan menyetujui pengajuan hak akses PIC dari mahasiswa.</p>
        </div>
        <a href="{{ route('admin.catalogs.index') }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-heading font-semibold bg-white text-slate-700 hover:bg-slate-50 shadow-sm transition">
            Moderasi Katalog OPREC
        </a>
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

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm font-sans">
            <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 text-xs font-heading font-semibold uppercase tracking-wider">
                <tr>
                    <th class="p-4">Nama Pemohon dan Email</th>
                    <th class="p-4">Tim yang Diajukan</th>
                    <th class="p-4">Lokasi dan Periode</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($registrations as $reg)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4">
                            <div class="font-heading font-bold text-slate-900">{{ $reg->user->name }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $reg->user->email }} ({{ $reg->user->nim ?? '-' }})</div>
                        </td>
                        <td class="p-4">
                            <div class="font-heading font-bold text-slate-800">{{ $reg->proposed_team_name }}</div>
                            @if($reg->description)
                                <div class="text-xs text-slate-500 line-clamp-1 font-sans">{{ $reg->description }}</div>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-slate-600">
                            <div>{{ $reg->target_location }}</div>
                            <div class="text-slate-400">{{ $reg->period }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-heading font-bold
                                @if($reg->status === 'approved') bg-emerald-100 text-emerald-800
                                @elseif($reg->status === 'pending') bg-amber-100 text-amber-800
                                @else bg-rose-100 text-rose-800 @endif">
                                {{ strtoupper($reg->status) }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            @if($reg->status === 'pending')
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('admin.pic-registrations.approve', $reg->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-heading font-bold rounded-lg shadow-sm transition">
                                            Approve
                                        </button>
                                    </form>
                                    <button onclick="document.getElementById('reject-form-{{ $reg->id }}').classList.toggle('hidden')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-heading font-bold rounded-lg shadow-sm transition">
                                        Reject
                                    </button>
                                </div>
                                <div id="reject-form-{{ $reg->id }}" class="hidden mt-2 text-left bg-slate-50 p-2.5 border rounded-xl">
                                    <form action="{{ route('admin.pic-registrations.reject', $reg->id) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="text" name="rejection_reason" required placeholder="Alasan penolakan..." class="w-full px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg">
                                        <button type="submit" class="px-3 py-1 bg-rose-600 text-white text-xs font-heading font-bold rounded-lg">Kirim Penolakan</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-slate-400 font-sans">Selesai diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500 font-sans">Belum ada pengajuan registrasi PIC.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $registrations->links() }}
    </div>
</div>
@endsection
