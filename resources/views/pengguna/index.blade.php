@extends('layouts.app')

@section('title', 'Kelola Akun')
@section('header', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6 select-none">

    @if(session('success'))
        <div class="p-4 bg-sky-50 border border-sky-100 text-sky-700 text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm animate-fade-in">
            <i class="fa-solid fa-circle-check text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form action="/kelola-akun" method="GET" class="relative w-full sm:w-96">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-sm opacity-50"></i>
            </span>
            <input type="text" name="cari" value="{{ request('cari') }}" 
                placeholder="Cari nama, username, atau email staf..." 
                class="w-full pl-11 pr-4 py-2.5 bg-gray-50/60 border border-gray-200 rounded-xl text-xs outline-none focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-inner">
        </form>

        <a href="/kelola-akun/tambah" class="w-full sm:w-auto px-6 py-2.5 bg-[#0c1a3c] text-white font-bold rounded-xl text-xs uppercase tracking-wider text-center hover:bg-[#112554] active:scale-[0.98] transition-all shadow-md shadow-blue-950/10 flex items-center justify-center gap-2">
            <i class="fa-solid fa-user-plus text-[11px] text-sky-400"></i> Tambah Anggota
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto overflow-y-auto max-h-[420px] scrollbar-thin">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white z-20 shadow-[0_1px_0_0_rgba(243,244,246,1)]">
                    <tr class="text-[10px] uppercase text-gray-400 font-black border-b border-gray-50 bg-gray-50/50 tracking-wider">
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Username Identitas</th>
                        <th class="px-6 py-4">Kontrol Kontak Email</th>
                        <th class="px-6 py-4">Otoritas Otorisasi Unit</th>
                        <th class="px-6 py-4">Role Hak Akses</th>
                        <th class="px-6 py-4 text-center">Tindakan Otoritas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-600">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            
                            <td class="px-6 py-4 font-bold text-[#0c1a3c] text-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-[#0c1a3c] font-black uppercase text-[10px] border border-slate-200/30">
                                        {{ substr($user->nama_lengkap, 0, 2) }}
                                    </div>
                                    <span>{{ $user->nama_lengkap }}</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 font-mono font-bold text-slate-400 text-[11px] tracking-tight">{{ $user->username }}</td>
                            
                            <td class="px-6 py-4">
                                <span class="block font-semibold text-[#0c1a3c]">{{ $user->email }}</span>
                                <span class="block text-[10px] text-gray-400 font-mono mt-0.5"><i class="fa-solid fa-phone text-[9px] opacity-60 mr-1"></i>{{ $user->no_hp ?? '-' }}</span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 font-mono text-[10px] font-bold uppercase rounded border border-gray-200/20">
                                    {{ $user->bisnis_unit ?? 'SEMUA UNIT (ADMIN)' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($user->role == 'admin')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-sky-950 text-sky-400 border border-sky-900/40 rounded-full text-[9px] font-black uppercase tracking-wider">
                                        <i class="fa-solid fa-key text-[8px]"></i> Admin Pusat
                                    </span>
                                @elseif($user->role == 'pegawai-retail' || $user->role == 'pegawai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[9px] font-black uppercase tracking-wider">
                                        <i class="fa-solid fa-shop text-[8px]"></i> Retail Staff
                                    </span>
                                @elseif($user->role == 'pegawai-komersial')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[9px] font-black uppercase tracking-wider">
                                        <i class="fa-solid fa-wallet text-[8px]"></i> Commercial Staff
                                    </span>
                                @elseif($user->role == 'kepala-bu')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-sky-50 text-sky-700 border border-sky-100 rounded-full text-[9px] font-black uppercase tracking-wider">
                                        <i class="fa-solid fa-user-tie text-[8px]"></i> Kepala BU
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center space-x-1 whitespace-nowrap">
                                <a href="/kelola-akun/edit/{{ $user->id }}" 
                                   class="inline-flex items-center gap-0.5 text-[11px] font-black text-[#0c1a3c] bg-gray-50 hover:bg-[#0c1a3c] hover:text-white border border-gray-200 px-2.5 py-1 rounded-lg transition-all shadow-sm">
                                    <i class="fa-solid fa-user-pen text-[9px] opacity-70"></i> Edit
                                </a>
                                
                                <form action="/kelola-akun/hapus/{{ $user->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin mutlak ingin memusnahkan akun ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-0.5 text-[11px] font-black text-red-600 bg-red-50 hover:bg-red-600 hover:text-white border border-red-200/60 px-2.5 py-1 rounded-lg transition-all shadow-sm cursor-pointer">
                                        <i class="fa-solid fa-user-xmark text-[9px] opacity-70"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic font-light">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-user-ninja text-2xl text-gray-200"></i>
                                    <span>Belum ada akun operasional staf yang terdaftar dalam kluster pencarian ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection