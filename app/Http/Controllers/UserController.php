<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Dokumen;
use App\Models\Perusahaan;
use App\Models\BisnisUnit;
use App\Models\LogAktivitas;

class UserController extends Controller
{
    // =========================================================================
    // 🛠️ 1. ADMIN: TAMPILKAN HALAMAN UTAMA (TABEL PENGGUNA)
    // =========================================================================
    public function index(Request $request)
    {
        $cari = $request->query('cari');

        if (!empty($cari)) {
            $users = User::where('nama_lengkap', 'like', '%' . $cari . '%')
                        ->orWhere('email', 'like', '%' . $cari . '%')
                        ->get();
        } else {
            $users = User::all();
        }

        return view('pengguna.index', compact('users'));
    }

    // =========================================================================
    // 🛠️ 2. ADMIN: TAMPILKAN FORM TAMBAH PENGGUNA
    // =========================================================================
    public function create()
    {
        $bisnisUnits = BisnisUnit::all(); 
        return view('pengguna.tambah', compact('bisnisUnits'));
    }

    // =========================================================================
    // 🛠️ 3. ADMIN: TAMPILKAN HALAMAN EDIT PENGGUNA
    // =========================================================================
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $bisnisUnits = BisnisUnit::all(); 

        return view('pengguna.edit', compact('user', 'bisnisUnits'));
    }

    // =========================================================================
    // 🛠️ 4. ADMIN: PROSES SIMPAN AKUN BARU (ANTI-BYPASS KANDIDAT) - VERSI STRIP BAKU
    // =========================================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:admin,pegawai-retail,pegawai-komersial,kepala_bu,kepala-bu,Kepala Business Unit',
            'no_hp'        => 'required|string|max:15',
            'bisnis_unit'  => 'required_unless:role,admin|nullable|string|max:255' 
        ], [
            'bisnis_unit.required_unless' => 'Kolom wilayah bisnis unit wajib diisi, kecuali jika mendaftarkan hak akses Admin Pusat.',
        ]);

        // 🔥 JALUR PENYELAMAT DATA: Paksa semua bentuk variasi inputan menjadi 'kepala-bu' (Pake Strip!)
        $roleData = $request->role;
        if ($roleData === 'kepala_bu' || $roleData === 'Kepala Business Unit') {
            $roleData = 'kepala-bu';
        }

        // Kunci status berdasarkan role yang sudah diseragamkan memakai strip
        $statusAwal = in_array($roleData, ['admin', 'kepala-bu']) ? 'approved' : 'pending';

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => bcrypt($request->password),
            'role'         => $roleData, // 😎 Tersimpan konsisten sebagai 'kepala-bu'
            'bisnis_unit'  => $request->bisnis_unit, 
            'no_hp'        => $request->no_hp,
            'status_aktif' => $statusAwal, 
        ]);

        self::catatLog('Kelola Akun', Auth::user()->nama_lengkap . ' membuat akun baru dengan nama: ' . $user->nama_lengkap);

        return redirect('/kelola-akun')->with('success', 'Akun baru berhasil ditambahkan.');
    }

    // =========================================================================
    // 🛠️ 5. ADMIN: PROSES UPDATE DATA AKUN - VERSI REVISI MATANG MARICA
    // =========================================================================
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $id,
            'email'        => 'required|string|email|max:255|unique:users,email,' . $id,
            'role'         => 'required|in:admin,pegawai-retail,pegawai-komersial,kepala_bu,kepala-bu,Kepala Business Unit',
            'no_hp'        => 'nullable|string|max:15',
            'bisnis_unit'  => 'required_unless:role,admin|nullable|string|max:255' 
        ], [
            'bisnis_unit.required_unless' => 'Kolom wilayah bisnis unit wajib diisi, kecuali jika mendaftarkan hak akses Admin Pusat.',
        ]);

        // 🔥 1. JALUR PENYELAMAT ROLE SAAT UPDATE: Paksa format strip 'kepala-bu'
        $roleData = $request->role;
        if ($roleData === 'kepala_bu' || $roleData === 'Kepala Business Unit') {
            $roleData = 'kepala-bu';
        }

        // 🔥 2. JALUR PENYELAMAT BISNIS UNIT SAAT UPDATE: Ubah spasi jadi strip & paksa Kapital
        $unitData = $request->bisnis_unit;
        if ($unitData) {
            $unitData = strtoupper(str_replace(' ', '-', $unitData)); // "LPG PSO" -> "LPG-PSO"
        }

        $user->nama_lengkap = $request->nama_lengkap;
        $user->username     = $request->username;
        $user->email        = $request->email;
        $user->role         = $roleData; 
        $user->bisnis_unit  = $unitData; // 😎 Menggunakan data bisnis unit yang sudah steril!
        $user->no_hp        = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        self::catatLog('Kelola Akun', Auth::user()->nama_lengkap . ' memperbarui data akun milik: ' . $user->nama_lengkap);

        return redirect('/kelola-akun')->with('success', 'Data akun berhasil diperbarui!');
    }

    // =========================================================================
    // 🛠️ 6. ADMIN: PROSES HAPUS AKUN PERMANEN
    // =========================================================================
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        self::catatLog('Kelola Akun', Auth::user()->nama_lengkap . ' menghapus akun pengguna bernama: ' . $user->nama_lengkap);
        $user->delete();

        return redirect('/kelola-akun')->with('success', 'Akun berhasil dihapus!');
    }

   // =========================================================================
    // 🏢 7. KEPALA BU: TAMPILKAN STAF UNIT & DAFTAR KANDIDAT ANTRIAN (ANTI-CASE SENSITIVE)
    // =========================================================================
    public function stafUnit()
    {
        $kepalaBu = Auth::user();
        
        // 🔥 KUNCI SAKTI: Ubah unit atasan ke huruf kecil & bersihkan spasi
        $unitAtasan = strtolower(trim($kepalaBu->bisnis_unit));

        // 1. Ambil staf aktif yang bisnis unitnya cocok secara case-insensitive
        $staf = User::whereRaw('LOWER(TRIM(bisnis_unit)) = ?', [$unitAtasan])
                    ->where('status_aktif', 'approved') 
                    ->whereIn('role', ['pegawai-retail', 'pegawai-komersial'])
                    ->get();

        // 2. 🔥 PERBAIKAN UTAMA: Tarik kandidat pending tanpa membatasi ketat role (asalkan unit bisnisnya sama persis)
        $kandidat = User::whereRaw('LOWER(TRIM(bisnis_unit)) = ?', [$unitAtasan])
                        ->where('status_aktif', 'pending') 
                        ->whereIn('role', ['pegawai-retail', 'pegawai-komersial'])
                        ->get();

        return view('kepala_bu.staf', compact('staf', 'kandidat'));
    }

    // =========================================================================
    // 🏢 8. KEPALA BU: PROSES TARIK/REKRUT PEGAWAI DARI DAFTAR KANDIDAT
    // =========================================================================
    public function rekrutStaf($id)
    {
        $pegawai = User::findOrFail($id);

        // 🔥 KUNCI UX: Ubah status dari pending menjadi approved agar resmi masuk tabel utama
        $pegawai->status_aktif = 'approved';
        $pegawai->save();

        return redirect()->back()->with('success', 'Berhasil menarik ' . $pegawai->nama_lengkap . ' menjadi anggota staf unit aktif.');
    }

    // =========================================================================
    // 🏢 9. KEPALA BU: KELUARKAN PEGAWAI DARI UNIT (KEMBALI KE STATUS PENDING)
    // =========================================================================
    public function keluarkanStaf($id)
    {
        $pegawai = User::findOrFail($id);
        
        // Kembalikan statusnya menjadi pending agar dia masuk kotak kandidat lagi
        $pegawai->status_aktif = 'pending';
        $pegawai->save();

        return redirect()->back()->with('success', 'Berhasil mengeluarkan ' . $pegawai->nama_lengkap . ' dari unit bisnis.');
    }

    // =========================================================================
    // 🏢 10. KEPALA BU: HALAMAN UTAMA PERSETUJUAN HAPUS DOKUMEN
    // =========================================================================
    public function approvalIndex()
    {
        $kepalaBu = Auth::user();

        $dokumen = Dokumen::where('bisnis_unit', $kepalaBu->bisnis_unit)
                        ->where('status', 'pending_hapus')
                        ->get();

        return view('kepala_bu.approval', compact('dokumen'));
    }

    // =========================================================================
    // 🏢 11. KEPALA BU: SETUJUI PEMUSNAHAN PERMANEN DOKUMEN
    // =========================================================================
    public function setujuiHapus($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $pathFile = $dokumen->s3_object_key; // Ambil alamat kunci file di S3

        // 🔥 Hapus file fisik PDF dari Amazon S3 Cloud Storage jika filenya ada
        if ($pathFile && \Illuminate\Support\Facades\Storage::disk('s3')->exists($pathFile)) {
            \Illuminate\Support\Facades\Storage::disk('s3')->delete($pathFile);
        }

        LogAktivitas::create([
            'user_id'     => Auth::id(),
            'nama_user'   => Auth::user()->nama_lengkap,
            'role_user'   => Auth::user()->role,
            'bisnis_unit' => $dokumen->bisnis_unit,
            'aksi'        => 'Setujui Pemusnahan',
            'deskripsi'   => Auth::user()->nama_lengkap . ' menyetujui pemusnahan permanen berkas dokumen: ' . $dokumen->nama_dokumen . ' (File dihapus dari S3)'
        ]);

        $dokumen->delete(); // Hapus metadata di MySQL

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui dan dihapus permanen dari Cloud S3!');
    }

    // =========================================================================
    // 🏢 12. KEPALA BU: TOLAK PEMUSNAHAN DOKUMEN
    // =========================================================================
    public function tolakHapus($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        $dokumen->status = 'active'; 
        $dokumen->save();

        LogAktivitas::create([
            'user_id'     => Auth::id(),
            'nama_user'   => Auth::user()->nama_lengkap,
            'role_user'   => Auth::user()->role,
            'bisnis_unit' => $dokumen->bisnis_unit,
            'aksi'        => 'Tolak Pemusnahan',
            'deskripsi'   => Auth::user()->nama_lengkap . ' menolak permohonan pemusnahan berkas dokumen: ' . $dokumen->nama_dokumen . ' (Status kembali Aktif)'
        ]);

        return redirect()->back()->with('success', 'Permohonan hapus ditolak. Dokumen kembali aman dan aktif.');
    }

    // =========================================================================
    // 🌾 13. PEGAWAI: AJUKAN PERMOHONAN HAPUS DOKUMEN KEPADA ATASAN
    // =========================================================================
    public function ajukanHapusDokumen($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $dokumen->status = 'pending_hapus';
        $dokumen->save();
        
        self::catatLog('Ajukan Hapus', Auth::user()->nama_lengkap . ' mengajukan permohonan pemusnahan berkas dokumen: ' . $dokumen->nama_dokumen);
        return redirect()->back()->with('success', 'Permohonan hapus dokumen berhasil dikirim ke Kepala BU!');
    }

   // =========================================================================
    // 🌾 14. PEGAWAI RETAIL: DASHBOARD UTAMA (DILENGKAPI SENSOR PENGADANG UX)
    // =========================================================================
    public function dashboardRetail()
    {
        // 🚨 SENSOR PENGADANG: Jika statusnya masih pending, tendang ke halaman ruang tunggu!
        if (Auth::user()->status_aktif === 'pending') {
            return redirect()->route('tunggu.persetujuan');
        }

        $bisnisUnit = Auth::user()->bisnis_unit;

        $totalPerusahaan = Perusahaan::whereHas('bisnisUnit', function($query) use ($bisnisUnit) {
            $query->where('nama_bisnis_unit', $bisnisUnit); 
        })->count();

        $totalDokumen = Dokumen::where('bisnis_unit', $bisnisUnit)->count();

        $dokumenPending = Dokumen::where('bisnis_unit', $bisnisUnit)
                                ->where('status', 'pending_hapus')
                                ->count();

        $dokumenTerbaru = Dokumen::where('bisnis_unit', $bisnisUnit)->latest()->limit(5)->get();

        return view('retail.dashboard', compact('totalPerusahaan', 'totalDokumen', 'dokumenPending', 'dokumenTerbaru'));
    }

    // =========================================================================
    // 🌾 15. PEGAWAI KOMERSIAL: DASHBOARD UTAMA (DILENGKAPI SENSOR PENGADANG UX)
    // =========================================================================
    public function dashboardComercial(Request $request)
    {
        // 🚨 SENSOR PENGADANG: Jika statusnya masih pending, tendang ke halaman ruang tunggu!
        if (Auth::user()->status_aktif === 'pending') {
            return redirect()->route('tunggu.persetujuan');
        }

        $bisnisUnit = Auth::user()->bisnis_unit;

        $totalPerusahaan = Perusahaan::whereHas('bisnisUnit', function($query) use ($bisnisUnit) {
            $query->where('nama_bisnis_unit', $bisnisUnit); 
        })->count();

        $totalDokumen = Dokumen::where('bisnis_unit', $bisnisUnit)->count();

        $dokumenPending = Dokumen::where('bisnis_unit', $bisnisUnit)
                                ->where('status', 'pending_hapus')
                                ->count();

        $type = $request->query('type', 'semua');
        $queryDokumen = Dokumen::where('bisnis_unit', $bisnisUnit)->latest();

        if ($type !== 'semua') {
            $queryDokumen->where('tipe_keuangan', $type);
        }

        $dokumenTerbaru = $queryDokumen->limit(5)->get();

        return view('comercial.dashboard', compact('totalPerusahaan', 'totalDokumen', 'dokumenPending', 'dokumenTerbaru', 'type'));
    }

    // =========================================================================
    // 🏢 16. KEPALA BU: STATISTICS DASHBOARD CHIEF
    // =========================================================================
    public function dashboardKepalaBu()
    {
        $kepalaBu = Auth::user();
        $bisnisUnit = $kepalaBu->bisnis_unit;

        $totalDokumen = Dokumen::where('bisnis_unit', $bisnisUnit)->count();

        $dokumenPending = Dokumen::where('bisnis_unit', $bisnisUnit)
                                ->where('status', 'pending_hapus')
                                ->count();

        $totalStaf = User::where('bisnis_unit', $bisnisUnit)
                         ->where('status_aktif', 'approved') // Hanya menghitung staf yang sudah sah ditarik
                         ->whereIn('role', ['pegawai-retail', 'pegawai-komersial'])
                         ->count();

        $dokumenTerbaru = Dokumen::where('bisnis_unit', $bisnisUnit)->latest()->limit(5)->get();

        return view('kepala_bu.dashboard', compact('totalDokumen', 'dokumenPending', 'totalStaf', 'dokumenTerbaru'));
    }

    // =========================================================================
    // ⏱️ 17. GLOBAL AUDIT TRAIL LOG SYSTEM
    // =========================================================================
    public static function catatLog($aksi, $deskripsi)
    {
        if (Auth::check()) {
            $user = Auth::user();
            LogAktivitas::create([
                'user_id'     => $user->id,
                'nama_user'   => $user->nama_lengkap,
                'role_user'   => $user->role,
                'bisnis_unit' => $user->bisnis_unit,
                'aksi'        => $aksi,
                'deskripsi'   => $deskripsi
            ]);
        }
    }

    public function timelinePegawai()
    {
        $user = Auth::user();

        $logs = LogAktivitas::where('user_id', $user->id)
                    ->orWhere(function($query) use ($user) {
                        $query->where('bisnis_unit', $user->bisnis_unit)
                              ->whereIn('role_user', ['kepala-bu', 'kepala_bu']);
                    })
                    ->latest()
                    ->get();

        return view('pengguna.timeline', compact('logs'));
    }

    public function timelineKepalaBu(Request $request)
    {
        $kepalaBu = Auth::user();
        $bisnisUnitAtasan = $kepalaBu->bisnis_unit;

        $listStaf = User::where('bisnis_unit', $bisnisUnitAtasan)
                        ->where('status_aktif', 'approved') // Hanya memunculkan staf sah di filter dropdown
                        ->whereIn('role', ['pegawai-retail', 'pegawai-komersial'])
                        ->get();

        $query = LogAktivitas::where('bisnis_unit', $bisnisUnitAtasan)->latest();
        $filterStaf = $request->query('staf', 'semua');

        if ($filterStaf !== 'semua') {
            $query->where('user_id', $filterStaf);
        }

        $logs = $query->get();

        return view('pengguna.timeline', compact('logs', 'listStaf', 'filterStaf'));
    }

    public function timelineAdmin(Request $request)
    {
        $query = LogAktivitas::latest();

        $filterRole = $request->query('role', 'semua');
        $filterAksi = $request->query('aksi', 'semua');

        if ($filterRole === 'retail') {
            $query->where('role_user', 'pegawai-retail');
        } elseif ($filterRole === 'komersial') {
            $query->where('role_user', 'pegawai-komersial');
        } elseif ($filterRole === 'kepala_bu') {
            $query->whereIn('role_user', ['kepala-bu', 'kepala_bu']);
        } elseif ($filterRole === 'admin') {
            $query->where('role_user', 'admin');
        }

        if ($filterAksi !== 'semua') {
            $query->where('aksi', $filterAksi);
        }

        $logs = $query->get();

        return view('timeline_admin', compact('logs', 'filterRole', 'filterAksi'));
    }

    // =========================================================================
    // 🛠️ 18. ADMIN: CENTRAL PORTAL AUTHORITY (DASHBOARD UTAMA ADMIN)
    // =========================================================================
    public function dashboardAdmin(Request $request)
    {
        // 1. Perhitungan Statistik Atas (4 Kotak Matriks)
        $totalDokumen = Dokumen::count();
        $totalPengguna = User::where('status_aktif', 'approved')->count();
        $menungguPersetujuan = Dokumen::where('status', 'pending_hapus')->count();
        $totalPerusahaan = Perusahaan::count();

        // 2. Filter Log Jurnal Riwayat Berkas Terbaru (Berdasarkan Rumpun)
        $filter = $request->query('filter', 'semua');
        $queryDokumen = Dokumen::latest();

        if ($filter === 'retail') {
            $queryDokumen->whereIn('bisnis_unit', ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']);
        } elseif ($filter === 'komersial') {
            $queryDokumen->whereNotIn('bisnis_unit', ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar']);
        }

        $dokumenTerbaru = $queryDokumen->limit(10)->get();

        // 3. LOGIKA GRAFIK KANAN: INTEGRITAS PENYIMPANAN AWS S3 (Kuota 5GB Free Tier)
        // Hitung akumulasi dari database (asumsi kolom file_size menyimpan data float/decimal dalam MB)
        $totalTerpakaiMB = Dokumen::sum('file_size') ?? 0; 
        $kuotaMaksimalMB = 5120; // 5 GB = 5120 MB
        
        $sisaPenyimpananMB = $kuotaMaksimalMB - $totalTerpakaiMB;
        if ($sisaPenyimpananMB < 0) $sisaPenyimpananMB = 0;
        
        $persentaseTerpakai = ($totalTerpakaiMB / $kuotaMaksimalMB) * 100;
        $sisaPenyimpananGB = $sisaPenyimpananMB / 1024;

        // 4. LOGIKA GRAFIK KIRI: VOLUMETRIK TRAFIK FILTER (Tahun, Bulan, Minggu)
        $scale = $request->query('scale', 'tahun');
        $chartLabels = [];
        $chartData = [];

        if ($scale === 'minggu') {
            // Distribusi 7 Hari Terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subDays($i);
                $chartLabels[] = $date->translatedFormat('D'); 
                $chartData[] = Dokumen::whereDate('created_at', $date->toDateString())->count();
            }
        } elseif ($scale === 'bulan') {
            // Distribusi 6 Bulan Terakhir
            for ($i = 5; $i >= 0; $i--) {
                $month = \Carbon\Carbon::now()->subMonths($i);
                $chartLabels[] = $month->translatedFormat('F'); 
                $chartData[] = Dokumen::whereMonth('created_at', $month->month)
                                      ->whereYear('created_at', $month->year)
                                      ->count();
            }
        } else {
            // Default: Skala Tahunan (Ambil 4 Tahun dari Tahun Berjalan)
            $currentYear = \Carbon\Carbon::now()->year;
            for ($i = 3; $i >= 0; $i--) {
                $year = $currentYear - $i;
                $chartLabels[] = (string)$year;
                $chartData[] = Dokumen::whereYear('created_at', $year)->count();
            }
        }

        return view('dashboard_admin', compact(
            'totalDokumen', 'totalPengguna', 'menungguPersetujuan', 'totalPerusahaan',
            'dokumenTerbaru', 'filter', 'scale', 'totalTerpakaiMB', 'persentaseTerpakai',
            'sisaPenyimpananGB', 'chartLabels', 'chartData'
        ));
    }
}