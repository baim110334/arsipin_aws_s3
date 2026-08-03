<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bisnis_units', function (Blueprint $table) {
        $table->id();
        $table->string('nama_bisnis_unit');
        $table->enum('kategori', ['retail', 'commercial'])->default('retail');
        $table->text('deskripsi')->nullable(); // 👈 Tambahkan kolom ini, Baim!
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bisnis_units');
    }
};