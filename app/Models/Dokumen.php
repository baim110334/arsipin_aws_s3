<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumens';

    // 🌟 SUNTIKKAN DUA KOLOM BARU INI KE DALAM FILLABLE:
   protected $fillable = [
    'user_id', 'bisnis_unit', 'perusahaan', 'nama_dokumen', 'no_dokumen', 
    'tipe_keuangan', 'bulan_buku', 'tahun_buku', 'jilid_buku', 'keterangan', 
    'file_name_original', 's3_object_key', 'file_size', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}