@extends('layouts.app')

@section('title', 'Bisnis Unit Retail')
@section('header', 'Bisnis Unit - Retail')

@section('content')
<div class="space-y-6 select-none animate-fade-in">
    
    <div class="border-b border-gray-100 pb-4">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-layer-group text-sky-500 animate-pulse"></i> Kluster Data Operasional Divisi Retail
        </p>
        <p class="text-xs text-gray-400 mt-1 font-light">Pilih kelompok segmen bisnis unit di bawah ini untuk mengelola arsip pembukuan dan validasi data entitas perusahaan terkait.</p>
    </div>

    <!-- Alert Notifikasi Sukses/Gagal -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-2xl flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- 🔄 PERULANGAN CARD DINAMIS DARI DATABASE --}}
        @foreach($bisnisUnits as $bu)
            @php
                // Slug URL dari nama bisnis unit (Contoh: "LPG PSO" -> "lpg-pso")
                $slug = Str::slug($bu->nama_bisnis_unit);
            @endphp
            <a href="/retail/{{ $slug }}" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-sky-300 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between h-52 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 text-slate-50 opacity-[0.03] text-8xl font-black group-hover:opacity-[0.06] group-hover:scale-110 transition-all duration-300">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <div class="flex justify-between items-start">
                        <div class="w-10 h-10 bg-gray-50 text-slate-400 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-sky-50 group-hover:text-[#0ea5e9] transition-all duration-300 shadow-inner">
                            <i class="fa-solid fa-building text-sm"></i>
                        </div>
                        <span class="px-2.5 py-0.5 bg-slate-50 text-slate-400 border border-slate-100 rounded-full text-[9px] font-black uppercase tracking-widest group-hover:bg-sky-50 group-hover:text-sky-600 group-hover:border-sky-100 transition-all duration-300">RETAIL UNIT</span>
                    </div>
                    <h3 class="text-base font-black mt-4 text-[#0c1a3c] tracking-tight">{{ $bu->nama_bisnis_unit }}</h3>
                    <p class="text-[11px] text-gray-400 font-light mt-1 leading-relaxed line-clamp-2">
                        {{ $bu->deskripsi ?? 'Pusat log berkas dan laporan keuangan entitas '.$bu->nama_bisnis_unit }}
                    </p>
                </div>
                <div class="flex justify-between items-center border-t border-gray-50 pt-3 mt-3">
                    <span class="text-[11px] text-gray-400 font-bold font-mono bg-slate-50 px-2 py-0.5 rounded border border-gray-100">
                        <i class="fa-solid fa-building text-[10px] mr-1 opacity-70"></i>{{ $bu->perusahaans_count }} Perusahaan
                    </span>
                    <div class="w-6 h-6 rounded-full bg-gray-50 text-[#0c1a3c] flex items-center justify-center group-hover:bg-[#0c1a3c] group-hover:text-white group-hover:translate-x-1 transition-all duration-300 shadow-sm">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </div>
                </div>
            </a>
        @endforeach

        {{-- ➕ CARD KHUSUS "TAMBAH BISNIS BARU" (KHUSUS ROLE ADMIN) --}}
        @if(auth()->user()->role === 'admin')
            <button onclick="bukaModalBu()" type="button" class="bg-white/60 hover:bg-white p-6 rounded-3xl border-2 border-dashed border-gray-200 hover:border-sky-400 shadow-none hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center justify-center h-52 text-center cursor-pointer">
                <div class="w-12 h-12 bg-sky-50 text-sky-500 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-plus text-lg"></i>
                </div>
                <h3 class="text-sm font-extrabold text-[#0c1a3c] group-hover:text-sky-600 transition-colors">Tambah Bisnis Baru</h3>
                <p class="text-[10px] text-gray-400 mt-1 max-w-[200px]">Klik untuk menambah segmen unit bisnis retail baru ke dalam sistem</p>
            </button>
        @endif

    </div>
</div>

{{-- 🪟 MODAL POP-UP TAMBAH BISNIS UNIT BARU --}}
<div id="modalTambahBu" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-black text-[#0c1a3c] flex items-center gap-2">
                <i class="fa-solid fa-building-circle-check text-sky-500"></i> Buat Bisnis Unit Baru
            </h3>
            <button onclick="tutupModalBu()" class="text-gray-400 hover:text-red-500 w-8 h-8 rounded-full hover:bg-red-50 flex items-center justify-center transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('bisnis-unit.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            {{-- Lock Kategori ke Retail secara otomatis --}}
            <input type="hidden" name="kategori" value="retail">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Bisnis Unit Baru</label>
                <input type="text" name="nama_bisnis_unit" placeholder="Contoh: SWALAYAN / DERMAGA / HOTEL" required class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 uppercase font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Singkat (Opsional)</label>
                <textarea name="deskripsi" rows="3" placeholder="Tuliskan deskripsi singkat ruang lingkup unit ini..." class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500"></textarea>
            </div>

            <div class="flex justify-end items-center gap-2 pt-2 border-t border-gray-50">
                <button type="button" onclick="tutupModalBu()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-[10px]"></i> Buat Unit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalBu() {
        document.getElementById('modalTambahBu').classList.remove('hidden');
    }

    function tutupModalBu() {
        document.getElementById('modalTambahBu').classList.add('hidden');
    }
</script>
@endsection