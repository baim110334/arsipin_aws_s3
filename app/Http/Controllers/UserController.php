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
    // 1. Tampilkan Halaman Utama (Tabel Pengguna)
    public function index(Request $request)
    {
        $cari = $request->query('cari');

        // Pencarian disesuaikan dengan atribut ERD: nama_lengkap atau email
        if (!empty($cari)) {
            $users = User::where('nama_lengkap', 'like', '%' . $cari . '%')
                        ->orWhere('email', 'like', '%' . $cari . '%')
                        ->get();
        } else {
            $users = User::all();
        }

        return view('pengguna.index', compact('users'));
    }

    // 2. Tampilkan Form Tambah Pengguna
    public function create()
    {
        // Ambil semua data bisnis unit dari database
        $bisnisUnits = BisnisUnit::all(); 
        return view('pengguna.tambah', compact('bisnisUnits'));
    }

    // 3. Tampilkan Halaman Edit Pengguna
    public function edit($id)
    {
        $user = User::findOrFail($id);
        // Ambil semua data bisnis unit dari database
        $bisnisUnits = BisnisUnit::all(); 
        return view('pengguna.edit', compact('user', 'bisnisUnits'));
    }

    // 4. Proses Simpan Akun Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:admin,pegawai-retail,pegawai-komersial,kepala-bu,kepala_bu',
            'bisnis_unit'  => 'nullable|string|max:255',
            'no_hp'        => 'nullable|string|max:15',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'bisnis_unit'  => $request->bisnis_unit,
            'no_hp'        => $request->no_hp,
        ]);

        // 🌟 DI SINI: Admin mencatat pembuatan akun baru
        self::catatLog('Kelola Akun', Auth::user()->nama_lengkap . ' membuat akun baru dengan nama: ' . $userBaru->nama_lengkap . ' (' . strtoupper($userBaru->role) . ')');

        return redirect('/kelola-akun')->with('success', 'Akun baru berhasil ditambahkan');
    }

    // 5. Proses Simpan Perubahan / Update Akun
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $id,
            'email'        => 'required|string|email|max:255|unique:users,email,' . $id,
            'role'         => 'required|in:admin,pegawai-retail,pegawai-komersial,kepala-bu,kepala_bu',
            'bisnis_unit'  => 'nullable|string|max:255',
            'no_hp'        => 'nullable|string|max:15',
        ]);

        $user->nama_lengkap = $request->nama_lengkap;
        $user->username     = $request->username;
        $user->email        = $request->email;
        $user->role         = $request->role;
        $user->bisnis_unit  = $request->bisnis_unit;
        $user->no_hp        = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // 🌟 DI SINI: Admin mencatat perubahan data akun
        self::catatLog('Kelola Akun', Auth::user()->nama_lengkap . ' memperbarui data akun milik: ' . $user->nama_lengkap);

        return redirect('/kelola-akun')->with('success', 'Data akun berhasil diperbarui!');
    }

    // 6. Proses Hapus Akun secara Permanen
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // 🌟 DI SINI: Catat dulu aktivitas penghapusan sebelum data user hilang dari MySQL
        self::catatLog('Kelola Akun', Auth::user()->nama_lengkap . ' menghapus akun pengguna bernama: ' . $user->nama_lengkap);
        
        $user->delete();

        return redirect('/kelola-akun')->with('success', 'Akun berhasil dihapus!');
    }

    // 7. Tampilkan Staf Unit untuk Kepala BU
    public function stafUnit()
    {
        $kepalaBu = Auth::user();

        // 1. Ambil staf aktif yang saat ini berada di bawah unit bisnis Kepala BU
        $staf = User::where('bisnis_unit', $kepalaBu->bisnis_unit)
                    ->whereIn('role', ['pegawai-retail', 'pegawai-komersial'])
                    ->get();

        // 2. Tentukan kandidat mana yang boleh dilihat berdasarkan unit bisnis Kepala BU
        if ($kepalaBu->bisnis_unit === 'spbu') {
            $allowedRoles = ['pegawai-retail'];
        } else {
            $allowedRoles = ['pegawai-komersial'];
        }

        // 3. Ambil data kandidat pegawai baru yang bisnis_unit-nya masih kosong
        $kandidat = User::whereNull('bisnis_unit')
                        ->whereIn('role', $allowedRoles)
                        ->get();

        return view('kepala_bu.staf', compact('staf', 'kandidat'));
    }

    // 8. Rekrut Pegawai masuk ke Unit Bisnis
    public function rekrutStaf($id)
    {
        $kepalaBu = Auth::user();
        $pegawai = User::findOrFail($id);

        $pegawai->bisnis_unit = $kepalaBu->bisnis_unit;
        $pegawai->save();

        return redirect()->back()->with('success', 'Berhasil merekrut ' . $pegawai->nama_lengkap . ' ke dalam unit bisnis.');
    }

    // 9. Keluarkan Pegawai dari Unit Bisnis (Kembali jadi NULL)
    public function keluarkanStaf($id)
    {
        $pegawai = User::findOrFail($id);
        
        $pegawai->bisnis_unit = null;
        $pegawai->save();

        return redirect()->back()->with('success', 'Berhasil mengeluarkan ' . $pegawai->nama_lengkap . ' dari unit bisnis.');
    }

    // 10. Tampilkan dokumen yang berstatus pending_hapus sesuai unit bisnis Kepala BU
    public function approvalIndex()
    {
        $kepalaBu = Auth::user();

        $dokumen = Dokumen::where('bisnis_unit', $kepalaBu->bisnis_unit)
                        ->where('status', 'pending_hapus')
                        ->get();

        return view('kepala_bu.approval', compact('dokumen'));
    }

    // 11. Setujui Penghapusan (BENERAN MENGHAPUS DARI DATABASE DAFTAR SEKARANG)
    public function setujuiHapus($id)
    {
        // 1. Cari dokumennya di database
        $dokumen = Dokumen::findOrFail($id);
        
        // 2. 🌟 CATAT LOG-NYA TERLEBIH DAHULU (Sebelum datanya dihapus permanen!)
        LogAktivitas::create([
            'user_id'     => Auth::id(),
            'nama_user'   => Auth::user()->nama_lengkap, // Nama Kepala BU (Pak Anas/Bu Sri)
            'role_user'   => Auth::user()->role,
            'bisnis_unit' => $dokumen->bisnis_unit, // Dikunci ke unit dokumen staf agar Imam bisa baca!
            'aksi'        => 'Setujui Pemusnahan',
            'deskripsi'   => Auth::user()->nama_lengkap . ' menyetujui pemusnahan permanen berkas dokumen: ' . $dokumen->nama_dokumen
        ]);

        // 3. 🔥 EKSEKUSI MATI: Hapus dokumen secara total dari database MySQL lokal
        $dokumen->delete(); 

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui untuk dihapus dari sistem.');
    }

    // 12. Tolak Penghapusan (Kembalikan status menjadi 'active' semula)
    public function tolakHapus($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Kembalikan status dokumen menjadi aktif normal
        $dokumen->status = 'active'; 
        $dokumen->save();

        // 🌟 LOG DINAMIS: Mengunci bisnis_unit dokumen supaya bisa dibaca di timeline pegawai terkait!
        LogAktivitas::create([
            'user_id'     => Auth::id(),
            'nama_user'   => Auth::user()->nama_lengkap,
            'role_user'   => Auth::user()->role,
            'bisnis_unit' => $dokumen->bisnis_unit, // Kunci unit dokumen asal staf
            'aksi'        => 'Tolak Pemusnahan',
            'deskripsi'   => Auth::user()->nama_lengkap . ' menolak permohonan pemusnahan berkas dokumen: ' . $dokumen->nama_dokumen . ' (Status kembali Aktif)'
        ]);

        return redirect()->back()->with('success', 'Permohonan hapus ditolak. Dokumen kembali aman dan aktif.');
    }

    // 13. Ajukan Permohonan Hapus Dokumen (Untuk Pegawai Retail/Komersial)
    public function ajukanHapusDokumen($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $dokumen->status = 'pending_hapus';
        $dokumen->save();
        
        self::catatLog('Ajukan Hapus', Auth::user()->nama_lengkap . ' mengajukan permohonan pemusnahan berkas dokumen: ' . $dokumen->nama_dokumen);
        return redirect()->back()->with('success', 'Permohonan hapus dokumen berhasil dikirim ke Kepala BU!');
    }

    // 14. Tampilkan Dashboard Utama Pegawai Retail
    public function dashboardRetail()
    {
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

    // 15. Tampilkan Dashboard Utama Pegawai Komersial
    public function dashboardComercial(Request $request)
    {
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

    // 16. Tampilkan Statistik Dashboard Khusus Kepala BU
    public function dashboardKepalaBu()
    {
        $kepalaBu = Auth::user();
        $bisnisUnit = $kepalaBu->bisnis_unit;

        $totalDokumen = Dokumen::where('bisnis_unit', $bisnisUnit)->count();

        $dokumenPending = Dokumen::where('bisnis_unit', $bisnisUnit)
                                ->where('status', 'pending_hapus')
                                ->count();

        $totalStaf = User::where('bisnis_unit', $bisnisUnit)
                         ->whereIn('role', ['pegawai-retail', 'pegawai-komersial'])
                         ->count();

        $dokumenTerbaru = Dokumen::where('bisnis_unit', $bisnisUnit)->latest()->limit(5)->get();

        return view('kepala_bu.dashboard', compact('totalDokumen', 'dokumenPending', 'totalStaf', 'dokumenTerbaru'));
    }

    // 🌟 MESIN PENCATAT OTOMATIS TIMELINE
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

    // =========================================================================
    // 🌟 1. TIMELINE PEGAWAI (REVISI SINKRONISASI TOTAL)
    // =========================================================================
    public function timelinePegawai()
    {
        $user = Auth::user();

        // 🚀 Solusi Cerdas: Tarik log aktivitas pribadi pegawai ATAU log aksi Kepala BU pada bisnis_unit yang sama
        $logs = LogAktivitas::where('user_id', $user->id)
                    ->orWhere(function($query) use ($user) {
                        $query->where('bisnis_unit', $user->bisnis_unit)
                              ->whereIn('role_user', ['kepala-bu', 'kepala_bu']);
                    })
                    ->latest()
                    ->get();

        return view('pengguna.timeline', compact('logs'));
    }

   // =========================================================================
    // 🌟 2. TIMELINE KEPALA BU: Monitoring aktivitas staf + Fitur Filter Anggota
    // =========================================================================
    public function timelineKepalaBu(\Illuminate\Http\Request $request)
    {
        $kepalaBu = Auth::user();
        $bisnisUnitAtasan = $kepalaBu->bisnis_unit;

        // 1. Ambil semua staf retail/komersial di bawah unit bisnis ini untuk isi dropdown filter
        $listStaf = User::where('bisnis_unit', $bisnisUnitAtasan)
                        ->whereIn('role', ['pegawai-retail', 'pegawai-komersial'])
                        ->get();

        // 2. Mulai query log aktivitas berdasarkan unit bisnis terkait
        $query = LogAktivitas::where('bisnis_unit', $bisnisUnitAtasan)->latest();

        // 3. Tangkap filter user dari request dropdown
        $filterStaf = $request->query('staf', 'semua');

        if ($filterStaf !== 'semua') {
            // Saring log yang dibuat oleh user_id tertentu
            $query->where('user_id', $filterStaf);
        }

        $logs = $query->get();

        // Kirim $listStaf dan $filterStaf ke view blade
        return view('pengguna.timeline', compact('logs', 'listStaf', 'filterStaf'));
    }

    // =========================================================================
    // 🌟 3. TIMELINE ADMIN: Mata elang global, melihat semua dengan filter
    // =========================================================================
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
}