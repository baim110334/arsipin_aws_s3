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
        // 🌟 PASTIKAN DI SINI TERTULIS Schema::create BUKAN Route::create YA ANGELA!
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_user');
            $table->string('role_user');
            $table->string('bisnis_unit')->nullable();
            $table->string('aksi');
            $table->text('deskripsi');
            $table->timestamps();

            // Relasi aman ke tabel users, jika user dihapus, log tidak ikut hilang (set null)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};