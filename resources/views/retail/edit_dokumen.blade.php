@extends('layouts.app')

@section('title', 'Ubah Dokumen ' . $dokumen->nama_dokumen)
@section('header', 'Edit Informasi Dokumen')

@section('content')
@php
    // 🌟 BREAKDOWN NOMOR DOKUMEN LAMA UNTUK MENGISI FORM
    $parts = explode('/', $dokumen->no_dokumen);
    $bulanLama = isset($parts[2]) ? $parts[2] : '06';
    $tahunLama = isset($parts[3]) ? $parts[3] : $dokumen->tahun_buku;
    $jilidLama = isset($parts[4]) ? $parts[4] : '01';

    $namaPerusahaanFix = 'SCK';
    if (isset($perusahaan) && $perusahaan != '') {
        $namaPerusahaanFix = $perusahaan;
    } elseif (isset($dokumen) && $dokumen->perusahaan != '') {
        $namaPerusahaanFix = $dokumen->perusahaan;
    } else {
        $namaPerusahaanFix = request()->segment(3) ?? 'SCK';
    }
@endphp

<div class="max-w-4xl mx-auto select-none animate-fade-in">
    <div class="mb-6">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-[#0c1a3c] transition mb-2 uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar Dokumen
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl shadow-sm font-semibold">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-xl font-bold text-[#0c1a3c]">Form Perbarui Berkas PDF (Retail)</h3>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider font-semibold">Mengubah arsip data untuk <span class="text-blue-600 font-bold">{{ strtoupper(str_replace('-', ' ', $namaPerusahaanFix)) }}</span></p>
        </div>

        <form action="{{ route('dokumen.update', $dokumen->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')

            <input type="hidden" id="perusahaan_nama" name="perusahaan" value="{{ $namaPerusahaanFix }}">

            <div class="space-y-2 mb-6">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nama Perusahaan (Terkunci)</label>
                <input type="text" value="{{ strtoupper(str_replace('-', ' ', $namaPerusahaanFix)) }}" readonly
                    class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 outline-none cursor-not-allowed shadow-inner">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nama Dokumen / Berkas</label>
                    <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}" required placeholder="Masukkan nama dokumen..."
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#0c1a3c] focus:ring-1 focus:ring-[#0c1a3c] outline-none transition-all font-light shadow-sm">
                </div>

                {{-- 🔥 DROPDOWN DINAMIS BERDASARKAN TABEL KATEGORIS DB --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Jenis Dokumen Keuangan</label>
                    <select id="tipe_keuangan" name="tipe_keuangan" required
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#0c1a3c] focus:ring-1 focus:ring-[#0c1a3c] outline-none transition-all font-light shadow-sm text-gray-600">
                        <option value="" data-singkatan="DOC">-- Pilih Jenis Dokumen --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->nama_kategori }}" data-singkatan="{{ $kat->singkatan }}" {{ old('tipe_keuangan', $dokumen->tipe_keuangan) == $kat->nama_kategori ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }} ({{ $kat->singkatan }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Bulan Buku</label>
                    <input type="text" id="bulan_buku" name="bulan_buku" value="{{ old('bulan_buku', $bulanLama) }}" placeholder="Contoh: 06" required
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#0c1a3c] focus:ring-1 focus:ring-[#0c1a3c] outline-none transition-all font-light shadow-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Tahun Buku</label>
                    <input type="text" id="tahun_buku" name="tahun_buku" value="{{ old('tahun_buku', $tahunLama) }}" placeholder="Contoh: 2026" required
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#0c1a3c] focus:ring-1 focus:ring-[#0c1a3c] outline-none transition-all font-light shadow-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Jilid Buku</label>
                    <input type="text" id="jilid_buku" name="jilid_buku" value="{{ old('jilid_buku', $jilidLama) }}" placeholder="Contoh: 01" required
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#0c1a3c] focus:ring-1 focus:ring-[#0c1a3c] outline-none transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-2 mb-6">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nomor Dokumen Resmi (Auto-Generate)</label>
                <input type="text" id="no_dokumen" name="no_dokumen" value="{{ old('no_dokumen', $dokumen->no_dokumen) }}" readonly required
                    class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm font-mono font-bold text-[#0c1a3c] outline-none cursor-not-allowed shadow-inner" placeholder="Auto-Generating...">
            </div>

            <div class="space-y-2 mb-6">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Keterangan / Catatan Tambahan (Boleh Kosong)</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-[#0c1a3c] focus:ring-1 focus:ring-[#0c1a3c] outline-none transition-all font-light shadow-sm resize-none">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-4 border-t border-gray-50 pt-6">
                <a href="javascript:history.back()" class="px-5 py-2.5 text-xs font-bold text-gray-400 hover:text-[#0c1a3c] transition-colors uppercase tracking-wider">Batal</a>
                <button type="submit"
                    class="px-8 py-3 bg-[#0c1a3c] text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md shadow-blue-950/10 hover:bg-[#112554] active:scale-95 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipeKeuangan = document.getElementById('tipe_keuangan');
        const perusahaanNama = document.getElementById('perusahaan_nama');
        const bulanBuku = document.getElementById('bulan_buku');
        const tahunBuku = document.getElementById('tahun_buku');
        const jilidBuku = document.getElementById('jilid_buku');
        const noDokumen = document.getElementById('no_dokumen');

        function generateNomorDokumen() {
            if (!tipeKeuangan || !tipeKeuangan.value) {
                return;
            }
            const selectedOption = tipeKeuangan.options[tipeKeuangan.selectedIndex];
            const singkatanJenis = selectedOption.getAttribute('data-singkatan') || 'DOC';
            const ptNama = perusahaanNama ? perusahaanNama.value.replace(/PT\s*/i, '').trim().toUpperCase() : 'PT';
            
            const bln = bulanBuku.value.trim() || '01';
            const thn = tahunBuku.value.trim() || '2026';
            const jld = jilidBuku.value.trim() || '01';

            noDokumen.value = `${singkatanJenis}/${ptNama}/${bln}/${thn}/${jld}`.toUpperCase();
        }

        tipeKeuangan.addEventListener('change', generateNomorDokumen);
        bulanBuku.addEventListener('input', generateNomorDokumen);
        tahunBuku.addEventListener('input', generateNomorDokumen);
        jilidBuku.addEventListener('input', generateNomorDokumen);

        generateNomorDokumen();
    });
</script>
@endsection