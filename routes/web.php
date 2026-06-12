<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;

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
    | 2. KELOMPOK GLOBAL DOKUMEN CONTROL (BISA DIAKSES RETAIL & KOMERSIAL)
    |----------------------------------------------------------------------
    */
    Route::post('/dokumen/store', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::get('/dokumen/{id}/preview', [DokumenController::class, 'preview'])->name('dokumen.preview');
    Route::delete('/dokumen/{id}/delete', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('/dokumen/edit/{id}', [DokumenController::class, 'editDokumen'])->name('dokumen.edit');
    Route::put('/dokumen/update/{id}', [DokumenController::class, 'updateDokumen'])->name('dokumen.update');


    /*
    |----------------------------------------------------------------------
    | 3. KELOMPOK KHUSUS ROLE: ADMIN (KELOLA AKUN PEGAWAI)
    |----------------------------------------------------------------------
    |
    */
    Route::middleware(['role:admin', 'auth'])->group(function () {
        
        Route::get('/dashboard', function (Illuminate\Http\Request $request) {
            // 1. Hitung statistik asli database
            $totalDokumen = \App\Models\Dokumen::count();
            $totalPengguna = \App\Models\User::count();
            $menungguPersetujuan = \App\Models\Dokumen::where('status', 'pending_hapus')->count();
            $totalPerusahaan = \App\Models\Perusahaan::count(); 

            // 2. Logika filter dokumen terbaru murni PHP Laravel
            $filter = $request->query('filter', 'semua');
            $queryDokumen = \App\Models\Dokumen::latest();

            if ($filter === 'retail') {
                $queryDokumen->whereIn('bisnis_unit', ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']);
            } elseif ($filter === 'komersial') {
                $queryDokumen->whereIn('bisnis_unit', ['transportasi-laut', 'shipyard', 'gas-industri']);
            }

            $dokumenTerbaru = $queryDokumen->limit(5)->get();

            return view('dashboard', compact('totalDokumen', 'totalPengguna', 'menungguPersetujuan', 'totalPerusahaan', 'dokumenTerbaru', 'filter'));
        })->name('dashboard');

        // Seluruh Fitur CRUD Kelola Akun Pegawai (Gunakan full path agar tidak error class not found)
        Route::get('/kelola-akun', [\App\Http\Controllers\UserController::class, 'index']);
        Route::get('/kelola-akun/tambah', [\App\Http\Controllers\UserController::class, 'create']);
        Route::post('/kelola-akun/simpan', [\App\Http\Controllers\UserController::class, 'store'])->name('user.store');
        Route::get('/kelola-akun/edit/{id}', [\App\Http\Controllers\UserController::class, 'edit']);
        Route::put('/kelola-akun/update/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');
        Route::delete('/kelola-akun/hapus/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
        
        // 🌟 POTONGAN KODE YANG BENAR DI WEB.PHP HARUSNYA HANYA SEPERTI INI YA ANGELA:
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

        // Menu Utama Fitur Retail (Index Bisnis Unit)
        Route::get('/retail', function () {
            return view('retail.index');
        })->name('retail');

        // Jembatan Menampilkan Daftar Perusahaan Berdasarkan Bisnis Unit Retail
        Route::get('/retail/{bisnis_unit}', function ($bisnis_unit) {
            $data_pt = [
                'spbu'        => ['PT SCK', 'PT MMS', 'PT IS', 'PT LEP'],
                'lpg-pso'     => ['PT SJN', 'PT PJNP', 'PT PJS', 'PT LCPS', 'PT BSN'],
                'lpg-npso'    => ['PT LBS'],
                'sppbe'       => ['PT PKSP'],
                'bbm-retail'  => ['PT BKI', 'PT PJS', 'PT ADHEL', 'PT TRP'],
                'inmar'       => ['PT CNGM', 'PT PIMS', 'PT TEMARINDO', 'PT SCP']
            ];

            $list_pt = $data_pt[$bisnis_unit] ?? [];
            $nama_bu = strtoupper(str_replace('-', ' ', $bisnis_unit));

            return view('retail.perusahaan', compact('nama_bu', 'list_pt', 'bisnis_unit'));
        })->name('retail.perusahaan');

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

        // Menu Utama Fitur Komersial (Index Pilihan Bisnis Unit)
        Route::get('/comercial', function () {
            return view('comercial.index');
        })->name('comercial');

        // Jembatan Menampilkan Daftar Perusahaan Berdasarkan Bisnis Unit Komersial
        Route::get('/comercial/{bisnis_unit}', function ($bisnis_unit) {
            $nama_bu = '';
            $list_pt = [];
            $bu_lower = strtolower($bisnis_unit);

            if ($bu_lower == 'transportasi-laut') {
                $nama_bu = 'Transportasi Laut';
                $list_pt = ['PT CPT', 'PT MHM'];
            } elseif ($bu_lower == 'shipyard') {
                $nama_bu = 'Shipyard';
                $list_pt = ['PT SBS'];
            } elseif ($bu_lower == 'gas-industri') {
                $nama_bu = 'Gas Industri';
                $list_pt = ['PT GVI', 'PT MTG'];
            }

            return view('comercial.perusahaan', compact('bisnis_unit', 'nama_bu', 'list_pt'));
        })->name('comercial.perusahaan');

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
        
        // 🌟 TIMELINE AKTIVITAS PRIBADI PEGAWAI (RETAIL & KOMERSIAL)
        Route::get('/timeline/pegawai', [UserController::class, 'timelinePegawai'])->name('pegawai.timeline');
    });

    // 🌟 B. RUTE KHUSUS ATASAN: Hanya boleh diakses Admin dan Kepala BU saja!
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

        // 🌟 TIMELINE MONITORING ANGGOTA UNIT UNTUK KEPALA BU
        Route::get('/dashboard/kepala-bu/timeline', [UserController::class, 'timelineKepalaBu'])->name('kepala-bu.timeline');
    });

});