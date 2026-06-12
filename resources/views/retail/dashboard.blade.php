@extends('layouts.app')

@section('title', 'Dashboard Divisi Retail')
@section('header', 'Dashboard Divisi Retail')

@section('content')
<div class="space-y-8 select-none animate-fade-in">
    
    <div class="p-6 bg-blue-50 border border-blue-100 rounded-3xl shadow-sm">
        <h3 class="text-lg font-black text-[#0c1a3c] tracking-tight">Selamat Datang di Panel Retail, {{ Auth::user()->nama_lengkap ?? 'Pegawai' }}!</h3>
        <p class="text-xs text-slate-500 mt-1 font-light">
            Anda masuk dengan hak akses Pegawai Divisi Retail. Saat ini Anda ditempatkan pada Otoritas Wilayah kerja: <span class="font-bold uppercase text-sky-500 tracking-wide">{{ Auth::user()->bisnis_unit }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-sky-200 transition-all duration-300 flex items-center justify-between group">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Anak Perusahaan</span>
                <span class="text-3xl font-black text-[#0c1a3c] font-mono tracking-tight">{{ $totalPerusahaan }} <span class="text-xs font-normal text-gray-400">Perusahaan</span></span>
            </div>
            <div class="w-12 h-12 bg-gray-50 text-slate-400 font-mono font-black text-xs rounded-2xl flex items-center justify-center tracking-widest border border-gray-100/50 group-hover:bg-[#0c1a3c] group-hover:text-white transition-all duration-300 shadow-sm">
                PT
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-sky-200 transition-all duration-300 flex items-center justify-between group">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Arsip Dokumen</span>
                <span class="text-3xl font-black text-[#0c1a3c] font-mono tracking-tight">{{ $totalDokumen }} <span class="text-xs font-normal text-gray-400">Berkas</span></span>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-sky-600 font-mono font-black text-xs rounded-2xl flex items-center justify-center tracking-widest border border-blue-100/30 group-hover:bg-[#0c1a3c] group-hover:text-white transition-all duration-300 shadow-sm">
                DOC
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300 flex items-center justify-between group">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Dokumen Tertunda</span>
                <span class="text-3xl font-black text-amber-500 font-mono tracking-tight">{{ $dokumenPending }} <span class="text-xs font-normal text-gray-400">Berkas</span></span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 font-mono font-black text-xs rounded-2xl flex items-center justify-center tracking-widest border border-amber-100 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm">
                PND
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-gray-50 bg-gray-50/20">
                    <h3 class="text-sm font-black text-[#0c1a3c] uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-sky-500"></i> Aktivitas Dokumen Retail Terakhir
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5 font-light">Daftar riwayat berkas digital yang baru saja dikelola oleh Anda pada unit kerja ini</p>
                </div>

                <div class="overflow-x-auto max-h-[340px] overflow-y-auto scrollbar-thin">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-white z-20 shadow-[0_1px_0_0_rgba(243,244,246,1)]">
                            <tr class="text-[10px] uppercase text-gray-400 font-black border-b border-gray-50 bg-gray-50/50 tracking-wider">
                                <th class="px-6 py-4 w-1/2">Nama File / Dokumen</th>
                                <th class="px-6 py-4">Perusahaan</th>
                                <th class="px-6 py-4">Tipe Keuangan</th>
                                <th class="px-6 py-4">Status Alur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs text-gray-600">
                            @forelse($dokumenTerbaru as $dok)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-500 shadow-sm">
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
                                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded w-max block uppercase tracking-wide border border-slate-200/30">
                                            {{ $dok->tipe_keuangan ?? 'Umum' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($dok->status === 'active')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-sky-50 text-sky-700 border border-sky-100 rounded-full text-[9px] font-black uppercase tracking-wider shadow-sm">
                                                APPROVED
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
                                            <span>Belum ada data aktivitas penyimpanan dokumen retail terbaru.</span>
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
                    <i class="fa-solid fa-bolt text-sky-500"></i> Aksi Cepat
                </h4>
                <p class="text-xs text-gray-400 font-light">Pintasan praktis untuk mengelola dan memantau berkas di unit bisnis Anda secara cepat.</p>
            </div>
            <div class="pt-2">
                <a href="/retail/{{ strtolower(Auth::user()->bisnis_unit) }}" class="w-full py-3 bg-[#0c1a3c] text-white text-xs font-black rounded-xl uppercase tracking-wider hover:bg-[#112554] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 shadow-md shadow-blue-950/10 cursor-pointer text-center">
                    <i class="fa-solid fa-folder-plus text-sky-400"></i> Kelola Dokumen Perusahaan
                </a>
            </div>
        </div>

    </div>
</div>
@endsection 