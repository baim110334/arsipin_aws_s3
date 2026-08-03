<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    /**
     * Menyimpan Jenis Dokumen / Kategori Baru dari Modal Pop-Up
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
            'singkatan'     => 'required|string|max:10|unique:kategoris,singkatan',
        ], [
            'nama_kategori.unique' => 'Jenis dokumen ini sudah ada!',
            'singkatan.unique'     => 'Kode singkatan ini sudah digunakan!',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'singkatan'     => strtoupper($request->singkatan),
        ]);

        return redirect()->back()->with('success', 'Jenis dokumen baru berhasil ditambahkan!');
    }
}