<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\BisnisUnit;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    /**
     * Menampilkan daftar dokumen (Dinamis DB & Kebal Spasi/Strip)
     */
    public function index($bisnis_unit, $perusahaan)
    {
        // 1. Ambil versi strip dan versi spasi
        $versiStripBU = str_replace(' ', '-', $bisnis_unit);
        $versiSpasiBU = str_replace('-', ' ', $bisnis_unit);

        $versiStripPT = str_replace(' ', '-', $perusahaan);
        $versiSpasiPT = str_replace('-', ' ', $perusahaan);

        // 2. Query Dokumen berdasarkan BU dan Perusahaan
        $list_dokumen = Dokumen::where(function($query) use ($versiStripPT, $versiSpasiPT) {
                                    $query->where('perusahaan', 'LIKE', $versiStripPT)
                                          ->orWhere('perusahaan', 'LIKE', $versiSpasiPT);
                                })
                                ->where(function($query) use ($versiStripBU, $versiSpasiBU) {
                                    $query->where('bisnis_unit', 'LIKE', $versiStripBU)
                                          ->orWhere('bisnis_unit', 'LIKE', $versiSpasiBU);
                                })
                                ->get();

        // 3. Format nama header
        $nama_bu = strtoupper(str_replace('-', ' ', $bisnis_unit));
        $nama_pt = strtoupper(str_replace('-', ' ', $perusahaan));

        // 🔥 4. Sedot Data Kategori Dinamis dari DB untuk Dropdown Filter
        $kategoris = Kategori::all();

        // 5. Cek Rumpun Divisi Langsung dari Database (Bukan Hardcoded Lagi)
        $buObj = BisnisUnit::where('nama_bisnis_unit', 'LIKE', $nama_bu)->first();
        $isRetail = $buObj ? ($buObj->kategori === 'retail') : request()->is('retail*');

        if ($isRetail) {
            return view('retail.dokumen', compact('nama_bu', 'nama_pt', 'list_dokumen', 'bisnis_unit', 'kategoris'));
        }

        return view('comercial.dokumen', compact('nama_bu', 'nama_pt', 'list_dokumen', 'bisnis_unit', 'kategoris'));
    }

    /**
     * Menampilkan halaman form upload dokumen
     */
    public function create($bisnis_unit, $perusahaan)
    {
        // 🚨 BENTENG PERTAHANAN 1: Hadang Kepala BU yang mencoba membuka form upload divisi lain
        if (in_array(Auth::user()->role, ['kepala-bu', 'kepala_bu'])) {
            if (strtolower(Auth::user()->bisnis_unit) !== strtolower($bisnis_unit)) {
                return redirect()->back()->with('error', 'Otoritas ditolak! Anda tidak memiliki hak akses untuk mengunggah berkas ke unit operasional ini.');
            }
        }

        // 🔥 SEDOT KATEGORI DINAMIS UNTUK DROPDOWN FORM UPLOAD (FIX ERROR UNDEFINED $kategoris)
        $kategoris = Kategori::all();

        // Cek Rumpun Divisi dari Database
        $nama_bu = strtoupper(str_replace('-', ' ', $bisnis_unit));
        $buObj = BisnisUnit::where('nama_bisnis_unit', 'LIKE', $nama_bu)->first();
        $isRetail = $buObj ? ($buObj->kategori === 'retail') : request()->is('retail*');

        if ($isRetail) {
            return view('retail.upload', compact('bisnis_unit', 'perusahaan', 'kategoris'));
        }

        return view('comercial.upload', compact('bisnis_unit', 'perusahaan', 'kategoris'));
    }

    /**
     * PROSES SIMPAN FILE PDF KE CLOUD AMAZON S3 & DATABASE
     */
    public function store(Request $request)
    {
        // 🚨 BENTENG PERTAHANAN 2
        if (in_array(Auth::user()->role, ['kepala-bu', 'kepala_bu'])) {
            if (strtolower(Auth::user()->bisnis_unit) !== strtolower($request->bisnis_unit)) {
                return redirect()->back()->with('error', 'Otoritas manipulasi data ditolak! Unit operasional ini berada di luar wilayah pengawasan Anda.');
            }
        }

        // 1. Validasi Input Form
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'bisnis_unit'   => 'required|string',
            'perusahaan'    => 'required|string',
            'nama_dokumen'  => 'required|string|max:255',
            'no_dokumen'    => 'required|string|unique:dokumens,no_dokumen', 
            'tipe_keuangan' => 'required|string',
            'bulan_buku'    => 'required|string|max:20', 
            'tahun_buku'    => 'required|string|max:20', 
            'jilid_buku'    => 'required|string|max:10', 
            'file_dokumen'  => 'required|file|mimes:pdf|max:3072', 
        ], [
            'no_dokumen.unique' => 'Nomor dokumen resmi ini sudah pernah terdaftar di dalam arsip digital!',
            'file_dokumen.max'  => 'Ukuran berkas PDF terlalu besar, maksimal batas sistem adalah 3 MB.',
            'file_dokumen.mimes'=> 'Sistem hanya menerima berkas resmi berformat PDF.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 2. Ambil File Unggahan
        $file = $request->file('file_dokumen');
        $originalName = $file->getClientOriginalName();
        $fileSize = round($file->getSize() / 1024 / 1024, 2) . ' MB';

        // 3. Bersihkan Nama Unit Bisnis untuk Folder S3
        $cleanBU = strtolower(basename($request->bisnis_unit));
        $nama_bu_search = strtoupper(str_replace('-', ' ', $cleanBU));
        $buObj = BisnisUnit::where('nama_bisnis_unit', 'LIKE', $nama_bu_search)->first();
        
        $folderDivisi = ($buObj && $buObj->kategori === 'commercial') ? 'commercial' : 'retail';

        // 4. Siapkan Target Folder S3
        $namaFileMurni = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $targetFolder = 'arsip/' . $folderDivisi;

        // 5. Upload ke Cloud AWS S3
        $jalurAwanS3 = Storage::disk('s3')->putFileAs(
            $targetFolder,
            $file,
            $namaFileMurni
        );

        $userId = Auth::id();

        // 6. Inject Metadata ke Database
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Dokumen::create([
            'user_id'            => $userId, 
            'bisnis_unit'        => $cleanBU, 
            'perusahaan'         => $request->perusahaan,
            'nama_dokumen'       => $request->nama_dokumen, 
            'no_dokumen'         => $request->no_dokumen, 
            'tipe_keuangan'      => $request->tipe_keuangan,
            'bulan_buku'         => $request->bulan_buku, 
            'tahun_buku'         => $request->tahun_buku, 
            'jilid_buku'         => $request->jilid_buku, 
            'keterangan'         => $request->keterangan, 
            'file_name_original' => $originalName,
            's3_object_key'      => $jalurAwanS3,
            'file_size'          => $fileSize,
            'status'             => 'active',
        ]);

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 7. Catat Audit Trail
        \App\Http\Controllers\UserController::catatLog('Upload Dokumen', Auth::user()->nama_lengkap . ' mengunggah dokumen baru dengan nomor ' . $request->no_dokumen . ' ke Amazon S3 Cloud Storage.');

        // 8. Redirect Kembali ke View
        if ($folderDivisi === 'retail') {
            return redirect()->route('retail.dokumen', [$cleanBU, $request->perusahaan])
                             ->with('success', 'Dokumen retail berhasil diarsipkan ke Cloud S3!');
        }

        return redirect()->route('comercial.dokumen', [$cleanBU, $request->perusahaan])
                         ->with('success', 'Dokumen komersial berhasil diarsipkan ke Cloud S3!');
    }

    /**
     * Preview Berkas PDF Pre-signed URL S3 Privat
     */
    public function preview($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $pathFile = $dokumen->s3_object_key; 

        if (!Storage::disk('s3')->exists($pathFile)) {
            abort(404, 'Maaf Angela, file fisik PDF tidak ditemukan di dalam bucket Amazon S3!');
        }

        $s3Url = Storage::disk('s3')->temporaryUrl($pathFile, now()->addMinutes(5));
        return redirect()->away($s3Url);
    }

    /**
     * Menghapus Dokumen Permanen (S3 + DB)
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Maaf, hanya Akun Admin yang memiliki otoritas untuk menghapus arsip fisik!');
        }

        $dokumen = Dokumen::findOrFail($id);
        $pathFile = $dokumen->s3_object_key;

        \App\Http\Controllers\UserController::catatLog(
            'Hapus Dokumen', 
            Auth::user()->nama_lengkap . ' menghapus permanen berkas dokumen: ' . $dokumen->nama_dokumen . ' dari Amazon S3 Cloud Storage.'
        );

        if (Storage::disk('s3')->exists($pathFile)) {
            Storage::disk('s3')->delete($pathFile);
        }

        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus secara permanen dari Cloud S3!');
    }
    
    /**
     * Form Edit Dokumen
     */
    public function editDokumen($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $perusahaan = $dokumen->perusahaan;
        $kategoris = Kategori::all(); // SEDOT DATA KATEGORI DINAMIS

        if (in_array(Auth::user()->role, ['kepala-bu', 'kepala_bu'])) {
            if (strtolower(Auth::user()->bisnis_unit) !== strtolower($dokumen->bisnis_unit)) {
                return redirect()->back()->with('error', 'Otoritas ditolak! Anda tidak memiliki izin untuk mengubah arsip dokumen milik unit operasional lain.');
            }
        }

        $nama_bu = strtoupper(str_replace('-', ' ', $dokumen->bisnis_unit));
        $buObj = BisnisUnit::where('nama_bisnis_unit', 'LIKE', $nama_bu)->first();
        
        if (($buObj && $buObj->kategori === 'commercial') || str_contains(request()->url(), 'comercial')) {
            return view('comercial.edit_dokumen', compact('dokumen', 'perusahaan', 'kategoris')); 
        }

        return view('retail.edit_dokumen', compact('dokumen', 'perusahaan', 'kategoris')); 
    }

    /**
     * Memproses Update Dokumen
     */
    public function updateDokumen(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);

        $request->validate([
            'nama_dokumen'  => 'required|string|max:255',
            'no_dokumen'    => 'required|string|max:255|unique:dokumens,no_dokumen,' . $id, 
            'tipe_keuangan' => 'required|string',
            'bulan_buku'    => 'required|string|max:20',  
            'tahun_buku'    => 'required|string|max:20',  
            'jilid_buku'    => 'required|string|max:10',  
            'keterangan'    => 'nullable|string',
        ]);

        $dokumen->update([
            'nama_dokumen'  => $request->nama_dokumen,
            'no_dokumen'    => $request->no_dokumen, 
            'tipe_keuangan' => $request->tipe_keuangan,
            'bulan_buku'    => $request->bulan_buku,  
            'tahun_buku'    => $request->tahun_buku,  
            'jilid_buku'    => $request->jilid_buku,  
            'keterangan'    => $request->keterangan,
        ]);

        // 🔥 SUNTIKAN AUDIT TRAIL: Rekam aktivitas edit dokumen ke database log
        \App\Http\Controllers\UserController::catatLog(
            'Edit Dokumen', 
            Auth::user()->nama_lengkap . ' memperbarui metadata dokumen resmi bernomor: ' . $request->no_dokumen
        );

        $nama_bu = strtoupper(str_replace('-', ' ', $dokumen->bisnis_unit));
        $buObj = BisnisUnit::where('nama_bisnis_unit', 'LIKE', $nama_bu)->first();

        if ($buObj && $buObj->kategori === 'commercial') {
            return redirect()->route('comercial.dokumen', [$dokumen->bisnis_unit, $dokumen->perusahaan])
                             ->with('success', 'Informasi dokumen komersial berhasil diperbarui!');
        }

        return redirect()->route('retail.dokumen', [$dokumen->bisnis_unit, $dokumen->perusahaan])
                         ->with('success', 'Informasi dokumen retail berhasil diperbarui!');
    }
}