<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('dokumens', function (Blueprint $table) {
        $table->id();
        
        // ==========================================
        // KUNCI RELASI (FOREIGN KEYS) SESUAI ERD
        // ==========================================
        // 1. Mencatat siapa yang upload (Hubungan ke tabel users)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        // 🌟 2. Hubungan ke tabel kategoris (Agar terpusat sesuai ERD)
        // Hubungkan ke tabel 'kategoris' jika kamu menggunakannya, jika tidak, bisa tetap pakai string tipe_keuangan
        // $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->onDelete('set null');

        // ==========================================
        // METADATA ORGANISASI & KELOMPOK
        // ==========================================
        $table->string('bisnis_unit'); // contoh: spbu, lpg-pso, inmar
        $table->string('perusahaan');  // contoh: PT SCK, PT MMS

        // ==========================================
        // METADATA DATA DOKUMEN
        // ==========================================
        $table->string('nama_dokumen');         // contoh: Laporan Pajak Mei
        $table->string('no_dokumen')->unique();    // contoh: TAX/RETAIL/2026/042
        $table->string('tipe_keuangan');        // contoh: Invoice, Kuitansi, Pajak, Slip Gaji
        
        // 🌟 ATRIBUT TAMBAHAN BIAR ERD MAKIN MATANG:
        $table->text('keterangan')->nullable(); // Catatan tambahan dari pengupload
        $table->string('bulan_buku')->nullable();
        $table->string('tahun_buku')->nullable();
        $table->string('jilid_buku')->nullable();

        // ==========================================
        // DATA FISIK PENYIMPANAN CLOUD AWS S3
        // ==========================================
        $table->string('file_name_original');   // Nama asli file pas diupload (laporan.pdf)
        $table->string('s3_object_key');        // Path rahasia di AWS S3 (arsip/retail/unik-nama.pdf)
        $table->string('file_size');            // Ukuran file (contoh: 2.4 MB)
        
        // ==========================================
        // STATUS ALUR KERJA (WORKFLOW STATUS)
        // ==========================================
        // 'active' = normal, 'pending_delete' = menunggu persetujuan hapus dari Kepala BU
        $table->string('status')->default('active'); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
