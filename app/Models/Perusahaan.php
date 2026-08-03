<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaans'; 

    protected $fillable = ['nama_pt', 'bisnis_unit_id'];

    public function bisnisUnit()
    {
        return $this->belongsTo(BisnisUnit::class, 'bisnis_unit_id');
    }
}