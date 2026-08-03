<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BisnisUnit;
use Illuminate\Support\Str;

class BisnisUnitController extends Controller
{
    /**
     * Menampilkan Halaman Bisnis Unit Retail
     */
    public function retailIndex()
    {
        // Ambil semua bisnis unit yang kategorinya 'retail' beserta jumlah perusahaan di bawahnya
        $bisnisUnits = BisnisUnit::where('kategori', 'retail')
                        ->withCount('perusahaans')
                        ->get();

        return view('retail.index', compact('bisnisUnits'));
    }

    /**
     * Menampilkan Halaman Bisnis Unit Commercial
     */
   public function commercialIndex()
    {
        $bisnisUnits = BisnisUnit::where('kategori', 'commercial')
                        ->withCount('perusahaans')
                        ->get();

        return view('comercial.index', compact('bisnisUnits')); // 👈 Sesuaikan dengan folder views/comercial
    }

    /**
     * Menyimpan Bisnis Unit Baru dari Modal Pop-Up
     */
    public function store(Request $request)
    {
        // Proteksi Hak Akses: Hanya Admin yang Boleh Tambah BU
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak! Hanya Admin Pusat yang dapat menambah Bisnis Unit.');
        }

        $request->validate([
            'nama_bisnis_unit' => 'required|string|max:255',
            'kategori'         => 'required|in:retail,commercial',
            'deskripsi'        => 'nullable|string|max:500',
        ]);

        BisnisUnit::create([
            'nama_bisnis_unit' => strtoupper($request->nama_bisnis_unit), // Otomatis KAPITAL
            'kategori'         => $request->kategori,
            'deskripsi'        => $request->deskripsi ?? 'Pusat manajemen dan arsip dokumentasi operasional unit kerja.',
        ]);

        return redirect()->back()->with('success', 'Bisnis Unit baru berhasil dibuat!');
    }
}