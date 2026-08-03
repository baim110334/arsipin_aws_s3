@extends('layouts.app')

@section('title', 'Menunggu Persetujuan Unit')

@section('content')
<div class="max-w-xl mx-auto mt-12 select-none animate-fade-in">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-md p-8 text-center space-y-6">
        
        <div class="flex justify-center">
            <div class="relative w-24 h-24 flex items-center justify-center bg-amber-50 rounded-full text-amber-500 border border-amber-100 shadow-sm animate-pulse">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-amber-400 animate-spin"></div>
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-xl font-bold text-[#0c1a3c]">Verifikasi Otoritas Wilayah</h3>
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Status Akun: <span class="text-amber-600">Menunggu Persetujuan (Pending)</span></p>
        </div>

        <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 text-left space-y-3">
            <p class="text-sm text-gray-600 leading-relaxed font-light">
                Halo, <span class="font-bold text-[#0c1a3c]">{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}</span>. Akun Anda telah <span class="text-green-600 font-semibold">Berhasil Diaktifkan</span> oleh Admin Utama di dalam sistem ARSIPIN.
            </p>
            <p class="text-xs text-gray-500 leading-relaxed font-light">
                Namun, sesuai dengan matriks hak akses penutupan buku finansial, Anda **diwajibkan menunggu Kepala Bisnis Unit (Kepala BU)** dari divisi <span class="font-bold uppercase text-blue-600">{{ Auth::user()->bisnis_unit ?? 'Pilihan' }}</span> untuk menarik dan memvalidasi penempatan unit kerja Anda terlebih dahulu.
            </p>
        </div>

        <div class="pt-4 border-t border-gray-50">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 transition rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar dari Sistem
            </a>
        </div>

    </div>
</div>

{{-- Form Hidden khusus Logout bawaan sistem --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>
@endsection