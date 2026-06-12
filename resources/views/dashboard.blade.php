@extends('layouts.app')

@section('title', 'Dashboard Utama Admin')

@section('content')
<div class="space-y-8 select-none">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#0c1a3c] tracking-tight">Pusat Otoritas Dokumen</h2>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-nodes text-sky-500"></i> Matriks dan Log Penutupan Buku Finansial
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs font-mono font-bold text-gray-400 bg-white px-4 py-2 rounded-xl border border-gray-100 shadow-sm self-start sm:self-auto">
            <i class="fa-regular fa-clock text-[#0c1a3c]"></i>
            <span id="live-clock">{{ now()->format('d M Y H:i:s') }} WIB</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Arsip Berkas</span>
                <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center text-[#0c1a3c]">
                    <i class="fa-solid fa-file-invoice text-xs"></i>
                </div>
            </div>
            <span class="text-4xl font-black text-[#0c1a3c] mt-4 block font-mono tracking-tight">{{ $totalDokumen }}</span>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengguna Aktif</span>
                <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center text-[#0c1a3c]">
                    <i class="fa-solid fa-users-gear text-xs"></i>
                </div>
            </div>
            <span class="text-4xl font-black text-[#0c1a3c] mt-4 block font-mono tracking-tight">{{ $totalPengguna }}</span>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Antrean Penghapusan</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500">
                    <i class="fa-solid fa-hourglass-half text-xs"></i>
                </div>
            </div>
            <span class="text-4xl font-black text-amber-500 mt-4 block font-mono tracking-tight">{{ $menungguPersetujuan }}</span>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Entitas Perusahaan</span>
                <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center text-[#0c1a3c]">
                    <i class="fa-solid fa-building-columns text-xs"></i>
                </div>
            </div>
            <span class="text-4xl font-black text-[#0c1a3c] mt-4 block font-mono tracking-tight">{{ $totalPerusahaan }}</span>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        
        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/10 flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-[#0c1a3c] tracking-tight">Jurnal Riwayat Berkas Terbaru</h3>
                <p class="text-xs text-gray-400 mt-0.5 font-light">Arsip data yang baru saja ter-upload masuk ke server</p>
            </div>
            <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200/50 flex-shrink-0">
                <a href="?filter=semua" class="px-4 py-1.5 text-[11px] font-bold rounded-lg transition-all {{ $filter === 'semua' ? 'bg-white text-[#0c1a3c] shadow-sm' : 'text-gray-400' }}">Semua</a>
                <a href="?filter=retail" class="px-4 py-1.5 text-[11px] font-bold rounded-lg transition-all {{ $filter === 'retail' ? 'bg-white text-[#0c1a3c] shadow-sm' : 'text-gray-400' }}">Retail</a>
                <a href="?filter=komersial" class="px-4 py-1.5 text-[11px] font-bold rounded-lg transition-all {{ $filter === 'komersial' ? 'bg-white text-[#0c1a3c] shadow-sm' : 'text-gray-400' }}">Komersial</a>
            </div>
        </div>

        <div class="overflow-x-auto overflow-y-auto max-h-[340px] scrollbar-thin">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white z-20 shadow-[0_1px_0_0_rgba(243,244,246,1)]">
                    <tr class="text-[10px] uppercase text-gray-400 font-black border-b border-gray-50 bg-gray-50/50 tracking-wider">
                        <th class="px-6 py-4">Informasi Berkas</th>
                        <th class="px-6 py-4">Unit Operasional</th>
                        <th class="px-6 py-4">Entitas</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Klasifikasi</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-600">
                    @forelse($dokumenTerbaru as $dok)
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                                        <i class="fa-solid fa-file-pdf text-sm"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#0c1a3c] block text-xs truncate max-w-[200px]" title="{{ $dok->nama_dokumen }}">{{ $dok->nama_dokumen }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono font-bold mt-0.5 block">{{ $dok->no_dokumen }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 font-mono text-[10px] font-bold uppercase rounded">{{ str_replace('-', ' ', $dok->bisnis_unit) }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-xs text-gray-700">{{ $dok->perusahaan }}</td>
                            <td class="px-6 py-4">
                                @if($dok->status === 'active')
                                    <span class="px-2.5 py-0.5 bg-sky-50 text-sky-700 border border-sky-100 rounded-full text-[9px] font-black">ACTIVE</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[9px] font-black">PENDING</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100/80 px-2.5 py-0.5 rounded border border-slate-200/30">{{ $dok->tipe_keuangan }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(in_array($dok->bisnis_unit, ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']))
                                    <a href="{{ route('retail.dokumen', [$dok->bisnis_unit, $dok->perusahaan]) }}" class="text-[11px] font-bold text-[#0c1a3c] bg-gray-50 border border-gray-200 px-3 py-1 rounded-xl hover:bg-[#0c1a3c] hover:text-white transition-all shadow-sm">Buka</a>
                                @else
                                    <a href="{{ route('comercial.dokumen', [$dok->bisnis_unit, $dok->perusahaan]) }}" class="text-[11px] font-bold text-[#0c1a3c] bg-gray-50 border border-gray-200 px-3 py-1 rounded-xl hover:bg-[#0c1a3c] hover:text-white transition-all shadow-sm">Buka</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 italic font-light">Belum ada berkas dokumen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-[#0c1a3c]">Volume Trafik Berkas Finansial</h3>
                    <p class="text-[11px] text-gray-400 font-light">Distribusi jumlah arsip penutupan buku berdasarkan tahun</p>
                </div>
                <select class="text-[10px] font-bold bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 outline-none text-gray-500 cursor-pointer focus:border-[#0c1a3c]">
                    <option value="tahun">Skala: Tahunan</option>
                    <option value="bulan" disabled>Skala: Bulanan (🔒 Backend Plan)</option>
                    <option value="minggu" disabled>Skala: Mingguan (🔒 Backend Plan)</option>
                </select>
            </div>
            <div class="w-full h-64 relative">
                <canvas id="financialBarChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="border-b border-gray-50 pb-4 mb-4">
                <h3 class="text-sm font-bold text-[#0c1a3c]">Integritas Penyimpanan AWS S3</h3>
                <p class="text-[11px] text-gray-400 font-light">Status sisa kapasitas ruang penyimpanan cloud storage</p>
            </div>
            <div class="w-full h-48 relative flex items-center justify-center">
                <canvas id="awsStorageDoughnutChart"></canvas>
                <div class="absolute flex flex-col items-center justify-center pointer-events-none mt-2">
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Terpakai</span>
                    <span class="text-xl font-black text-[#0c1a3c] font-mono">9.0%</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center border-t border-gray-50 pt-4 mt-2">
                <div class="border-r border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Used Space</p>
                    <p class="text-xs font-black text-[#0c1a3c] font-mono mt-0.5">450.50 MB</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Remaining Space</p>
                    <p class="text-xs font-black text-sky-500 font-mono mt-0.5">4.55 GB</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. DIGITAL LIVE TIME RECORDER
        const clockElement = document.getElementById('live-clock');
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        const dateStr = new Date().toLocaleDateString('id-ID', options);
        setInterval(() => {
            const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            clockElement.innerText = dateStr + " | " + timeStr + " WIB";
        }, 1000);

        // 2. RENDERING BAR CHART (MOCK TAHUNAN)
        const ctxBar = document.getElementById('financialBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025', '2026 (YTD)'],
                datasets: [{
                    label: 'Jumlah Dokumen Finansial Terindeks',
                    data: [120, 340, 512, 189],
                    backgroundColor: '#0c1a3c',
                    hoverBackgroundColor: '#0ea5e9',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 } } },
                    y: { grid: { color: '#f3f4f6' }, ticks: { font: { family: 'Poppins', size: 10 } } }
                }
            }
        });

        // 3. RENDERING DOUGHNUT CHART (AWS S3 MOCK)
        const ctxDoughnut = document.getElementById('awsStorageDoughnutChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Terpakai (MB)', 'Sisa Ruang Kosong (GB)'],
                datasets: [{
                    data: [450.50, 4550.00],
                    backgroundColor: ['#0c1a3c', '#e2e8f0'],
                    hoverBackgroundColor: ['#0ea5e9', '#cbd5e1'],
                    borderWidth: 0,
                    weight: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: { legend: { display: false } }
            }
        });

    });
</script>
@endsection