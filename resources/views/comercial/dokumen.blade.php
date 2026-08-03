@extends('layouts.app')

@section('title', 'Dokumen ' . $nama_pt)
@section('header', $nama_pt)

@section('content')
<div class="space-y-6 select-none animate-fade-in">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4">
        <div>
            <a href="/comercial/{{ $bisnis_unit }}" class="inline-flex items-center gap-1.5 text-xs font-black text-gray-400 hover:text-[#0c1a3c] transition-all uppercase tracking-wider mb-1">
                <i class="fa-solid fa-arrow-left text-sm"></i> Kembali ke Perusahaan
            </a>
            <h3 class="text-xs text-gray-400 font-normal">
                Kategori: Komersial &gt; <span class="uppercase font-black text-sky-500 tracking-wide">{{ str_replace('-', ' ', $nama_bu) }}</span>
            </h3>
        </div>
    </div>

    <!-- ACTION BAR -->
    <div class="flex flex-col md:flex-row gap-3 justify-between items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto flex-1">
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs opacity-50"></i>
                </span>
                <input type="text" id="input_pencarian" placeholder="Cari nama / nomor dokumen..." 
                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50/60 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-inner">
            </div>

            <select id="filter_jenis" class="w-full sm:w-52 py-2.5 px-3 bg-gray-50/60 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white text-gray-600 font-bold">
                <option value="">-- Semua Jenis Dokumen --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ strtolower($kat->nama_kategori) }}">{{ $kat->nama_kategori }} ({{ $kat->singkatan }})</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            @if(Auth::user()->role === 'admin')
                <button onclick="bukaModalKategori()" type="button" class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-black rounded-xl text-xs border border-amber-200 transition-all flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-folder-plus"></i> + Jenis Baru
                </button>
            @endif

            @if(Auth::user()->role === 'admin' || strtolower(Auth::user()->bisnis_unit) === strtolower($bisnis_unit))
                <a href="{{ route('comercial.upload', [$bisnis_unit, $nama_pt]) }}" 
                   class="px-5 py-2.5 bg-[#0c1a3c] text-white font-black rounded-xl text-xs uppercase tracking-wider text-center hover:bg-[#112554] active:scale-[0.98] transition-all shadow-md shadow-blue-950/10 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-sky-400 text-sm"></i> Tambah Dokumen
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-sky-50 border border-sky-100 text-sky-700 text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm animate-fade-in">
            <i class="fa-solid fa-circle-check text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tabel_dokumen">
                <thead>
                    <tr class="text-[10px] uppercase text-gray-400 font-black border-b border-gray-50 bg-gray-50/50 tracking-wider">
                        <th class="px-6 py-4 w-1/4">Informasi Dokumen</th>
                        <th class="px-6 py-4 w-1/4">Keterangan / Catatan</th>
                        <th class="px-6 py-4">Nama File Asli</th>
                        <th class="px-6 py-4">Ukuran</th>
                        <th class="px-6 py-4">Tanggal Dokumen</th>
                        <th class="px-6 py-4">Status / Tipe</th>
                        <th class="px-6 py-4 text-center">Aksi Otoritas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-600">
                    @forelse($list_dokumen as $dokumen)
                        <tr class="hover:bg-gray-50/30 transition-colors group baris-dokumen" data-jenis="{{ strtolower($dokumen->tipe_keuangan) }}">
                            
                            <td class="px-6 py-4">
                                <div class="font-bold text-[#0c1a3c] text-xs flex items-center gap-2">
                                    {{ $dokumen->nama_dokumen }}
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 font-mono text-[9px] font-bold rounded">
                                        TH {{ $dokumen->tahun_buku ?? '2026' }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5 font-mono font-bold tracking-tight">{{ $dokumen->no_dokumen }}</div>
                            </td>

                            <td class="px-6 py-4 max-w-xs">
                                @if($dokumen->keterangan)
                                    <span class="cursor-help text-[11px] font-medium bg-gray-50 text-slate-500 px-2.5 py-1.5 rounded-lg border border-gray-200/50 block truncate shadow-sm" title="{{ $dokumen->keterangan }}">
                                         {{ \Illuminate\Support\Str::limit($dokumen->keterangan, 35, '...') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300 italic font-light">Tidak ada catatan</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-file-pdf text-red-500 text-sm"></i>
                                    <a href="{{ route('dokumen.preview', $dokumen->id) }}" target="_blank" class="truncate max-w-[160px] text-[#0c1a3c] hover:text-sky-500 hover:underline font-bold" title="{{ $dokumen->file_name_original }}">
                                        {{ $dokumen->file_name_original }}
                                    </a>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 font-mono font-bold text-slate-400 text-[11px]">{{ $dokumen->file_size ?? '-' }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-500">
                                {{ $dokumen->created_at ? $dokumen->created_at->format('d M Y') : 'Tanggal tidak tersedia' }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-sky-50 text-sky-700 border border-sky-100 rounded-full text-[9px] font-black uppercase tracking-wider">
                                    📂 {{ $dokumen->tipe_keuangan ?? 'Umum' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'pegawai-komersial' && strtolower(Auth::user()->bisnis_unit) === strtolower($bisnis_unit)))
                                        <a href="{{ route('dokumen.edit', $dokumen->id) }}" class="inline-flex items-center text-[11px] font-black text-[#0c1a3c] bg-gray-50 hover:bg-[#0c1a3c] hover:text-white border border-gray-200 px-2.5 py-1 rounded-lg transition-all shadow-sm">
                                            <i class="fa-solid fa-file-pen text-[10px] opacity-70 mr-0.5"></i> Edit
                                        </a>

                                        @if(Auth::user()->role == 'admin')
                                            <form action="{{ route('dokumen.destroy', $dokumen->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas dokumen ini secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center text-[11px] font-black text-red-600 bg-red-50 hover:bg-red-600 hover:text-white border border-red-200/60 px-2.5 py-1 rounded-lg transition-all shadow-sm cursor-pointer">
                                                    <i class="fa-solid fa-trash-can text-[10px] opacity-70 mr-0.5"></i> Hapus
                                                </button>
                                            </form>
                                        @else
                                            @if($dokumen->status === 'pending_hapus')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg text-[9px] font-black uppercase tracking-wider animate-pulse shadow-sm">
                                                    <i class="fa-solid fa-hourglass-start"></i> Diajukan Hapus
                                                </span>
                                            @else
                                                <form action="{{ route('dokumen.ajukan-hapus', $dokumen->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengajukan permohonan hapus berkas komersial ini ke Kepala BU?')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl border border-gray-200/50 hover:border-red-200 transition inline-flex items-center justify-center cursor-pointer shadow-sm" title="Ajukan Hapus">
                                                        <i class="fa-solid fa-trash-arrow-up text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-[10px] font-mono font-bold text-gray-400 uppercase tracking-wider bg-gray-50 border border-gray-200 px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                                            <i class="fa-solid fa-lock text-amber-500"></i> Read Only
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic font-light">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-folder-open text-2xl text-gray-200"></i>
                                    <span>Belum ada berkas dokumen komersial yang diarsipkan untuk perusahaan ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

    document.addEventListener('DOMContentLoaded', function() {
        const inputCari = document.getElementById('input_pencarian');
        const filterJenis = document.getElementById('filter_jenis');
        const barisDokumen = document.querySelectorAll('.baris-dokumen');

        function filterTabel() {
            const kataKunci = inputCari.value.toLowerCase();
            const jenisDipilih = filterJenis.value.toLowerCase();

            barisDokumen.forEach(row => {
                const teksBaris = row.innerText.toLowerCase();
                const jenisDokumen = row.getAttribute('data-jenis');

                const cocokKataKunci = teksBaris.includes(kataKunci);
                const cocokJenis = (jenisDipilih === '') || (jenisDokumen === jenisDipilih);

                if (cocokKataKunci && cocokJenis) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if(inputCari) inputCari.addEventListener('input', filterTabel);
        if(filterJenis) filterJenis.addEventListener('change', filterTabel);
    });
</script>
@endsection