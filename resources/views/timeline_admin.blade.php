@extends('layouts.app')

@section('title', 'Audit Trail Sistem Global')
@section('header', 'Audit Trail & Log Aktivitas Global')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Bagian Atas: Form Kontrol Filter Interaktif -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.timeline') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
            
            <!-- Filter 1: Rumpun Kelompok / Role -->
            <div class="w-full md:w-1/3 space-y-1.5">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Filter Rumpun Hak Akses</label>
                <select name="role" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-marica-navy outline-none text-gray-600 font-medium transition-all">
                    <option value="semua" {{ $filterRole == 'semua' ? 'selected' : '' }}>🌍 Semua Hak Akses (Global)</option>
                    <option value="retail" {{ $filterRole == 'retail' ? 'selected' : '' }}>🔵 Pegawai Divisi Retail</option>
                    <option value="komersial" {{ $filterRole == 'komersial' ? 'selected' : '' }}>🟢 Pegawai Divisi Komersial</option>
                    <option value="kepala_bu" {{ $filterRole == 'kepala_bu' ? 'selected' : '' }}>🏢 Kepala Bisnis Unit (Atasan)</option>
                    <option value="admin" {{ $filterRole == 'admin' ? 'selected' : '' }}>🛠️ Admin Utama</option>
                </select>
            </div>

            <!-- Filter 2: Jenis Tindakan / Aktivitas -->
            <div class="w-full md:w-1/3 space-y-1.5">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Filter Jenis Tindakan</label>
                <select name="aksi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-marica-navy outline-none text-gray-600 font-medium transition-all">
                    <option value="semua" {{ $filterAksi == 'semua' ? 'selected' : '' }}>🔍 Semua Tindakan Sistem</option>
                    <option value="Upload Dokumen" {{ $filterAksi == 'Upload Dokumen' ? 'selected' : '' }}>📄 Upload Berkas Baru</option>
                    <option value="Ajukan Hapus" {{ $filterAksi == 'Ajukan Hapus' ? 'selected' : '' }}>⚠️ Pengajuan Pemusnahan</option>
                    <option value="Setujui Pemusnahan" {{ $filterAksi == 'Setujui Pemusnahan' ? 'selected' : '' }}>✅ Persetujuan Pemusnahan</option>
                    <option value="Tolak Pemusnahan" {{ $filterAksi == 'Tolak Pemusnahan' ? 'selected' : '' }}>❌ Penolakan Pemusnahan</option>
                </select>
            </div>

            <!-- Tombol Submit Filter Sederhana -->
            <div class="w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-[#0c1a3c] text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm hover:bg-opacity-95 transition-all">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Kotak Utama Tabel Log Audit Trail -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4 text-center w-16">ID</th>
                        <th class="px-6 py-4 w-44">Waktu Kejadian</th>
                        <th class="px-6 py-4 w-48">Aktor Pelaksana</th>
                        <th class="px-6 py-4 w-40">Tindakan</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-600">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/40 transition-colors">
                            <!-- ID Log -->
                            <td class="px-6 py-4 text-center font-mono text-gray-400 font-semibold">#{{ $log->id }}</td>
                            
                            <!-- Waktu Real-Time -->
                            <td class="px-6 py-4 font-mono text-gray-400">
                                {{ $log->created_at->format('d/m/Y') }} <span class="text-marica-navy font-semibold ml-1">{{ $log->created_at->format('H:i') }} WIB</span>
                            </td>

                            <!-- Nama & Role User -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-marica-navy mb-0.5">{{ $log->nama_user }}</div>
                                @if($log->role_user === 'pegawai-retail')
                                    <span class="text-[9px] px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded font-bold uppercase border border-blue-100">Retail</span>
                                @elseif($log->role_user === 'pegawai-komersial')
                                    <span class="text-[9px] px-1.5 py-0.5 bg-green-50 text-green-600 rounded font-bold uppercase border border-green-100">Komersial</span>
                                @elseif(in_array($log->role_user, ['kepala-bu', 'kepala_bu']))
                                    <span class="text-[9px] px-1.5 py-0.5 bg-purple-50 text-purple-600 rounded font-bold uppercase border border-purple-100">Kepala BU</span>
                                @else
                                    <span class="text-[9px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded font-bold uppercase">Admin</span>
                                @endif
                                <span class="text-[9px] font-mono text-gray-400 ml-1">({{ $log->bisnis_unit ?? 'GLOBAL' }})</span>
                            </td>

                            <!-- Jenis Aksi (Badge) -->
                            <td class="px-6 py-4 font-medium">
                                @if($log->aksi === 'Upload Dokumen')
                                    <span class="text-blue-600 font-bold">📄 Upload</span>
                                @elseif($log->aksi === 'Ajukan Hapus')
                                    <span class="text-amber-600 font-bold">⚠️ Request Del</span>
                                @elseif(in_array($log->aksi, ['Setujui Pemusnahan', 'Setujui Hapus']))
                                    <span class="text-green-600 font-bold">✅ Approved</span>
                                @elseif($log->aksi === 'Tolak Pemusnahan')
                                    <span class="text-red-600 font-bold">❌ Rejected</span>
                                @else
                                    <span class="text-gray-500">{{ $log->aksi }}</span>
                                @endif
                            </td>

                            <!-- Deskripsi Detail -->
                            <td class="px-6 py-4 text-gray-500 font-light leading-relaxed">
                                {{ $log->deskripsi }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic font-light">
                                Tidak ada rekaman data log aktivitas yang cocok dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection