@extends('layouts.app')

@section('title', 'Dashboard Kepala BU')
@section('header', 'Dashboard Kepala Unit Bisnis')

@section('content')
<div class="space-y-8 select-none animate-fade-in">
    
    <div class="p-6 bg-blue-50 border border-blue-100 rounded-3xl shadow-sm">
        <h3 class="text-lg font-black text-[#0c1a3c] tracking-tight">Selamat Datang di Panel Otoritas, {{ Auth::user()->nama_lengkap ?? 'Kepala BU' }}!</h3>
        <p class="text-xs text-slate-500 mt-1 font-light">
            Anda masuk sebagai Kepala Unit Bisnis bagian <span class="font-bold uppercase text-sky-500 tracking-wide">{{ str_replace('-', ' ', Auth::user()->bisnis_unit) }}</span>. Monitor berkas staf dan kelola persetujuan validasi secara tersentralisasi.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-sky-200 transition-all duration-300 flex items-center justify-between group">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Arsip Unit</span>
                <span class="text-3xl font-black text-[#0c1a3c] font-mono tracking-tight">{{ $totalDokumen }} <span class="text-xs font-normal text-gray-400">Berkas</span></span>
            </div>
            <div class="w-12 h-12 bg-gray-50 text-slate-400 font-mono font-black text-xs rounded-2xl flex items-center justify-center tracking-widest border border-gray-100/50 group-hover:bg-[#0c1a3c] group-hover:text-white transition-all duration-300 shadow-sm">
                {{ strtoupper(substr(Auth::user()->bisnis_unit, 0, 3)) }}
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300 flex items-center justify-between group">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Validasi Hapus</span>
                <span class="text-3xl font-black text-amber-500 font-mono tracking-tight">{{ $dokumenPending }} <span class="text-xs font-normal text-gray-400">Permohonan</span></span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 font-black text-xs rounded-2xl flex items-center justify-center tracking-wider border border-amber-100 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm">
                ACC
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 flex items-center justify-between group">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Staf Bawahan</span>
                <span class="text-3xl font-black text-indigo-600 font-mono tracking-tight">{{ $totalStaf }} <span class="text-xs font-normal text-gray-400">Pegawai</span></span>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 font-black text-xs rounded-2xl flex items-center justify-center tracking-wider border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                STF
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-gray-50 bg-gray-50/20">
                    <h3 class="text-sm font-black text-[#0c1a3c] uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solidxl fa-solid fa-folder-open text-sky-500"></i> Arsip Berkas Terbaru Masuk
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5 font-light">Daftar riwayat dokumen finansial yang baru saja diunggah oleh staf unit Anda</p>
                </div>

                <div class="overflow-x-auto max-h-[340px] overflow-y-auto scrollbar-thin">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-white z-20 shadow-[0_1px_0_0_rgba(243,244,246,1)]">
                            <tr class="text-[10px] uppercase text-gray-400 font-black border-b border-gray-50 bg-gray-50/50 tracking-wider">
                                <th class="px-6 py-4 w-1/2">Nama File / Dokumen</th>
                                <th class="px-6 py-4">Perusahaan</th>
                                <th class="px-6 py-4">Tipe & Tahun</th>
                                <th class="px-6 py-4">Status Alur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs text-gray-600">
                            @forelse($dokumenTerbaru as $dok)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                                                <i class="fa-solid fa-file-pdf text-xs"></i>
                                            </div>
                                            <div>
                                                <span class="truncate max-w-[180px] block font-bold text-[#0c1a3c]" title="{{ $dok->nama_dokumen }}">{{ $dok->nama_dokumen }}</span>
                                                <span class="text-[10px] text-gray-400 font-mono font-bold mt-0.5 block tracking-tight">{{ $dok->no_dokumen }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 font-black text-xs text-slate-700 uppercase">
                                        <i class="fa-solid fa-building text-gray-300 mr-1"></i>{{ $dok->perusahaan }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-0.5">
                                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded w-max block uppercase tracking-wide border border-slate-200/30">
                                                {{ $dok->tipe_keuangan }}
                                            </span>
                                            <span class="text-[9px] font-black text-gray-400 font-mono block">
                                                TH {{ $dok->tahun_buku ?? '2026' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($dok->status === 'active')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-sky-50 text-sky-700 border border-sky-100 rounded-full text-[9px] font-black uppercase tracking-wider shadow-sm">
                                                ACTIVE
                                            </span>
                                        @elseif($dok->status === 'pending_hapus')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[9px] font-black uppercase tracking-wider shadow-sm animate-pulse">
                                                PENDING
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-gray-50 text-gray-600 border border-gray-100 rounded-full text-[9px] font-black uppercase tracking-wider">
                                                {{ strtoupper($dok->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic font-light">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-folder-open text-2xl text-gray-200"></i>
                                            <span>Belum ada riwayat dokumen finansial yang diunggah di dalam unit bisnis ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-sky-300 transition-all duration-300 flex flex-col justify-between space-y-6">
            <div>
                <h4 class="text-sm font-black text-[#0c1a3c] uppercase tracking-wider mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-sky-500"></i> Aksi Cepat BU
                </h4>
                <p class="text-xs text-gray-400 font-light">Pintasan kontrol peninjauan berkas internal divisi Anda secara instan.</p>
            </div>
            
            <div class="space-y-3 pt-2">
                @if(in_array(strtolower(Auth::user()->bisnis_unit), ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']))
                    <a href="/retail" class="w-full py-3 bg-[#0c1a3c] text-white text-xs font-black rounded-xl uppercase tracking-wider hover:bg-[#112554] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 shadow-md shadow-blue-950/10 cursor-pointer">
                        <i class="fa-solid fa-magnifying-glass text-sky-400"></i> Periksa Dokumen Staf
                    </a>
                @else
                    <a href="/comercial" class="w-full py-3 bg-[#0c1a3c] text-white text-xs font-black rounded-xl uppercase tracking-wider hover:bg-[#112554] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 shadow-md shadow-blue-950/10 cursor-pointer">
                        <i class="fa-solid fa-magnifying-glass text-sky-400"></i> Periksa Dokumen Staf
                    </a>
                @endif
                
                <a href="{{ route('kepala-bu.staf') }}" class="w-full py-3 bg-gray-50 border border-gray-200 text-gray-500 text-xs font-black rounded-xl uppercase tracking-wider hover:bg-gray-100 transition duration-200 flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                    <i class="fa-solid fa-user-group opacity-70"></i> Kelola Akun Anggota
                </a>
            </div>
        </div>

    </div>
</div>
@endsection