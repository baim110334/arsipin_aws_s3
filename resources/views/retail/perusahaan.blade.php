@extends('layouts.app')

@section('title', 'Daftar Perusahaan ' . $nama_bu)
@section('header', 'Retail - ' . $nama_bu)

@section('content')
<div class="space-y-6 select-none animate-fade-in">
    
    <div class="border-b border-gray-100 pb-4">
        <a href="/retail" class="inline-flex items-center gap-1.5 text-xs font-black text-gray-400 hover:text-[#0c1a3c] transition-all uppercase tracking-wider mb-1">
            <i class="fa-solid fa-arrow-left text-sm"></i> Kembali ke Bisnis Unit
        </a>
        <p class="text-xs text-gray-400 mt-1 font-light">Silakan pilih salah satu entitas korporat (PT) di bawah ini untuk mengakses, memverifikasi, dan mengelola log repositori berkas PDF penutupan buku keuangan terkait.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($list_pt as $pt)
            <a href="/retail/{{ $bisnis_unit }}/{{ Str::slug($pt) }}" 
               class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-sky-300 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between h-56 relative overflow-hidden">
                
                <div class="absolute -right-6 -bottom-6 text-slate-50 opacity-[0.03] text-8xl font-black group-hover:opacity-[0.06] group-hover:scale-110 transition-all duration-300">
                    <i class="fa-solid fa-building"></i>
                </div>

                <div>
                    <div class="flex justify-between items-start">
                        <div class="w-11 h-11 bg-gray-50 text-slate-400 rounded-2xl border border-gray-100/50 flex items-center justify-center flex-shrink-0 group-hover:bg-sky-50 group-hover:text-[#0ea5e9] group-hover:border-sky-100 transition-all duration-300 shadow-sm">
                            <i class="fa-solid fa-building text-base"></i>
                        </div>
                        <span class="px-2.5 py-0.5 bg-slate-50 text-slate-400 border border-slate-100 rounded-full text-[9px] font-black uppercase tracking-widest group-hover:bg-sky-50 group-hover:text-sky-600 group-hover:border-sky-100 transition-all duration-300">CORPORATE</span>
                    </div>

                    <h4 class="font-black text-[#0c1a3c] text-xl mt-5 tracking-tight group-hover:text-sky-500 transition-colors duration-200">
                        {{ $pt }}
                    </h4>
                </div>

                <div class="flex justify-between items-center border-t border-gray-50 pt-3 mt-4">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-wider font-black text-gray-400">Direktori Berkas</span>
                        <span class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1">
                            <i class="fa-regular fa-file-pdf text-red-500"></i> Klik untuk verifikasi PDF
                        </span>
                    </div>
                    <div class="w-7 h-7 rounded-full bg-gray-50 text-[#0c1a3c] flex items-center justify-center group-hover:bg-[#0c1a3c] group-hover:text-white group-hover:translate-x-1 transition-all duration-300 shadow-sm">
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl border border-dashed border-gray-200 text-center text-gray-400">
                <div class="flex flex-col items-center justify-center gap-3">
                    <i class="fa-solid fa-building-circle-exclamation text-4xl text-gray-200 animate-bounce"></i>
                    <span class="text-xs font-light">Entitas perusahaan di bawah unit ini tidak ditemukan atau belum diregistrasikan ke sistem.</span>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection