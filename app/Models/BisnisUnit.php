<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BisnisUnit extends Model
{
    protected $table = 'bisnis_units';

    protected $fillable = ['nama_bisnis_unit', 'kategori', 'deskripsi'];

    // Relasi: 1 Bisnis Unit menaungi banyak Perusahaan/PT
    public function perusahaans()
    {
        return $this->hasMany(Perusahaan::class, 'bisnis_unit_id');
    }
}