<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Super (Baim)
        User::create([
            'nama_lengkap' => 'Maulana Malik Ibrahim',
            'username'     => 'admin',
            'email'        => 'admin@arsipin.com',
            'no_hp'        => '081234567890',
            'password'     => Hash::make('password'), // 🔑 Password-nya: password
            'role'         => 'admin',
            'bisnis_unit'  => 'GLOBAL',
            'status_aktif' => 'approved',
        ]);

        // 2. Akun Kepala BU (Pak Anas)
        User::create([
            'nama_lengkap' => 'Pak Anas (Kepala BU SPBU)',
            'username'     => 'anas',
            'email'        => 'anas@arsipin.com',
            'no_hp'        => '081234567891',
            'password'     => Hash::make('password'),
            'role'         => 'kepala_bu',
            'bisnis_unit'  => 'SPBU',
            'status_aktif' => 'approved',
        ]);

        // 3. Akun Pegawai Retail (Imam)
        User::create([
            'nama_lengkap' => 'Imam (Pegawai Retail)',
            'username'     => 'imam',
            'email'        => 'imam@arsipin.com',
            'no_hp'        => '081234567892',
            'password'     => Hash::make('password'),
            'role'         => 'pegawai-retail',
            'bisnis_unit'  => 'SPBU',
            'status_aktif' => 'approved',
        ]);
    }
}