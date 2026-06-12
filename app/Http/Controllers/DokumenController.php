<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    /**
     * Menampilkan daftar dokumen (Versi Super Kebal Spasi & Strip untuk Bisnis Unit DAN Perusahaan!)
     */
    public function index($bisnis_unit, $perusahaan)
    {
        // 1. Ambil versi strip dan versi spasi untuk BISNIS UNIT
        $versiStripBU = str_replace(' ', '-', $bisnis_unit); // Contoh: bbm-retail
        $versiSpasiBU = str_replace('-', ' ', $bisnis_unit); // Contoh: bbm retail

        // 🌟 2. JINAKKAN PERUSAHAAN: Ambil versi strip dan spasi untuk PERUSAHAAN agar anti-eror!
        $versiStripPT = str_replace(' ', '-', $perusahaan); // Contoh: pt-sck atau PT-SCK
        $versiSpasiPT = str_replace('-', ' ', $perusahaan); // Contoh: pt sck atau PT SCK

        // 3. Query Pintar: Cari yang mirip versi strip ATAU spasi untuk kedua parameter
        $list_dokumen = Dokumen::where(function($query) use ($versiStripPT, $versiSpasiPT) {
                                    $query->where('perusahaan', 'LIKE', $versiStripPT)
                                          ->orWhere('perusahaan', 'LIKE', $versiSpasiPT);
                                })
                                ->where(function($query) use ($versiStripBU, $versiSpasiBU) {
                                    $query->where('bisnis_unit', 'LIKE', $versiStripBU)
                                          ->orWhere('bisnis_unit', 'LIKE', $versiSpasiBU);
                                })
                                ->get();

        // 4. Format judul header halaman agar tampil estetik
        $nama_bu = strtoupper(str_replace('-', ' ', $bisnis_unit));
        $nama_pt = strtoupper(str_replace('-', ' ', $perusahaan)); // Biar di header tampil "PT SCK"

        // 5. Deteksi folder view berdasarkan URL asli
        $bisnisUnitLower = strtolower($bisnis_unit);
        $kelompokRetail = ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar'];

        if (in_array($bisnisUnitLower, $kelompokRetail)) {
            return view('retail.dokumen', compact('nama_bu', 'nama_pt', 'list_dokumen', 'bisnis_unit'));
        }

        return view('comercial.dokumen', compact('nama_bu', 'nama_pt', 'list_dokumen', 'bisnis_unit'));
    }

    /**
     * Menampilkan halaman form upload dokumen
     */
    public function create($bisnis_unit, $perusahaan)
    {
        $bisnisUnitLower = strtolower($bisnis_unit);
        $kelompokRetail = ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar'];

        if (in_array($bisnisUnitLower, $kelompokRetail)) {
            return view('retail.upload', compact('bisnis_unit', 'perusahaan'));
        }

        return view('comercial.upload', compact('bisnis_unit', 'perusahaan'));
    }

    /**
     * 3. PROSES SIMPAN FILE PDF KE HARDDISK & DATABASE (Full Terintegrasi & Anti-Crash)
     */
    public function store(Request $request)
    {
        // 🌟 1. MEMBUAT VALIDATOR SECARA MANUAL (Biar sinkron dengan pengecekan $validator->fails())
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'bisnis_unit'   => 'required|string',
            'perusahaan'    => 'required|string',
            'nama_dokumen'  => 'required|string|max:255',
            'no_dokumen'    => 'required|string|unique:dokumens,no_dokumen', // Kunci nomor dokumen anti-kembar
            'tipe_keuangan' => 'required|string',
            'bulan_buku'    => 'required|string|max:20', // Wajib diisi!
            'tahun_buku'    => 'required|string|max:20', // Mendukung rentang "2025-2026"
            'jilid_buku'    => 'required|string|max:10', // Wajib diisi!
            'file_dokumen'  => 'required|file|mimes:pdf|max:3072', // Maksimal 3MB
        ], [
            // Custom Announcement Bahasa Indonesia biar dosen penguji senang wkwkwk
            'file_dokumen.mimes' => 'Format berkas wajib berupa PDF! Ekstensi lain seperti PPT, Word, atau Excel dilarang masuk.',
            'file_dokumen.max'   => 'Ukuran berkas terlalu gajah, maksimal hanya boleh 3MB!',
            'no_dokumen.unique'  => 'Nomor dokumen resmi ini sudah terdaftar di sistem, silakan sesuaikan jilid/bulan buku.',
        ]);

        // 🌟 2. JIKA GAGAL VALIDASI, KEMBALIKAN SECARA ANGGUN (Form gak bakal crash halaman merah!)
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        // 3. PROSES PEMBACAAN DAN PENYIMPANAN BERKAS FISIK PDF
        $file = $request->file('file_dokumen');
        $originalName = $file->getClientOriginalName();
        $fileSize = round($file->getSize() / 1024 / 1024, 2) . ' MB';

        // Deteksi folder tujuan berdasarkan nama unit bisnis
        $bisnisUnitLower = strtolower($request->bisnis_unit);
        $kelompokRetail = ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar'];

        if (in_array($bisnisUnitLower, $kelompokRetail)) {
            $folderDivisi = 'retail';
        } else {
            $folderDivisi = 'commercial';
        }

        // Amankan nama file di storage lokal dengan string acak
        $filename = \Str::random(20) . '.' . $file->getClientOriginalExtension();
        $folderPath = 'arsip/' . $folderDivisi; 
        $path = $file->storeAs($folderPath, $filename, 'public');

        $userId = Auth::id();

        // 4. INJECT DATA BARU KE DALAM TABEL MYSQL DENGAN AMAN
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \App\Models\Dokumen::create([
            'user_id'            => $userId, 
            'bisnis_unit'        => $request->bisnis_unit,
            'perusahaan'         => $request->perusahaan,
            'nama_dokumen'       => $request->nama_dokumen,
            'no_dokumen'         => $request->no_dokumen, // Menyimpan nomor hasil racikan JS otomatis
            'tipe_keuangan'      => $request->tipe_keuangan,
            'bulan_buku'         => $request->bulan_buku, // 🌟 PASTIKAN INI ADA DI DATABASE KAMU ANGELA
            'tahun_buku'         => $request->tahun_buku, 
            'jilid_buku'         => $request->jilid_buku, // 🌟 PASTIKAN INI ADA DI DATABASE KAMU ANGELA
            'keterangan'         => $request->keterangan, 
            'file_name_original' => $originalName,
            's3_object_key'      => $path,
            'file_size'          => $fileSize,
            'status'             => 'active',
        ]);

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 🌟 5. REKAM HISTORI KE TIMELINE DIGITAL ARSIPIN
        \App\Http\Controllers\UserController::catatLog('Upload Dokumen', Auth::user()->nama_lengkap . ' mengunggah dokumen baru dengan nomor ' . $request->no_dokumen);

        // 🌟 6. JALUR PULANG DINAMIS ANTI-NYASAR
        if (in_array($bisnisUnitLower, $kelompokRetail)) {
            return redirect()->route('retail.dokumen', [$request->bisnis_unit, $request->perusahaan])
                             ->with('success', 'Dokumen retail berhasil diarsipkan!');
        }

        return redirect()->route('comercial.dokumen', [$request->bisnis_unit, $request->perusahaan])
                         ->with('success', 'Dokumen komersial berhasil diarsipkan!');
    }

    /**
     * Menampilkan pratinjau (preview) berkas PDF
     */
    public function preview($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $pathFile = $dokumen->s3_object_key; 

        if (!Storage::disk('public')->exists($pathFile)) {
            abort(404, 'Maaf Angela, file fisik PDF tidak ditemukan di dalam folder storage!');
        }

        $file = Storage::disk('public')->get($pathFile);
        $type = Storage::disk('public')->mimeType($pathFile);

        return response($file, 200)->header('Content-Type', $type);
    }

    /**
     * Menghapus dokumen dari database secara absolut (Khusus Admin - SUDAH TERHUBUNG TIMELINE)
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Maaf, hanya Akun Admin yang memiliki otoritas untuk menghapus arsip fisik!');
        }

        $dokumen = Dokumen::findOrFail($id);
        $pathFile = $dokumen->s3_object_key;

        // 🌟 SUNTIKAN INTEGRASI LOG TIMELINE OLEH MARICA (Catat sebelum musnah)
        \App\Http\Controllers\UserController::catatLog(
            'Hapus Dokumen', 
            Auth::user()->nama_lengkap . ' menghapus permanen berkas dokumen: ' . $dokumen->nama_dokumen . ' dari database lokal'
        );

        // Hapus file fisik di storage lokal
        if (Storage::disk('public')->exists($pathFile)) {
            Storage::disk('public')->delete($pathFile);
        }

        // Hapus baris data di MySQL
        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus secara permanen!');
    }
    // 🌟 FUNGSI TAMPIL FORM EDIT DOKUMEN
    public function editDokumen($id)
    {
        $dokumen = \App\Models\Dokumen::findOrFail($id);
        return view('retail.edit_dokumen', compact('dokumen')); // Kita pakai satu form global yang rapi
    }

   // 🌟 FUNGSI PROSES UPDATE DATA KE DATABASE (SUDAH DI-DEEP DIVE OLEH MARICA)
    public function updateDokumen(Request $request, $id)
    {
        $dokumen = \App\Models\Dokumen::findOrFail($id);

        // 1. Validasi data inputan baru (Wajib daftarkan bulan, tahun, dan jilid!)
        // Pasang id di bagian akhir unique agar tidak bentrok dengan nomor lama dirinya sendiri
        $request->validate([
            'nama_dokumen'  => 'required|string|max:255',
            'no_dokumen'    => 'required|string|max:255|unique:dokumens,no_dokumen,' . $id, // 🔑 KUNCI AMAN ANTI-BENTROK
            'tipe_keuangan' => 'required|string',
            'bulan_buku'    => 'required|string|max:20',  // 🌟 BARU: Wajib ditangkap backend
            'tahun_buku'    => 'required|string|max:20',  // 🌟 BARU: Wajib ditangkap backend
            'jilid_buku'    => 'required|string|max:10',  // 🌟 BARU: Wajib ditangkap backend
            'keterangan'    => 'nullable|string',
        ]);

        // 2. Eksekusi pembaruan metadata ke database MySQL lokal
        $dokumen->update([
            'nama_dokumen'  => $request->nama_dokumen,
            'no_dokumen'    => $request->no_dokumen, // Nomor baru hasil racikan JS otomatis
            'tipe_keuangan' => $request->tipe_keuangan,
            'bulan_buku'    => $request->bulan_buku,  // 🌟 SIMPAN KE DATABASE
            'tahun_buku'    => $request->tahun_buku,  // 🌟 SIMPAN KE DATABASE
            'jilid_buku'    => $request->jilid_buku,  // 🌟 SIMPAN KE DATABASE
            'keterangan'    => $request->keterangan,
        ]);

        // 3. 🌟 JALUR PULANG ASLI ANGELA: Kembalikan ke halaman daftar dokumen sesuai rumpun divisinya
        if (in_array($dokumen->bisnis_unit, ['spbu', 'lpg-pso', 'lpg-npso', 'sppbe', 'bbm-retail', 'inmar'])) {
            return redirect()->route('retail.dokumen', [$dokumen->bisnis_unit, $dokumen->perusahaan])
                             ->with('success', 'Informasi dokumen retail berhasil diperbarui dengan nomor resmi baru!');
        } else {
            return redirect()->route('comercial.dokumen', [$dokumen->bisnis_unit, $dokumen->perusahaan])
                             ->with('success', 'Informasi dokumen komersial berhasil diperbarui dengan nomor resmi baru!');
        }
    }
}