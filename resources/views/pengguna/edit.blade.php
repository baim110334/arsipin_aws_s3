@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('header', 'Perbarui Data Akun')

@section('content')
<div class="space-y-6 max-w-xl mx-auto select-none animate-fade-in">
    
    <div class="mb-2">
        <h3 class="text-lg font-black text-[#0c1a3c] tracking-tight">Perbarui Informasi Otoritas Pengguna</h3>
        <p class="text-xs text-gray-400 mt-0.5 font-light">Ubah detail kredensial atau mutasi penempatan bisnis unit akun yang bersangkutan.</p>
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

        <form action="{{ route('user.update', $user->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Nama Lengkap Anggota</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-id-card absolute left-4 text-gray-300 text-sm"></i>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Username Identitas (Read-only)</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-user-lock absolute left-4 text-gray-400 text-sm"></i>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required readonly
                        class="w-full pl-11 pr-4 py-3 bg-slate-100/80 border border-gray-200 rounded-xl outline-none text-slate-400 text-xs font-mono font-bold tracking-tight shadow-inner cursor-not-allowed">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Alamat Email Resmi</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-envelope absolute left-4 text-gray-300 text-sm"></i>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Nomor Handphone Kontak</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-phone absolute left-4 text-gray-300 text-sm"></i>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Tingkatan Otoritas Hak Akses</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-user-shield absolute left-4 text-gray-300 text-sm"></i>
                    <select name="role" required 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-600 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-bold cursor-pointer shadow-sm">
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin Pusat</option>
                        <option value="pegawai-retail" {{ ($user->role == 'pegawai-retail' || $user->role == 'pegawai') ? 'selected' : '' }}>Retail Staff</option>
                        <option value="pegawai-komersial" {{ $user->role == 'pegawai-komersial' ? 'selected' : '' }}>Commercial Staff</option>
                        <option value="kepala-bu" {{ $user->role == 'kepala-bu' ? 'selected' : '' }}>Kepala Business Unit</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Penempatan Wilayah Otoritas Bisnis Unit</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-building-flag absolute left-4 text-gray-300 text-sm"></i>
                    <select name="bisnis_unit" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-600 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-bold cursor-pointer shadow-sm">
                        <option value="" {{ is_null($user->bisnis_unit) ? 'selected' : '' }}>-- KOSONG / TANPA WILAYAH (KHUSUS ADMIN) --</option>
                        <option value="spbu" {{ $user->bisnis_unit == 'spbu' ? 'selected' : '' }}>SPBU</option>
                        <option value="swalayan" {{ $user->bisnis_unit == 'swalayan' ? 'selected' : '' }}>SWALAYAN</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Ganti Kata Sandi Baru (Kosongkan jika tidak diganti)</label>
                <div class="relative flex items-center">
                    <i class="fa-solid fa-key absolute left-4 text-gray-300 text-sm"></i>
                    <input type="password" name="password" placeholder="Isi kotak jika ingin merestart password baru" 
                        class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-xs focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="/kelola-akun" class="px-5 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-black text-gray-500 uppercase tracking-wider transition text-center flex-shrink-0">
                    Batal
                </a>
                <button type="submit" class="px-5 py-3 bg-[#0c1a3c] text-white rounded-xl text-xs font-black hover:bg-[#112554] active:scale-[0.99] uppercase tracking-wider transition flex-1 shadow-md shadow-blue-950/10">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection