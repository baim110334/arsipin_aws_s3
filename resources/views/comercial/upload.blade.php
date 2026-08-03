@extends('layouts.app')

@section('title', 'Unggah Dokumen Keuangan Komersial')
@section('header', 'Pengarsipan Dokumen Komersial: ' . $perusahaan)

@section('content')
<div class="max-w-4xl mx-auto select-none animate-fade-in">
    
    <div class="mb-6">
        <a href="{{ route('comercial.dokumen', [$bisnis_unit, $perusahaan]) }}" class="inline-flex items-center gap-2 text-xs font-black text-gray-400 hover:text-[#0c1a3c] transition-all uppercase tracking-wider">
            <i class="fa-solid fa-arrow-left text-sm"></i> Kembali ke Daftar Dokumen
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold rounded-xl shadow-sm">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-xl shadow-sm">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="flex items-center gap-1.5"><i class="fa-solid fa-circle-xmark text-sm"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-xl font-black text-[#0c1a3c] tracking-tight">Form Unggah Berkas PDF (Komersial)</h3>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-wallet text-sky-500"></i> Pastikan dokumen yang diunggah berkaitan dengan Otoritas Komersial / Commercial Divisi
            </p>
        </div>

        <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            
            <input type="hidden" name="bisnis_unit" value="{{ $bisnis_unit }}">
            <input type="hidden" id="perusahaan_nama" name="perusahaan" value="{{ $perusahaan }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Nama Dokumen / Berkas</label>
                    <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen') }}" placeholder="Contoh: Invoice Logistik April" required
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>

                {{-- DROPDOWN DINAMIS --}}
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Jenis Dokumen Keuangan</label>
                        @if(Auth::user()->role === 'admin')
                            <button type="button" onclick="bukaModalKategori()" class="text-[10px] font-black text-sky-600 hover:text-sky-800 flex items-center gap-1">
                                <i class="fa-solid fa-plus-circle"></i> Tambah Jenis Baru
                            </button>
                        @endif
                    </div>
                    <select id="tipe_keuangan" name="tipe_keuangan" required class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-bold text-gray-600 shadow-sm">
                        <option value="" data-singkatan="DOC">-- Pilih Jenis Dokumen --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->nama_kategori }}" data-singkatan="{{ $kat->singkatan }}">
                                {{ $kat->nama_kategori }} ({{ $kat->singkatan }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Bulan Buku</label>
                    <input type="text" id="bulan_buku" name="bulan_buku" value="{{ old('bulan_buku', '06') }}" placeholder="Contoh: 06" required
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Tahun Anggaran / Buku</label>
                    <input type="text" id="tahun_buku" name="tahun_buku" value="{{ old('tahun_buku', '2026') }}" placeholder="Contoh: 2026" required
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Jilid Buku</label>
                    <input type="text" id="jilid_buku" name="jilid_buku" value="{{ old('jilid_buku', '01') }}" placeholder="Contoh: 01" required
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Nomor Dokumen Resmi (Auto-Generate)</label>
                <input type="text" id="no_dokumen" name="no_dokumen" value="{{ old('no_dokumen') }}" readonly required
                    class="w-full px-4 py-3.5 bg-gray-100 border border-gray-200 rounded-xl text-xs font-mono font-bold text-[#0c1a3c] outline-none cursor-not-allowed shadow-inner transition-all" placeholder="PILIH JENIS DOKUMEN TERLEBIH DAHULU">
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Keterangan / Catatan Tambahan (Boleh Kosong)</label>
                <textarea name="keterangan" rows="3" placeholder="Masukkan ringkasan atau catatan penting berkas komersial..."
                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm resize-none">{{ old('keterangan') }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider ml-1">Pilih Berkas (Format PDF Resmi)</label>
                <div class="relative flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50 hover:bg-white hover:border-[#0c1a3c] transition-all group cursor-pointer">
                    <input type="file" id="file_dokumen_input" name="file_dokumen" accept="application/pdf" required
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="text-center space-y-2 pointer-events-none" id="upload-placeholder">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center mx-auto group-hover:bg-[#0c1a3c] group-hover:text-white transition-all duration-300 shadow-sm">
                            <i class="fa-solid fa-file-pdf text-lg"></i>
                        </div>
                        <p class="text-xs font-bold text-[#0c1a3c]" id="file-name-text">Tarik berkas PDF kesini atau klik untuk mencari</p>
                        <p class="text-[10px] text-gray-400 font-light">*Maksimal ukuran file: 3MB (Format berkas selain PDF akan otomatis ditolak sistem)</p>
                    </div>
                </div>
                
                <div id="error_file_announcement" class="hidden mt-2 p-3 bg-red-50 border border-red-100 text-red-600 rounded-xl text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span id="error_file_message">Format berkas salah!</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-50 pt-6">
                <button type="reset" class="px-5 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-black text-gray-500 uppercase tracking-wider transition text-center cursor-pointer">
                    Reset Form
                </button>
                <button type="submit" id="btn_submit_arsip"
                    class="px-6 py-3 bg-[#0c1a3c] text-white rounded-xl font-black text-xs uppercase tracking-wider hover:bg-[#112554] active:scale-[0.98] transition-all shadow-md shadow-blue-950/10 flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up text-sky-400"></i> Simpan ke Arsip Digital
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TAMBAH KATEGORI --}}
<div id="modalTambahKategori" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-black text-[#0c1a3c]"><i class="fa-solid fa-folder-plus text-sky-500"></i> Tambah Jenis Dokumen Baru</h3>
            <button onclick="tutupModalKategori()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('kategori.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Jenis Dokumen</label>
                <input type="text" name="nama_kategori" placeholder="Contoh: Rekening Koran / Tax Invoice" required class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-sky-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Kode Singkatan (Maks 10 Karakter)</label>
                <input type="text" name="singkatan" placeholder="Contoh: RKB / TXI / VOU" maxlength="10" required class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-sky-500 uppercase font-bold">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="tutupModalKategori()" class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-sky-500 text-white text-xs font-bold rounded-xl shadow-md">Simpan Jenis</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalKategori() { document.getElementById('modalTambahKategori').classList.remove('hidden'); }
    function tutupModalKategori() { document.getElementById('modalTambahKategori').classList.add('hidden'); }

    document.addEventListener('DOMContentLoaded', function () {
        const tipeKeuangan = document.getElementById('tipe_keuangan');
        const perusahaanNama = document.getElementById('perusahaan_nama');
        const bulanBuku = document.getElementById('bulan_buku');
        const tahunBuku = document.getElementById('tahun_buku');
        const jilidBuku = document.getElementById('jilid_buku');
        const noDokumen = document.getElementById('no_dokumen');
        
        const fileInput = document.getElementById('file_dokumen_input');
        const fileNameText = document.getElementById('file-name-text');
        const errorBox = document.getElementById('error_file_announcement');
        const errorMessage = document.getElementById('error_file_message');
        const btnSubmit = document.getElementById('btn_submit_arsip');

        function generateNoDokumen() {
            if (!tipeKeuangan || !tipeKeuangan.value) {
                noDokumen.value = 'PILIH JENIS DOKUMEN TERLEBIH DAHULU';
                return;
            }

            const selectedOption = tipeKeuangan.options[tipeKeuangan.selectedIndex];
            const singkatanJenis = selectedOption.getAttribute('data-singkatan') || 'DOC';
            const ptNama = perusahaanNama ? perusahaanNama.value.replace(/PT\s*/i, '').trim().toUpperCase() : 'PT';
            
            const bulan = bulanBuku ? bulanBuku.value || '01' : '01';
            const tahun = tahunBuku ? tahunBuku.value || '2026' : '2026';
            const jilid = jilidBuku ? jilidBuku.value || '01' : '01';
            
            noDokumen.value = `${singkatanJenis}/${ptNama}/${bulan}/${tahun}/${jilid}`.toUpperCase();
        }

        tipeKeuangan.addEventListener('change', generateNoDokumen);
        bulanBuku.addEventListener('input', generateNoDokumen);
        tahunBuku.addEventListener('input', generateNoDokumen);
        jilidBuku.addEventListener('input', generateNoDokumen);

        function periksaDanSuntikFile(file) {
            if (file) {
                const fileName = file.name;
                const fileExtension = fileName.split('.').pop().toLowerCase();
                const fileSize = file.size / 1024 / 1024; 

                if (fileExtension !== 'pdf') {
                    errorMessage.innerText = `Format "${fileExtension.toUpperCase()}" ditolak! Sistem hanya menerima file .PDF resmi.`;
                    errorBox.classList.remove('hidden');
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                    fileInput.value = ''; 
                    return;
                }

                if (fileSize > 3) { 
                    errorMessage.innerText = `Berkas terlalu besar (${fileSize.toFixed(2)} MB)! Maksimal batas tampung sistem adalah 3.00 MB.`;
                    errorBox.classList.remove('hidden');
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                    fileInput.value = '';
                    return;
                }

                errorBox.classList.add('hidden');
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                fileNameText.innerText = "Berkas Terpilih: " + fileName;
                fileNameText.classList.add('text-sky-500');
            }
        }

        fileInput.addEventListener('change', function () {
            periksaDanSuntikFile(this.files[0]);
        });
    });
</script>
@endsection