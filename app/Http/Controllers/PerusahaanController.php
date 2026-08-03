<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BisnisUnit;
use App\Models\Perusahaan;

class PerusahaanController extends Controller
{
    /**
     * Menampilkan daftar perusahaan berdasarkan Bisnis Unit (Retail)
     */
    public function retailPerusahaan($slug_bu)
    {
        $nama_bu_search = str_replace('-', ' ', $slug_bu);
        $bu = BisnisUnit::where('nama_bisnis_unit', 'LIKE', $nama_bu_search)->first();

        if (!$bu) {
            return redirect('/retail')->with('error', 'Bisnis Unit tidak ditemukan!');
        }

        $perusahaans = Perusahaan::where('bisnis_unit_id', $bu->id)->get();

        return view('retail.perusahaan', compact('bu', 'perusahaans', 'slug_bu'));
    }

    /**
     * 🔥 TAMBAHAN BARU: Menampilkan daftar perusahaan berdasarkan Bisnis Unit (Komersial)
     */
    public function commercialPerusahaan($slug_bu)
    {
        $nama_bu_search = str_replace('-', ' ', $slug_bu);
        $bu = BisnisUnit::where('nama_bisnis_unit', 'LIKE', $nama_bu_search)->first();

        if (!$bu) {
            return redirect('/comercial')->with('error', 'Bisnis Unit Komersial tidak ditemukan!');
        }

        $perusahaans = Perusahaan::where('bisnis_unit_id', $bu->id)->get();

        return view('comercial.perusahaan', compact('bu', 'perusahaans', 'slug_bu'));
    }

    /**
     * Tambah Perusahaan Baru (Simpan ke DB)
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $request->validate([
            'nama_pt'        => 'required|string|max:255',
            'bisnis_unit_id' => 'required|exists:bisnis_units,id',
        ]);

        Perusahaan::create([
            'nama_pt'        => strtoupper($request->nama_pt),
            'bisnis_unit_id' => $request->bisnis_unit_id,
        ]);

        return redirect()->back()->with('success', 'Perusahaan baru berhasil ditambahkan!');
    }

    /**
     * Hapus Perusahaan (Delete DB)
     */
    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $pt = Perusahaan::findOrFail($id);
        $pt->delete();

        return redirect()->back()->with('success', 'Perusahaan berhasil dihapus!');
    }

    /**
     * Memperbarui Nama Perusahaan (PT)
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $request->validate([
            'nama_pt' => 'required|string|max:255',
        ]);

        $pt = Perusahaan::findOrFail($id);
        $pt->update([
            'nama_pt' => strtoupper($request->nama_pt),
        ]);

        return redirect()->back()->with('success', 'Nama perusahaan berhasil diperbarui!');
    }

    /**
     * Update Bisnis Unit (Edit Nama & Deskripsi)
     */
    public function updateBu(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $bu = BisnisUnit::findOrFail($id);
        $bu->update([
            'nama_bisnis_unit' => strtoupper($request->nama_bisnis_unit),
            'deskripsi'        => $request->deskripsi,
        ]);

        $redirectUrl = ($bu->kategori === 'commercial') ? '/comercial/' : '/retail/';
        $newSlug = \Illuminate\Support\Str::slug($request->nama_bisnis_unit);

        return redirect($redirectUrl . $newSlug)->with('success', 'Data Bisnis Unit berhasil diperbarui!');
    }

    /**
     * Hapus Bisnis Unit Beserta Seluruh Perusahaan di Bawahnya
     */
    public function destroyBu($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $bu = BisnisUnit::findOrFail($id);
        $targetKategori = $bu->kategori;
        $bu->delete();

        $redirectUrl = ($targetKategori === 'commercial') ? '/comercial' : '/retail';
        return redirect($redirectUrl)->with('success', 'Bisnis Unit berhasil dihapus dari sistem!');
    }
}