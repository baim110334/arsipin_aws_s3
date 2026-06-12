<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'nama_user',
        'role_user',
        'bisnis_unit',
        'aksi',
        'deskripsi'
    ];

    // Relasi balik ke data User (jika admin ingin klik profil user dari timeline)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}