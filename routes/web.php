<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\BisnisUnitController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\KategoriController;

/*
|--------------------------------------------------------------------------
| 1. RUTE FITUR AUTENTIKASI (LOGIN & LOGOUT)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| RUTE YANG DIJAGA KETAT (WAJIB LOGIN TERLEBIH DAHULU)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | 2. KELOMPOK GLOBAL DOKUMEN CONTROL, BISNIS UNIT & PERUSAHAAN (DINAMIS DB)
    |----------------------------------------------------------------------
    */
    // Action Dokumen
    Route::post('/dokumen/store', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::get('/dokumen/{id}/preview', [DokumenController::class, 'preview'])->name('dokumen.preview');
    Route::delete('/dokumen/{id}/delete', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('/dokumen/edit/{id}', [DokumenController::class, 'editDokumen'])->name('dokumen.edit');
    Route::put('/dokumen/update/{id}', [DokumenController::class, 'updateDokumen'])->name('dokumen.update');

    // 🌟 Rute Utama Index Bisnis Unit Dinamis (Retail & Commercial)
    Route::get('/retail', [BisnisUnitController::class, 'retailIndex'])->name('retail.index');
    Route::get('/commercial', [BisnisUnitController::class, 'commercialIndex'])->name('commercial.index');
    Route::get('/comercial', [BisnisUnitController::class, 'commercialIndex']); // Alias typo handal Baim

    // 🌟 Action CRUD Bisnis Unit
    Route::post('/bisnis-unit/store', [BisnisUnitController::class, 'store'])->name('bisnis-unit.store');
    Route::put('/bisnis-unit/{id}/update', [PerusahaanController::class, 'updateBu'])->name('bisnis-unit.update');
    Route::delete('/bisnis-unit/{id}/destroy', [PerusahaanController::class, 'destroyBu'])->name('bisnis-unit.destroy');

    // 🏬 RUTE PERUSAHAAN DINAMIS (Ditarik langsung dari DB MySQL)
    Route::get('/retail/{bisnis_unit}', [PerusahaanController::class, 'retailPerusahaan'])->name('retail.perusahaan');
    Route::get('/comercial/{bisnis_unit}', [PerusahaanController::class, 'commercialPerusahaan'])->name('comercial.perusahaan');
    
    // Action CRUD Perusahaan
    Route::post('/perusahaan/store', [PerusahaanController::class, 'store'])->name('perusahaan.store');
    Route::delete('/perusahaan/{id}/destroy', [PerusahaanController::class, 'destroy'])->name('perusahaan.destroy');
    Route::put('/perusahaan/{id}/update', [PerusahaanController::class, 'update'])->name('perusahaan.update');

    // Rute Tambah Jenis Dokumen Baru (Modal Pop-Up)
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');


    /*
    |----------------------------------------------------------------------
    | 3. KELOMPOK KHUSUS ROLE: ADMIN 
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin', 'auth'])->group(function () {
        
        Route::get('/dashboard', function (Illuminate\Http\Request $request) {
            // 1. Hitung statistik asli database untuk 4 kotak atas
            $totalDokumen = \App\Models\Dokumen::count();
            $totalPengguna = \App\Models\User::count();
            $menungguPersetujuan = \App\Models\Dokumen::where('status', 'pending_hapus')->count();
            $totalPerusahaan = \App\Models\Perusahaan::count(); 

            // 2. Logika filter log jurnal riwayat berkas terbaru
            $filter = $request->query('filter', 'semua');
            $queryDokumen = \App\Models\Dokumen::latest();

            if ($filter === 'retail') {
                $queryDokumen->whereIn('bisnis_unit', ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']);
            } elseif ($filter === 'komersial') {
                $queryDokumen->whereNotIn('bisnis_unit', ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']);
            }

            $dokumenTerbaru = $queryDokumen->limit(5)->get();

            // 🔥 3. INTEGRITAS PENYIMPANAN AWS S3 (Kuota 5GB Free Tier)
            $totalTerpakaiMB = \App\Models\Dokumen::sum('file_size') ?? 0; 
            $kuotaMaksimalMB = 5120; // 5 GB = 5120 MB
            
            $sisaPenyimpananMB = $kuotaMaksimalMB - $totalTerpakaiMB;
            if ($sisaPenyimpananMB < 0) $sisaPenyimpananMB = 0;
            
            $persentaseTerpakai = ($totalTerpakaiMB / $kuotaMaksimalMB) * 100;
            $sisaPenyimpananGB = $sisaPenyimpananMB / 1024;

            // 🔥 4. VOLUMETRIK TRAFIK FILTER GRAFIK (Tahun, Bulan, Minggu)
            $scale = $request->query('scale', 'tahun');
            $chartLabels = [];
            $chartData = [];

            if ($scale === 'minggu') {
                for ($i = 6; $i >= 0; $i--) {
                    $date = \Carbon\Carbon::now()->subDays($i);
                    $chartLabels[] = $date->translatedFormat('D'); 
                    $chartData[] = \App\Models\Dokumen::whereDate('created_at', $date->toDateString())->count();
                }
            } elseif ($scale === 'bulan') {
                for ($i = 5; $i >= 0; $i--) {
                    $month = \Carbon\Carbon::now()->subMonths($i);
                    $chartLabels[] = $month->translatedFormat('F'); 
                    $chartData[] = \App\Models\Dokumen::whereMonth('created_at', $month->month)
                                          ->whereYear('created_at', $month->year)
                                          ->count();
                }
            } else {
                $currentYear = \Carbon\Carbon::now()->year;
                for ($i = 3; $i >= 0; $i--) {
                    $year = $currentYear - $i;
                    $chartLabels[] = (string)$year;
                    $chartData[] = \App\Models\Dokumen::whereYear('created_at', $year)->count();
                }
            }

            // 🔥 MASUKKAN SEMUA VARIABEL KE COMPACT AGAR BLADE TIDAK UNDEFINED!
            return view('dashboard', compact(
                'totalDokumen', 'totalPengguna', 'menungguPersetujuan', 'totalPerusahaan', 
                'dokumenTerbaru', 'filter', 'scale', 'totalTerpakaiMB', 'persentaseTerpakai', 
                'sisaPenyimpananGB', 'chartLabels', 'chartData'
            ));
        })->name('dashboard');

        // Seluruh Fitur CRUD Kelola Akun Pegawai
        Route::get('/kelola-akun', [\App\Http\Controllers\UserController::class, 'index']);
        Route::get('/kelola-akun/tambah', [\App\Http\Controllers\UserController::class, 'create']);
        Route::post('/kelola-akun/simpan', [\App\Http\Controllers\UserController::class, 'store'])->name('user.store');
        Route::get('/kelola-akun/edit/{id}', [\App\Http\Controllers\UserController::class, 'edit']);
        Route::put('/kelola-akun/update/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');
        Route::delete('/kelola-akun/hapus/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
        
        // Timeline & Audit Trail Admin
        Route::get('/dashboard/admin/timeline', [\App\Http\Controllers\UserController::class, 'timelineAdmin'])->name('admin.timeline');



    });


    /*
    |----------------------------------------------------------------------
    | 4. KELOMPOK KHUSUS ROLE: PEGAWAI RETAIL (ADMIN, RETAIL & KEPALA BU)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin,pegawai-retail,kepala-bu,kepala_bu'])->group(function () {
        
        // Dashboard Utama Pegawai Retail
        Route::get('/dashboard/retail', [UserController::class, 'dashboardRetail'])->name('retail.dashboard');

        // Menampilkan tabel dokumen dinamis via Controller
        Route::get('/retail/{bisnis_unit}/{perusahaan}', [DokumenController::class, 'index'])->name('retail.dokumen');

        // Menampilkan halaman form upload dokumen retail
        Route::get('/retail/{bisnis_unit}/{perusahaan}/upload', [DokumenController::class, 'create'])->name('retail.upload');
    });

    /*
    |----------------------------------------------------------------------
    | 5. KELOMPOK KHUSUS ROLE: PEGAWAI KOMERSIAL (ADMIN, KOMERSIAL & KEPALA BU)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin,pegawai-komersial,kepala-bu,kepala_bu'])->group(function () {
        
        // Jalur Ganda Dashboard Komersial
        Route::get('/dashboard/comercial', [UserController::class, 'dashboardComercial'])->name('commercial.dashboard');
        Route::get('/dashboard/commercial', [UserController::class, 'dashboardComercial']);

        // Menampilkan tabel dokumen dinamis Komersial via Controller
        Route::get('/comercial/{bisnis_unit}/{perusahaan}', [DokumenController::class, 'index'])->name('comercial.dokumen');

        // Menampilkan halaman form upload dokumen Komersial
        Route::get('/comercial/{bisnis_unit}/{perusahaan}/upload', [DokumenController::class, 'create'])->name('comercial.upload');
    });

    /*
    |----------------------------------------------------------------------
    | 6. KELOMPOK KHUSUS ROLE: KEPALA BU & PEGAWAI (Workflow & Timeline System)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin,kepala-bu,kepala_bu,pegawai-retail,pegawai-komersial'])->group(function () {
        
        // Fitur Utama Pengajuan Hapus Dokumen Global
        Route::post('/retail/dokumen/ajukan-hapus/{id}', [UserController::class, 'ajukanHapusDokumen'])->name('dokumen.ajukan-hapus');
        
        // TIMELINE AKTIVITAS PRIBADI PEGAWAI (RETAIL & KOMERSIAL)
        Route::get('/timeline/pegawai', [UserController::class, 'timelinePegawai'])->name('pegawai.timeline');
    });

    // Rute halaman ruang tunggu persetujuan Kepala BU
    Route::get('/menunggu-persetujuan', function () {
        if (!auth()->check()) {
            return redirect('/login');
        }
        return view('pengguna.persetujuan');
    })->name('tunggu.persetujuan');

    // B. RUTE KHUSUS ATASAN: Hanya boleh diakses Admin dan Kepala BU saja!
    Route::middleware(['role:admin,kepala-bu,kepala_bu'])->group(function () {
        
        // Halaman Dashboard Kepala BU
        Route::get('/dashboard/kepala-bu', [UserController::class, 'dashboardKepalaBu'])->name('kepala-bu.dashboard');

        // Kelola Staf Unit
        Route::get('/kepala-bu/staf', [UserController::class, 'stafUnit'])->name('kepala-bu.staf');
        Route::post('/kepala-bu/staf/rekrut/{id}', [UserController::class, 'rekrutStaf'])->name('kepala-bu.staf.rekrut');
        Route::post('/kepala-bu/staf/keluarkan/{id}', [UserController::class, 'keluarkanStaf'])->name('kepala-bu.staf.keluarkan');

        // Halaman Kotak Masuk Approval Dokumen Pending Hapus
        Route::get('/kepala-bu/approval', [UserController::class, 'approvalIndex'])->name('kepala-bu.approval');

        // Eksekusi Aksi Keputusan Atasan
        Route::post('/kepala-bu/approval/setujui/{id}', [UserController::class, 'setujuiHapus'])->name('kepala-bu.approval.setujui');
        Route::post('/kepala-bu/approval/tolak/{id}', [UserController::class, 'tolakHapus'])->name('kepala-bu.approval.tolak');

        // TIMELINE MONITORING ANGGOTA UNIT UNTUK KEPALA BU
        Route::get('/dashboard/kepala-bu/timeline', [UserController::class, 'timelineKepalaBu'])->name('kepala-bu.timeline');
    });

});