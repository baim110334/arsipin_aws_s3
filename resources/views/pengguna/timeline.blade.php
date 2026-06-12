@extends('layouts.app')

@section('title', 'Timeline Aktivitas')
@section('header', Auth::user()->role === 'kepala-bu' || Auth::user()->role === 'kepala_bu' ? 'Monitoring Aktivitas Staf Unit' : 'Timeline Kerja Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    
    <div class="p-5 bg-gradient-to-r from-[#0c1a3c] to-[#1a316c] rounded-2xl text-white shadow-sm flex justify-between items-center">
        <div>
            <h3 class="text-base font-black tracking-tight">
                {{ Auth::user()->role === 'kepala-bu' || Auth::user()->role === 'kepala_bu' ? 'Jejak Kerja Digital Unit Bisnis' : 'Log Jejak Digital Anda' }}
            </h3>
            <p class="text-[11px] text-gray-300 font-light mt-0.5">
                Sistem merekam seluruh aktivitas penanganan arsip dokumen secara transparan dan berurutan.
            </p>
        </div>
        <div class="px-3 py-1.5 bg-white/10 rounded-xl border border-white/10 text-[10px] font-bold uppercase tracking-wider font-mono">
            🏢 {{ Auth::user()->bisnis_unit }}
        </div>
    </div>

    @if(Auth::user()->role === 'kepala-bu' || Auth::user()->role === 'kepala_bu')
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('kepala-bu.timeline') }}" method="GET" class="flex items-center gap-3">
                <div class="flex-1 max-w-sm space-y-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Pilih Anggota Staf</label>
                    <select name="staf" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-marica-navy outline-none text-gray-600 font-medium transition-all">
                        <option value="semua" {{ $filterStaf == 'semua' ? 'selected' : '' }}>🌍 Tampilkan Semua Anggota</option>
                        @foreach($listStaf as $staf)
                            <option value="{{ $staf->id }}" {{ $filterStaf == $staf->id ? 'selected' : '' }}>
                                👤 {{ $staf->nama_lengkap }} ({{ strtoupper(str_replace('pegawai-', '', $staf->role)) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-5">
                    <button type="submit" class="px-5 py-2 bg-[#0c1a3c] text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-opacity-95 transition-all">
                        Saring
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-y-auto max-h-[calc(100vh-240px)] scrollbar-thin">
        
        @if($logs->isEmpty())
            <div class="py-12 text-center text-gray-400 italic font-light text-xs">
                Belum ada rekaman aktivitas digital yang terekam pada kriteria ini.
            </div>
        @else
            <div class="absolute left-[31px] top-10 bottom-10 w-0.5 bg-gray-100"></div>

            <div class="space-y-5 relative">
                @foreach($logs as $log)
                    <div class="flex gap-4 items-start group">
                        
                        <div class="w-6 h-6 rounded-lg bg-white border-2 border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:border-marica-navy transition-colors z-10 shadow-sm mt-1">
                            <div class="w-1.5 h-1.5 rounded-full {{ $log->aksi === 'Ajukan Hapus' ? 'bg-amber-500 animate-ping' : 'bg-marica-navy' }}"></div>
                        </div>

                        <div class="flex-1 bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-marica-navy/10 hover:bg-white transition-all duration-300 shadow-sm">
                            <div class="flex items-center justify-between gap-4 mb-2">
                                <div class="flex items-center gap-2">
                                    @if($log->aksi === 'Upload Dokumen')
                                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded text-[9px] font-bold uppercase">📄 UPLOAD</span>
                                    @elseif($log->aksi === 'Ajukan Hapus')
                                        <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded text-[9px] font-bold uppercase">⚠️ REQUEST</span>
                                    @elseif($log->aksi === 'Setujui Pemusnahan')
                                        <span class="px-1.5 py-0.5 bg-green-50 text-green-700 border border-green-100 rounded text-[9px] font-bold uppercase">✅ DELETED</span>
                                    @elseif($log->aksi === 'Tolak Pemusnahan')
                                        <span class="px-1.5 py-0.5 bg-red-50 text-red-700 border border-red-100 rounded text-[9px] font-bold uppercase">❌ REJECTED</span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-[9px] font-bold uppercase">{{ $log->aksi }}</span>
                                    @endif

                                    <span class="text-xs font-bold text-marica-navy ml-1">{{ $log->nama_user }}</span>
                                </div>

                                <span class="text-[10px] font-mono text-gray-400 font-medium">
                                    {{ $log->created_at->format('d M / H:i') }} WIB
                                               </span>
                            </div>

                            <p class="text-xs text-gray-600 font-normal leading-relaxed">
                                {{ $log->deskripsi }}
                            </p>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection