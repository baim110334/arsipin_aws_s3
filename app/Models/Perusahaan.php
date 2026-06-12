<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    // 🌟 Sesuaikan dengan nama tabel asli di phpMyAdmin kamu!
    protected $table = 'perusahaans'; 

    protected $fillable = ['nama_pt', 'bisnis_unit_id'];

    public function bisnisUnit()
    {
        // Hubungkan ke kelas model BisnisUnit dengan foreign key bisnis_unit_id
        return $this->belongsTo(BisnisUnit::class, 'bisnis_unit_id');
    }
}