@extends('layouts.app')

@section('title', 'Ubah Informasi Dokumen')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-marica-navy">Ubah Informasi Dokumen</h3>
        <p class="text-xs text-gray-400 mt-1">Silakan perbarui informasi teks dokumen di bawah ini tanpa mengubah berkas berkas asli.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="/dokumen/update/{{ $dokumen->id }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nama Dokumen -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-600 ml-1">Nama Dokumen</label>
                    <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}" required
                        class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-marica-navy/10 focus:border-marica-navy outline-none text-sm">
                </div>

                <!-- Nomor Dokumen -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-600 ml-1">Nomor Dokumen</label>
                    <input type="text" name="no_dokumen" value="{{ old('no_dokumen', $dokumen->no_dokumen) }}" required
                        class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-marica-navy/10 focus:border-marica-navy outline-none text-sm">
                </div>

                <!-- Tipe Keuangan -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-600 ml-1">Jenis Dokumen Keuangan</label>
                    <select name="tipe_keuangan" required
                        class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-marica-navy/10 focus:border-marica-navy outline-none text-sm text-gray-700">
                        <option value="Invoice" {{ $dokumen->tipe_keuangan == 'Invoice' ? 'selected' : '' }}>Invoice / Tagihan</option>
                        <option value="Kuitansi" {{ $dokumen->tipe_keuangan == 'Kuitansi' ? 'selected' : '' }}>Kuitansi Pembayaran</option>
                        <option value="Pajak" {{ $dokumen->tipe_keuangan == 'Pajak' ? 'selected' : '' }}>Laporan Pajak (PPN/PPh)</option>
                        <option value="Slip Gaji" {{ $dokumen->tipe_keuangan == 'Slip Gaji' ? 'selected' : '' }}>Slip Gaji / Reimbursement</option>
                        <option value="Lainnya" {{ $dokumen->tipe_keuangan == 'Lainnya' ? 'selected' : '' }}>Dokumen Finansial Lainnya</option>
                    </select>
                </div>

                <!-- Tahun Buku -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-600 ml-1">Tahun Anggaran / Tahun Buku</label>
                    <input type="number" name="tahun_buku" value="{{ old('tahun_buku', $dokumen->tahun_buku) }}" min="2020" max="2035" required
                        class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-marica-navy/10 focus:border-marica-navy outline-none text-sm">
                </div>
            </div>

            <!-- Keterangan -->
            <div class="space-y-2 mb-6">
                <label class="text-sm font-bold text-gray-600 ml-1">Keterangan / Catatan Tambahan</label>
                <textarea name="keterangan" rows="3" class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-marica-navy/10 focus:border-marica-navy outline-none text-sm resize-none">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
            </div>

            <!-- Aksi Tombol -->
            <div class="flex items-center justify-end gap-4 border-t border-gray-50 pt-6">
                <button type="button" onclick="window.history.back()" class="px-6 py-3 text-sm font-bold text-gray-400 hover:text-gray-600 transition">Batal</button>
                <button type="submit" class="px-10 py-3 bg-marica-navy text-white rounded-xl font-bold text-sm shadow-md transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection