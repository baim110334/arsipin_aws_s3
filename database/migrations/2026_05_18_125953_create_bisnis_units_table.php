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
        Schema::create('bisnis_units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bisnis_unit'); // 🌟 Menampung teks 'spbu', 'swalayan', dll
            $table->timestamps();
        }); // <-- Pastikan di sini rapi ya, kata "Levin" sudah dibuang total!
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bisnis_units');
    }
};
