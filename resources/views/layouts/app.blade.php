<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - ARSIPIN.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Animasi transisi lebar sidebar yang sangat smooth dan elegan */
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .fade-transition { transition: opacity 0.2s ease-in-out; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-[#0c1a3c]">

    <div class="flex min-h-screen relative overflow-hidden">
        
        <aside id="main-sidebar" style="width: 256px;" class="sidebar-transition fixed md:sticky top-0 bottom-0 left-0 bg-[#0c1a3c] text-white flex flex-col border-r border-white/5 z-50 -translate-x-full md:translate-x-0 overflow-hidden">
            
            <div class="p-6 flex items-center justify-between border-b border-white/5 min-h-[64px] overflow-hidden flex-shrink-0">
                <div class="flex items-center gap-3 flex-shrink-0">
                    <svg class="w-7 h-7 flex-shrink-0" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="100" height="100" rx="24" fill="#112554"/>
                        <path d="M50 22L72 72H60L50 48L40 72H28L50 22Z" fill="#38bdf8"/>
                        <path d="M43 58H57L50 42L43 58Z" fill="#ffffff"/>
                    </svg>
                    <h1 class="text-lg font-black tracking-tighter uppercase text-white sidebar-text fade-transition whitespace-nowrap">
                        ARSIPIN<span class="text-sky-400">.</span>
                    </h1>
                </div>
                <button id="toggle-collapse-btn" class="hidden md:flex text-gray-400 hover:text-white transition-all duration-200 focus:outline-none flex-shrink-0 cursor-pointer">
                    <i class="fa-solid fa-angles-left text-xs transition-transform duration-300" id="collapse-icon"></i>
                </button>
            </div>
            
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto overflow-x-hidden">
                
                @if(Auth::user()->role == 'admin')
                    <a href="/dashboard" class="flex items-center gap-4 p-3 {{ Request::is('dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Dashboard Admin">
                        <i class="fa-solid fa-chart-pie text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Dashboard Admin</span>
                    </a>
                @elseif(Auth::user()->role == 'pegawai-retail')
                    <a href="/dashboard/retail" class="flex items-center gap-4 p-3 {{ Request::is('dashboard/retail*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Dashboard Retail">
                        <i class="fa-solid fa-chart-pie text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Dashboard Retail</span>
                    </a>
                @elseif(Auth::user()->role == 'pegawai-komersial')
                    <a href="/dashboard/commercial" class="flex items-center gap-4 p-3 {{ Request::is('dashboard/commercial*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Dashboard Komersial">
                        <i class="fa-solid fa-chart-pie text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Dashboard Komersial</span>
                    </a>
                @elseif(Auth::user()->role == 'kepala-bu')
                    <a href="{{ route('kepala-bu.dashboard') }}" class="flex items-center gap-4 p-3 {{ Request::is('dashboard/kepala-bu*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Dashboard Kontrol BU">
                        <i class="fa-solid fa-chart-pie text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Dashboard Kontrol BU</span>
                    </a>
                @endif

                @if(Auth::user()->role == 'admin')
                    <div class="pt-4 pb-1 px-3 sidebar-text fade-transition">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest whitespace-nowrap">Kontrol Admin</p>
                    </div>
                    <a href="/kelola-akun" class="flex items-center gap-4 p-3 {{ Request::is('kelola-akun*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Kelola Akun">
                        <i class="fa-solid fa-users-gear text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Kelola Akun Pegawai</span>
                    </a>
                    <a href="/retail" class="flex items-center gap-4 p-3 {{ Request::is('retail*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Arsip Retail">
                        <i class="fa-solid fa-shop text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Arsip Retail (All)</span>
                    </a>
                    <a href="/comercial" class="flex items-center gap-4 p-3 {{ Request::is('comercial*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Arsip Komersial">
                        <i class="fa-solid fa-wallet text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Arsip Komersial (All)</span>
                    </a>
                    <a href="{{ route('admin.timeline') }}" class="flex items-center gap-4 p-3 {{ Request::is('dashboard/admin/timeline*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Audit Trail">
                        <i class="fa-solid fa-clock-rotate-left text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Audit Trail Sistem</span>
                    </a>
                
                @elseif(Auth::user()->role == 'pegawai-retail')
                    <div class="pt-4 pb-1 px-3 sidebar-text fade-transition">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest whitespace-nowrap">Menu Kerja Retail</p>
                    </div>
                    <a href="/retail" class="flex items-center gap-4 p-3 {{ Request::is('retail*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Kelola Dokumen Retail">
                        <i class="fa-solid fa-shop text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Kelola Dokumen Retail</span>
                    </a>
                    <a href="{{ route('pegawai.timeline') }}" class="flex items-center gap-4 p-3 {{ Request::is('timeline/pegawai*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Timeline Kerja Saya">
                        <i class="fa-solid fa-timeline text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Timeline Kerja Saya</span>
                    </a>

                @elseif(Auth::user()->role == 'pegawai-komersial')
                    <div class="pt-4 pb-1 px-3 sidebar-text fade-transition">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest whitespace-nowrap">Menu Kerja Komersial</p>
                    </div>
                    <a href="/comercial" class="flex items-center gap-4 p-3 {{ Request::is('comercial*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Kelola Dokumen Komersial">
                        <i class="fa-solid fa-wallet text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Kelola Dokumen Komersial</span>
                    </a>
                    <a href="{{ route('pegawai.timeline') }}" class="flex items-center gap-4 p-3 {{ Request::is('timeline/pegawai*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Timeline Kerja Saya">
                        <i class="fa-solid fa-timeline text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Timeline Kerja Saya</span>
                    </a>

                @elseif(Auth::user()->role == 'kepala-bu')
                    <div class="pt-4 pb-1 px-3 sidebar-text fade-transition">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest whitespace-nowrap">Otoritas Wilayah</p>
                    </div>
                    @if(in_array(strtolower(Auth::user()->bisnis_unit), ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']))
                        <a href="/retail" class="flex items-center gap-4 p-3 {{ Request::is('retail*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Monitoring Arsip Retail">
                            <i class="fa-solid fa-shop text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                            <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Monitoring Arsip Retail</span>
                        </a>
                    @else
                        <a href="/comercial" class="flex items-center gap-4 p-3 {{ Request::is('comercial*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Monitoring Arsip Komersial">
                            <i class="fa-solid fa-wallet text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                            <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Monitoring Arsip Komersial</span>
                        </a>
                    @endif

                    <a href="{{ route('kepala-bu.staf') }}" class="flex items-center gap-4 p-3 {{ Request::is('kepala-bu/staf*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Kelola Staf Unit">
                        <i class="fa-solid fa-users-viewfinder text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Kelola Staf Unit</span>
                    </a>
                    <a href="{{ route('kepala-bu.approval') }}" class="flex items-center gap-4 p-3 {{ Request::is('kepala-bu/approval*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Persetujuan Hapus">
                        <i class="fa-solid fa-clipboard-check text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Persetujuan Hapus</span>
                    </a>
                    <a href="{{ route('kepala-bu.timeline') }}" class="flex items-center gap-4 p-3 {{ Request::is('dashboard/kepala-bu/timeline*') ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-xl transition group flex-shrink-0" title="Timeline Aktivitas Unit">
                        <i class="fa-solid fa-clock-rotate-left text-sm flex-shrink-0 w-5 text-center group-hover:text-sky-400"></i>
                        <span class="sidebar-text text-xs tracking-wide whitespace-nowrap fade-transition">Timeline Aktivitas Unit</span>
                    </a>
                @endif
            </nav>

            <div class="p-3 border-t border-white/5 bg-black/10 overflow-hidden flex-shrink-0">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3 overflow-hidden flex-shrink-0">
                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-white font-bold text-xs border border-white/10 flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="truncate sidebar-text fade-transition">
                            <p class="text-[11px] font-bold text-white truncate whitespace-nowrap">{{ Auth::user()->nama_lengkap }}</p>
                            <p class="text-[9px] font-medium text-gray-400 uppercase tracking-wider truncate whitespace-nowrap">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 text-[10px] font-bold rounded-xl transition-all uppercase tracking-wider text-center flex items-center justify-center gap-2 flex-shrink-0">
                            <i class="fa-solid fa-power-off text-[11px] flex-shrink-0"></i> 
                            <span class="sidebar-text whitespace-nowrap fade-transition">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 md:px-8 flex-shrink-0 sticky top-0 z-40 shadow-sm backdrop-blur-md bg-white/90">
                <div class="flex items-center gap-4">
                    <button id="mobile-sidebar-toggle" class="text-[#0c1a3c] focus:outline-none md:hidden">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h2 class="font-extrabold text-sm md:text-base text-[#0c1a3c] tracking-tight">@yield('title')</h2>
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-block text-[10px] font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 uppercase tracking-wider">{{ Auth::user()->role }}</span>
                    <div class="w-8 h-8 bg-[#0c1a3c]/5 rounded-full flex items-center justify-center border border-[#0c1a3c]/10">
                        <span class="text-xs font-black text-[#0c1a3c]">{{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}</span>
                    </div>
                </div>
            </header>

            <div class="p-6 md:p-8 flex-1">
                @yield('content')
            </div>
        </div>

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('main-sidebar');
            const toggleCollapseBtn = document.getElementById('toggle-collapse-btn'); 
            const mobileToggleBtn = document.getElementById('mobile-sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            const collapseIcon = document.getElementById('collapse-icon');

            let isCollapsed = false;

            if(toggleCollapseBtn) {
                toggleCollapseBtn.addEventListener('click', function() {
                    const sidebarTexts = document.querySelectorAll('.sidebar-text');
                    isCollapsed = !isCollapsed;
                    
                    if(isCollapsed) {
                        // 1. Kecilkan lebar boks utama sidebar inline
                        if(sidebar) sidebar.style.width = '80px';
                        if(collapseIcon) collapseIcon.style.transform = 'rotate(180deg)';
                        
                        sidebarTexts.forEach(el => {
                            if(el) {
                                el.style.opacity = '0';
                                setTimeout(() => {
                                    if(isCollapsed) el.classList.add('hidden');
                                }, 150);
                            }
                        });
                    } else {
                        // 2. Lebarkan kembali ke default 256px
                        if(sidebar) sidebar.style.width = '256px';
                        if(collapseIcon) collapseIcon.style.transform = 'rotate(0deg)';
                        
                        sidebarTexts.forEach(el => {
                            if(el) {
                                el.classList.remove('hidden');
                                setTimeout(() => {
                                    if(!isCollapsed) el.style.opacity = '1';
                                }, 5);
                            }
                        });
                    }
                });
            }

            function toggleMobileSidebar() {
                if(sidebar) sidebar.classList.toggle('-translate-x-full');
                if(overlay) overlay.classList.toggle('hidden');
            }

            if(mobileToggleBtn) mobileToggleBtn.addEventListener('click', toggleMobileSidebar);
            if(overlay) overlay.addEventListener('click', toggleMobileSidebar);
        });
    </script>
</body>
</html>