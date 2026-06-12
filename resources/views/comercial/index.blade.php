@extends('layouts.app')

@section('title', 'Bisnis Unit Komersial')
@section('header', 'Bisnis Unit - Komersial')

@section('content')
<div class="space-y-6 select-none animate-fade-in">
    
    <div class="border-b border-gray-100 pb-4">
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest flex items-center gap-2">
            <i class="fa-solid fa-wallet text-sky-500 animate-pulse"></i> Kluster Data Operasional Divisi Komersial
        </p>
        <p class="text-xs text-gray-400 mt-1 font-light">Pilih kelompok segmen bisnis unit komersial di bawah ini untuk memverifikasi arsip, audit trail, serta pelacakan sirkulasi berkas penutupan buku perusahaan terkait.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <a href="/comercial/transportasi-laut" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-sky-300 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between h-52 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-slate-50 opacity-[0.03] text-8xl font-black group-hover:opacity-[0.06] group-hover:scale-110 transition-all duration-300">
                <i class="fa-solid fa-ship"></i>
            </div>
            <div>
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-gray-50 text-slate-400 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-sky-50 group-hover:text-[#0ea5e9] transition-all duration-300 shadow-inner">
                        <i class="fa-solid fa-ship text-sm"></i>
                    </div>
                    <span class="px-2.5 py-0.5 bg-slate-50 text-slate-400 border border-slate-100 rounded-full text-[9px] font-black uppercase tracking-widest group-hover:bg-sky-50 group-hover:text-sky-600 group-hover:border-sky-100 transition-all duration-300">COMMERCIAL</span>
                </div>
                <h3 class="text-base font-black mt-4 text-[#0c1a3c] tracking-tight">Transportasi Laut</h3>
                <p class="text-[11px] text-gray-400 font-light mt-1 leading-relaxed">Arsip manifes kargo komersial, berkas sewa armada kapal, trace PPN niaga, dan berkas invoice pelabuhan hub.</p>
            </div>
            <div class="flex justify-between items-center border-t border-gray-50 pt-3 mt-3">
                <span class="text-[11px] text-gray-400 font-bold font-mono bg-slate-50 px-2 py-0.5 rounded border border-gray-100"><i class="fa-solid fa-building text-[10px] mr-1 opacity-70"></i>2 Perusahaan</span>
                <div class="w-6 h-6 rounded-full bg-gray-50 text-[#0c1a3c] flex items-center justify-center group-hover:bg-[#0c1a3c] group-hover:text-white group-hover:translate-x-1 transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </div>
            </div>
        </a>

        <a href="/comercial/shipyard" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-sky-300 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between h-52 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-slate-50 opacity-[0.03] text-8xl font-black group-hover:opacity-[0.06] group-hover:scale-110 transition-all duration-300">
                <i class="fa-solid fa-anchor"></i>
            </div>
            <div>
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-gray-50 text-slate-400 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-sky-50 group-hover:text-[#0ea5e9] transition-all duration-300 shadow-inner">
                        <i class="fa-solid fa-anchor text-sm"></i>
                    </div>
                    <span class="px-2.5 py-0.5 bg-slate-50 text-slate-400 border border-slate-100 rounded-full text-[9px] font-black uppercase tracking-widest group-hover:bg-sky-50 group-hover:text-sky-600 group-hover:border-sky-100 transition-all duration-300">DOCKING UNIT</span>
                </div>
                <h3 class="text-base font-black mt-4 text-[#0c1a3c] tracking-tight">Shipyard (Galangan Kapal)</h3>
                <p class="text-[11px] text-gray-400 font-light mt-1 leading-relaxed">Pusat log dokumen proyek perbaikan (*docking*), rekap purchasing order material lambung, dan invoice korporat.</p>
            </div>
            <div class="flex justify-between items-center border-t border-gray-50 pt-3 mt-3">
                <span class="text-[11px] text-gray-400 font-bold font-mono bg-slate-50 px-2 py-0.5 rounded border border-gray-100"><i class="fa-solid fa-building text-[10px] mr-1 opacity-70"></i>1 Perusahaan</span>
                <div class="w-6 h-6 rounded-full bg-gray-50 text-[#0c1a3c] flex items-center justify-center group-hover:bg-[#0c1a3c] group-hover:text-white group-hover:translate-x-1 transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </div>
            </div>
        </a>

        <a href="/comercial/gas-industri" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-sky-300 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between h-52 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-slate-50 opacity-[0.03] text-8xl font-black group-hover:opacity-[0.06] group-hover:scale-110 transition-all duration-300">
                <i class="fa-solid fa-flask"></i>
            </div>
            <div>
                <div class="flex justify-between items-start">
                    <div class="w-10 h-10 bg-gray-50 text-slate-400 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-sky-50 group-hover:text-[#0ea5e9] transition-all duration-300 shadow-inner">
                        <i class="fa-solid fa-flask text-sm"></i>
                    </div>
                    <span class="px-2.5 py-0.5 bg-slate-50 text-slate-400 border border-slate-100 rounded-full text-[9px] font-black uppercase tracking-widest group-hover:bg-sky-50 group-hover:text-sky-600 group-hover:border-sky-100 transition-all duration-300">MANUFACTURING</span>
                </div>
                <h3 class="text-base font-black mt-4 text-[#0c1a3c] tracking-tight">Gas Industri (Niaga Gas)</h3>
                <p class="text-[11px] text-gray-400 font-light mt-1 leading-relaxed">Repositori data supply gas manufaktur pabrik, berkas invoice piutang klien komersial, dan rekapitulasi laporan omset.</p>
            </div>
            <div class="flex justify-between items-center border-t border-gray-50 pt-3 mt-3">
                <span class="text-[11px] text-gray-400 font-bold font-mono bg-slate-50 px-2 py-0.5 rounded border border-gray-100"><i class="fa-solid fa-building text-[10px] mr-1 opacity-70"></i>2 Perusahaan</span>
                <div class="w-6 h-6 rounded-full bg-gray-50 text-[#0c1a3c] flex items-center justify-center group-hover:bg-[#0c1a3c] group-hover:text-white group-hover:translate-x-1 transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </div>
            </div>
        </a>

    </div>
</div>
@endsection