@extends('layouts.app')

@section('title', 'Daftar Perusahaan ' . $bu->nama_bisnis_unit)
@section('header', 'Komersial - ' . $bu->nama_bisnis_unit)

@section('content')
<div class="space-y-6 select-none animate-fade-in">
    
    <div class="border-b border-gray-100 pb-4 flex justify-between items-end">
        <div>
            <a href="/comercial" class="inline-flex items-center gap-1.5 text-xs font-black text-gray-400 hover:text-[#0c1a3c] transition-all uppercase tracking-wider mb-1">
                <i class="fa-solid fa-arrow-left text-sm"></i> Kembali ke Bisnis Unit
            </a>
            <h2 class="text-xl font-black text-[#0c1a3c] tracking-tight flex items-center gap-2 mt-1">
                {{ $bu->nama_bisnis_unit }}
            </h2>
            <p class="text-xs text-gray-400 mt-1 font-light max-w-2xl">
                {{ $bu->deskripsi ?? 'Silakan pilih entitas komersial (PT) di bawah ini untuk mengakses repositori berkas.' }}
            </p>
        </div>

        @if(auth()->user()->role === 'admin')
            <div class="flex items-center gap-2">
                <button onclick="bukaModalEditBu()" title="Edit Bisnis Unit" class="px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Unit
                </button>
                <form action="{{ route('bisnis-unit.destroy', $bu->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Bisnis Unit ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Hapus Bisnis Unit" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can"></i> Hapus Unit
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-2xl flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @foreach($perusahaans as $pt)
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-sky-300 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between h-56 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 text-slate-50 opacity-[0.03] text-8xl font-black group-hover:opacity-[0.06] group-hover:scale-110 transition-all duration-300">
                    <i class="fa-solid fa-building"></i>
                </div>

                <div>
                    <div class="flex justify-between items-start">
                        <div class="w-11 h-11 bg-gray-50 text-slate-400 rounded-2xl border border-gray-100/50 flex items-center justify-center flex-shrink-0 group-hover:bg-sky-50 group-hover:text-[#0ea5e9] transition-all duration-300 shadow-sm">
                            <i class="fa-solid fa-building text-base"></i>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="px-2.5 py-0.5 bg-slate-50 text-slate-400 border border-slate-100 rounded-full text-[9px] font-black uppercase tracking-widest group-hover:bg-sky-50 group-hover:text-sky-600 group-hover:border-sky-100 transition-all duration-300">COMMERCIAL</span>
                            @if(auth()->user()->role === 'admin')
                                <div class="flex items-center gap-0.5 ml-1 z-10">
                                    <button onclick="bukaModalEditPt('{{ $pt->id }}', '{{ $pt->nama_pt }}')" class="text-gray-300 hover:text-amber-500 p-1 transition-colors" title="Edit PT">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('perusahaan.destroy', $pt->id) }}" method="POST" onsubmit="return confirm('Hapus perusahaan {{ $pt->nama_pt }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 p-1 transition-colors" title="Hapus PT">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <h4 class="font-black text-[#0c1a3c] text-xl mt-5 tracking-tight group-hover:text-sky-500 transition-colors duration-200">
                        {{ $pt->nama_pt }}
                    </h4>
                </div>

                <a href="/comercial/{{ $slug_bu }}/{{ Str::slug($pt->nama_pt) }}" class="flex justify-between items-center border-t border-gray-50 pt-3 mt-4">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase tracking-wider font-black text-gray-400">Akses Direktori</span>
                        <span class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1">
                            <i class="fa-regular fa-file-pdf text-red-500"></i> Buka repositori berkas
                        </span>
                    </div>
                    <div class="w-7 h-7 rounded-full bg-gray-50 text-[#0c1a3c] flex items-center justify-center group-hover:bg-[#0c1a3c] group-hover:text-white group-hover:translate-x-1 transition-all duration-300 shadow-sm">
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </a>
            </div>
        @endforeach

        @if(auth()->user()->role === 'admin')
            <button onclick="bukaModalPt()" type="button" class="bg-white/60 hover:bg-white p-6 rounded-3xl border-2 border-dashed border-gray-200 hover:border-sky-400 shadow-none hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center justify-center h-56 text-center cursor-pointer">
                <div class="w-12 h-12 bg-sky-50 text-sky-500 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-plus text-lg"></i>
                </div>
                <h3 class="text-sm font-extrabold text-[#0c1a3c] group-hover:text-sky-600 transition-colors">Tambah Perusahaan Baru</h3>
                <p class="text-[10px] text-gray-400 mt-1 max-w-[200px]">Klik untuk mendaftarkan entitas PT baru di bawah unit {{ $bu->nama_bisnis_unit }}</p>
            </button>
        @endif

    </div>
</div>

{{-- MODAL TAMBAH PT --}}
<div id="modalTambahPt" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-black text-[#0c1a3c]">Tambah Perusahaan Komersial</h3>
            <button onclick="tutupModalPt()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('perusahaan.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="bisnis_unit_id" value="{{ $bu->id }}">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Perusahaan (PT)</label>
                <input type="text" name="nama_pt" placeholder="Contoh: PT CPT / PT MHM / PT SBS" required class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-sky-500 uppercase font-bold">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="tutupModalPt()" class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-sky-500 text-white text-xs font-bold rounded-xl shadow-md">Simpan PT</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PT --}}
<div id="modalEditPt" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-black text-[#0c1a3c]">Edit Nama Perusahaan</h3>
            <button onclick="tutupModalEditPt()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditPt" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Perusahaan (PT)</label>
                <input type="text" id="editNamaPt" name="nama_pt" required class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-sky-500 uppercase font-bold">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="tutupModalEditPt()" class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 text-white text-xs font-bold rounded-xl shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT BU --}}
<div id="modalEditBu" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-black text-[#0c1a3c]">Edit Bisnis Unit</h3>
            <button onclick="tutupModalEditBu()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('bisnis-unit.update', $bu->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Bisnis Unit</label>
                <input type="text" name="nama_bisnis_unit" value="{{ $bu->nama_bisnis_unit }}" required class="w-full text-xs p-3 border border-gray-200 rounded-xl uppercase font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Unit</label>
                <textarea name="deskripsi" rows="3" class="w-full text-xs p-3 border border-gray-200 rounded-xl">{{ $bu->deskripsi }}</textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="tutupModalEditBu()" class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 text-white text-xs font-bold rounded-xl shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalPt() { document.getElementById('modalTambahPt').classList.remove('hidden'); }
    function tutupModalPt() { document.getElementById('modalTambahPt').classList.add('hidden'); }
    function bukaModalEditPt(id, namaPt) {
        document.getElementById('formEditPt').action = '/perusahaan/' + id + '/update';
        document.getElementById('editNamaPt').value = namaPt;
        document.getElementById('modalEditPt').classList.remove('hidden');
    }
    function tutupModalEditPt() { document.getElementById('modalEditPt').classList.add('hidden'); }
    function bukaModalEditBu() { document.getElementById('modalEditBu').classList.remove('hidden'); }
    function tutupModalEditBu() { document.getElementById('modalEditBu').classList.add('hidden'); }
</script>
@endsection