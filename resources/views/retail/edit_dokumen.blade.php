@extends('layouts.app')

@section('title', 'Edit Dokumen ' . $dokumen->nama_dokumen)
@section('header', 'Edit Informasi Dokumen')

@section('content')
@php
    // 🌟 BREAKDOWN NOMOR DOKUMEN LAMA UNTUK MENGISI FORM
    $parts = explode('/', $dokumen->no_dokumen);
    $bulanLama = isset($parts[2]) ? $parts[2] : '06';
    $tahunLama = isset($parts[3]) ? $parts[3] : $dokumen->tahun_buku;
    $jilidLama = isset($parts[4]) ? $parts[4] : '01';
@endphp

<div class="mb-6">
    <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-marica-navy transition mb-2 uppercase tracking-wider">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke Daftar Dokumen
    </a>
</div>

<div class="max-w-2xl bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
        <h3 class="text-sm font-bold text-marica-navy uppercase tracking-wide">Pembaruan Arsip Digital</h3>
        <p class="text-xs text-gray-400 mt-0.5 font-light">Silakan ubah informasi metadata berkas di bawah ini dengan teliti.</p>
    </div>

    <form action="{{ route('dokumen.update', $dokumen->id) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')

        <input type="hidden" id="perusahaan_nama" name="perusahaan" value="{{ $dokumen->perusahaan }}">

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Dokumen / Berkas</label>
            <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}" required
                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-marica-navy focus:ring-1 focus:ring-marica-navy outline-none transition-all font-light shadow-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tipe Keuangan / Kategori</label>
            <select id="tipe_keuangan" name="tipe_keuangan" required
                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-marica-navy focus:ring-1 focus:ring-marica-navy outline-none transition-all font-light shadow-sm text-gray-600">
                <option value="Invoice" {{ old('tipe_keuangan', $dokumen->tipe_keuangan) == 'Invoice' ? 'selected' : '' }}>📄 Invoice / Tagihan</option>
                <option value="Voucher" {{ old('tipe_keuangan', $dokumen->tipe_keuangan) == 'Voucher' ? 'selected' : '' }}>💰 Voucher Kas / Bank</option>
                <option value="Pajak" {{ old('tipe_keuangan', $dokumen->tipe_keuangan) == 'Pajak' ? 'selected' : '' }}>⚖️ Laporan Pajak (PPN/PPh)</option>
                <option value="Rekening Koran" {{ old('tipe_keuangan', $dokumen->tipe_keuangan) == 'Rekening Koran' ? 'selected' : '' }}>📂 Rekening Koran</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bulan Buku</label>
                <input type="text" id="bulan_buku" name="bulan_buku" value="{{ old('bulan_buku', $bulanLama) }}" placeholder="Contoh: 06" required
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-marica-navy focus:ring-1 focus:ring-marica-navy outline-none transition-all font-light shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tahun Buku</label>
                <input type="text" id="tahun_buku" name="tahun_buku" value="{{ old('tahun_buku', $tahunLama) }}" placeholder="Contoh: 2026" required
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-marica-navy focus:ring-1 focus:ring-marica-navy outline-none transition-all font-light shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Jilid Buku</label>
                <input type="text" id="jilid_buku" name="jilid_buku" value="{{ old('jilid_buku', $jilidLama) }}" placeholder="Contoh: 01" required
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-marica-navy focus:ring-1 focus:ring-marica-navy outline-none transition-all font-light shadow-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nomor Dokumen Resmi (Auto-Generate)</label>
            <input type="text" id="no_dokumen" name="no_dokumen" value="{{ old('no_dokumen', $dokumen->no_dokumen) }}" readonly required
                class="w-full px-4 py-3 bg-gray-50 font-mono font-bold text-marica-navy border border-gray-200 focus:outline-none cursor-not-allowed shadow-inner text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Keterangan / Catatan Tambahan</label>
            <textarea name="keterangan" rows="3" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:border-marica-navy focus:ring-1 focus:ring-marica-navy outline-none transition-all font-light shadow-sm" placeholder="Masukkan catatan ringkas...">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-marica-navy text-white text-xs font-bold rounded-xl uppercase tracking-wider hover:bg-opacity-90 transition-all duration-200 active:scale-95 shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
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
            let kodeJenis = '...';
            if (tipeKeuangan.value === 'Invoice') kodeJenis = 'INV';
            else if (tipeKeuangan.value === 'Voucher') kodeJenis = 'VOU';
            else if (tipeKeuangan.value === 'Pajak') kodeJenis = 'TAX';
            else if (tipeKeuangan.value === 'Rekening Koran') kodeJenis = 'RK';

            let kodePT = '...';
            let namaPT = perusahaanNama.value.toUpperCase();
            if (namaPT.includes('SCK')) kodePT = 'SCK';
            else if (namaPT.includes('SBS')) kodePT = 'SBS';

            const bln = bulanBuku.value.trim() || '01';
            const thn = tahunBuku.value.trim() || '2026';
            const jld = jilidBuku.value.trim() || '01';

            if(tipeKeuangan.value) {
                noDokumen.value = `${kodeJenis}/${kodePT}/${bln}/${thn}/${jld}`.toUpperCase();
            }
        }

        tipeKeuangan.addEventListener('change', generateNomorDokumen);
        bulanBuku.addEventListener('input', generateNomorDokumen);
        tahunBuku.addEventListener('input', generateNomorDokumen);
        jilidBuku.addEventListener('input', generateNomorDokumen);
    });
</script>
@endsection