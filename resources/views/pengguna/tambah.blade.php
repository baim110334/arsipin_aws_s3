@extends('layouts.app')

@section('title', 'Tambah Pengguna')
@section('header', 'Tambah Pengguna Baru')

@section('content')
<div class="space-y-6 max-w-xl mx-auto select-none animate-fade-in">
    
    <div class="mb-2">
        <h3 class="text-lg font-black text-[#0c1a3c] tracking-tight">Registrasi Anggota Operasional</h3>
        <p class="text-xs text-gray-400 mt-0.5 font-light">Daftarkan akun staf baru untuk mendapatkan otentikasi hak akses berkas finansial.</p>
    </div>

    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
        
        @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-xl shadow-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-center gap-1.5"><i class="fa-solid fa-circle-xmark text-sm"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Nama Lengkap Anggota</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-id-card absolute left-4 text-gray-300 text-sm"></i>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Masukkan nama lengkap staf" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Username Identitas (Untuk Login)</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-user-gear absolute left-4 text-gray-300 text-sm"></i>
                    <input type="text" name="username" value="{{ old('username') }}" required placeholder="Contoh: angela_retail" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-mono font-bold tracking-tight shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Alamat Email Resmi</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-envelope absolute left-4 text-gray-300 text-sm"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="staf@arsipin.com" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Nomor Handphone Kontak (Opsional)</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-phone absolute left-4 text-gray-300 text-sm"></i>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Tingkatan Otoritas Hak Akses</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-user-shield absolute left-4 text-gray-300 text-sm"></i>
                    <select name="role" required 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-600 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-bold cursor-pointer shadow-sm">
                        <option value="admin">Admin Pusat</option>
                        <option value="pegawai-retail">Retail Staff</option>
                        <option value="pegawai-komersial">Commercial Staff</option>
                        <option value="kepala-bu">Kepala Business Unit</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Penempatan Wilayah Otoritas Bisnis Unit</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-building-flag absolute left-4 text-gray-300 text-sm"></i>
                    <select name="bisnis_unit" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-600 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-bold cursor-pointer shadow-sm">
                        <option value="">-- KOSONG / TANPA WILAYAH (KHUSUS ADMIN) --</option>
                        @foreach($bisnisUnits as $bu)
                            <option value="{{ $bu->nama_bisnis_unit }}" {{ old('bisnis_unit') == $bu->nama_bisnis_unit ? 'selected' : '' }}>
                                {{ strtoupper(str_replace('-', ' ', $bu->nama_bisnis_unit)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="text-[9px] text-gray-400 font-medium italic mt-1 ml-1">*Wajib dikunci jika mendaftarkan role Staf Unit atau Kepala BU.</p>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Kata Sandi Akun Pertama</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-lock absolute left-4 text-gray-300 text-sm"></i>
                    <input type="password" name="password" required placeholder="Minimal berkapasitas 6 karakter" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="/kelola-akun" class="px-5 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-black text-gray-500 uppercase tracking-wider transition text-center flex-shrink-0">
                    Batal
                </a>
                <button type="submit" class="px-5 py-3 bg-[#0c1a3c] text-white rounded-xl text-xs font-black hover:bg-[#112554] active:scale-[0.99] uppercase tracking-wider transition flex-1 shadow-md shadow-blue-950/10">
                    Simpan Akun Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection