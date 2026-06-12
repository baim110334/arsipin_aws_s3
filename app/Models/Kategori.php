<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table){
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });
    }
}
