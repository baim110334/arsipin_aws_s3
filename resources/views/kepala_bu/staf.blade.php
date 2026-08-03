@extends('layouts.app')

@section('title', 'Daftar Anggota Staf')
@section('header', 'Manajemen Anggota Staf')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold animate-fade-in">
    {{ session('success') }}
</div>
@endif

{{-- ⏳ AKORDEON DAFTAR KANDIDAT ANTRIAN PEGAWAI --}}
<details class="mb-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group">
    <summary class="p-6 flex justify-between items-center cursor-pointer list-none select-none">
        <div>
            <h3 class="text-base font-bold text-[#0c1a3c] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Tarik Pegawai Baru
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">Klik untuk melihat daftar akun pegawai dari Admin yang belum memiliki penempatan unit.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-bold bg-blue-50 text-blue-700 px-4 py-2 rounded-xl border border-blue-100 uppercase tracking-wider group-open:bg-gray-100 group-open:text-gray-500 group-open:border-gray-200 transition">
            <span class="group-open:hidden">+ Buka Daftar Kandidat</span>
            <span class="hidden group-open:block">▲ Tutup Daftar</span>
        </div>
    </summary>
    
    <div class="p-6 border-t border-gray-50 bg-gray-50/30 max-h-80 overflow-y-auto divide-y divide-gray-100">
        @forelse($kandidat as $k)
        <div class="py-3.5 flex justify-between items-center first:pt-0 last:pb-0">
            <div>
                <p class="font-bold text-[#0c1a3c] text-xs">{{ $k->nama_lengkap }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $k->email }} • Hub: {{ $k->no_hp ?? '-' }}</p>
            </div>
            {{-- 🔥 TOMBOL REKRUT SUDAH DI-RESTORE WARNANYA AGAR MUNCUL GAGAH --}}
            <form action="{{ route('kepala-bu.staf.rekrut', $k->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-[#0c1a3c] text-white text-[11px] font-bold rounded-xl uppercase tracking-wider hover:bg-[#112554] active:scale-95 transition-all shadow-sm">
                    + Rekrut
                </button>
            </form>
        </div>
        @empty
        <div class="py-4 text-center text-gray-400 text-xs italic">
            Tidak ada kandidat pegawai baru yang tersedia untuk unit {{ strtoupper(Auth::user()->bisnis_unit) }} saat ini.
        </div>
        @endforelse
    </div>
</details>

{{-- 🏢 JUDUL INFORMASI TABEL UTAMA STAF AKTIF UNIT --}}
<div class="mb-6 flex justify-between items-center select-none">
    <div>
        <h3 class="text-sm font-bold text-[#0c1a3c] uppercase tracking-wider">Staf Aktif Unit: <span class="text-blue-600">{{ strtoupper(Auth::user()->bisnis_unit) }}</span></h3>
    </div>
    <div class="text-xs font-bold bg-white text-[#0c1a3c] px-3 py-1.5 rounded-xl border border-gray-100 shadow-2xs">
        Total: {{ $staf->count() }} Orang
    </div>
</div>

{{-- 📊 BLOK UTAMA TABEL PENGURUS DAFTAR ANGGOTA STAF --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs uppercase text-gray-400 font-bold bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4">Nama Pegawai / Email</th>
                    <th class="px-6 py-4">Kontak (No. HP)</th>
                    <th class="px-6 py-4">Divisi Kerja</th>
                    <th class="px-6 py-4 text-center w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm text-gray-600">
                @forelse($staf as $p)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-[#0c1a3c]">{{ $p->nama_lengkap }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $p->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-500 font-mono tracking-wide text-xs">
                        {{ $p->no_hp ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($p->role === 'pegawai-retail')
                            <span class="px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-red-100">
                                Retail
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-blue-100">
                                Komersial
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('kepala-bu.staf.keluarkan', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan {{ $p->nama_lengkap }} dari unit bisnis Anda?')">
                            @csrf
                            <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition-all inline-flex items-center justify-center border border-transparent hover:border-red-100 shadow-2xs" title="Keluarkan dari Unit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic text-xs font-light">
                        Belum ada anggota pegawai staf aktif yang terdaftar pada unit bisnis {{ strtoupper(Auth::user()->bisnis_unit) }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection