<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Menghubungkan model ini ke nama entitas asli di ERD kamu
    protected $table = 'users';

    // Atribut lengkap yang boleh diisi secara massal sesuai ERD
    protected $fillable = [
        'nama_lengkap',
        'username',
        'email',
        'no_hp',
        'password',
        'role',
        'bisnis_unit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke tabel dokumens (Satu pengguna bisa mengupload banyak dokumen)
     */
    public function dokumens()
    {
        return $this->hasMany(Dokumen::class, 'user_id', 'id');
    }
}