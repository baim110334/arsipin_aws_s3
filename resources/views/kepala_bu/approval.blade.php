@extends('layouts.app')

@section('title', 'Persetujuan Hapus Dokumen')
@section('header', 'Persetujuan Hapus Dokumen')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold">
    {{ session('success') }}
</div>
@endif

<div class="mb-6 flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
    <div>
        <h3 class="text-base font-bold text-marica-navy">Kotak Masuk Persetujuan Unit: <span class="uppercase text-blue-600">{{ str_replace('-', ' ', Auth::user()->bisnis_unit) }}</span></h3>
        <p class="text-xs text-gray-400 mt-0.5">Daftar berkas dokumen milik staf yang menunggu validasi persetujuan tindakan dari Anda.</p>
    </div>
    <div class="text-xs font-bold bg-amber-50 text-amber-700 px-3 py-1.5 rounded-xl border border-amber-100 uppercase tracking-wider">
        Menunggu: {{ $dokumen->count() }} Berkas
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs uppercase text-gray-400 font-bold bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 w-1/2">Nama Dokumen / Berkas</th>
                    <th class="px-6 py-4">Tipe & Tahun</th>
                    <th class="px-6 py-4">Status Saat Ini</th>
                    <th class="px-6 py-4 text-center w-40">Keputusan Otoritas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm text-gray-600">
                @forelse($dokumen as $d)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <!-- Kolom 1: Informasi Berkas -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>
                            <p class="font-semibold text-marica-navy truncate max-w-xs" title="{{ $d->nama_dokumen }}">{{ $d->nama_dokumen }}</p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 font-mono pl-6">No: {{ $d->no_dokumen }} | {{ $d->file_size ?? '-' }}</p>
                        @if($d->keterangan)
                            <p class="text-xs text-gray-400 italic bg-gray-50 p-2 rounded-lg border border-gray-100 mt-2 pl-3 font-light">💬 Catatan: {{ $d->keterangan }}</p>
                        @endif
                    </td>

                    <!-- Kolom 2: Tipe Keuangan & Tahun Buku -->
                    <td class="px-6 py-4">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded block w-max">
                                {{ $d->tipe_keuangan ?? 'Umum' }}
                            </span>
                            <span class="text-[10px] font-bold font-mono text-gray-400 block">
                                TAHUN BUKU: {{ $d->tahun_buku ?? '2026' }}
                            </span>
                        </div>
                    </td>

                    <!-- Kolom 3: Label Status Berdasarkan Kategori Database yang Valid -->
                    <td class="px-6 py-4">
                        @php
                            // 🔥 Cek kategori Bisnis Unit secara dinamis dari database MySQL
                            $currentBu = \App\Models\BisnisUnit::where('nama_bisnis_unit', 'LIKE', Auth::user()->bisnis_unit)->first();
                            $isRetail = $currentBu ? ($currentBu->kategori === 'retail') : true;
                        @endphp

                        @if($isRetail)
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold uppercase tracking-wider border border-blue-100">
                                🔵 RETAIL
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded text-[10px] font-bold uppercase tracking-wider border border-green-100">
                                🟢 KOMERSIAL
                            </span>
                        @endif
                    </td>

                  <!-- Kolom 4: Aksi Form POST -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                        
                        <!-- 1. TOMBOL SETUJUI HAPUS (Checklist Hijau) -->
                        <form action="{{ route('kepala-bu.approval.setujui', $d->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui penghapusan dokumen ini?')" 
                                    class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 p-2 rounded-lg transition-colors duration-200" 
                                    title="Setujui Hapus">
                                <!-- Ikon Checklist (✓) -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </form>

                        <!-- 2. TOMBOL TOLAK / AMANKAN KEMBALI (Silang Merah) -->
                        <form action="{{ route('kepala-bu.approval.tolak', $d->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membatalkan permohonan hapus ini dan mengamankannya kembali?')" 
                                    class="text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 p-2 rounded-lg transition-colors duration-200" 
                                    title="Tolak / Batalkan">
                                <!-- Ikon Silang (X) -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic font-light">
                         Bersih total! Tidak ada permohonan penghapusan dokumen yang masuk untuk unit kerja Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection