<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // 1. INPUT DATA MASTER KE TABEL `bisnis_units`
        // ==========================================
        
        // --- Kelompok Kategori: RETAIL ---
        $idSpbu       = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'SPBU', 'created_at' => now(), 'updated_at' => now()]);
        $idLpgPso     = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'LPG PSO', 'created_at' => now(), 'updated_at' => now()]);
        $idLpgNpso    = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'LPG NPSO', 'created_at' => now(), 'updated_at' => now()]);
        $idSppbe      = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'SPPBE', 'created_at' => now(), 'updated_at' => now()]);
        $idBbmRetail  = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'BBM RETAIL', 'created_at' => now(), 'updated_at' => now()]);
        $idInmar      = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'INMAR', 'created_at' => now(), 'updated_at' => now()]);

        // --- Kelompok Kategori: KOMERSIL ---
        $idTransLaut  = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'TRANSPORTASI LAUT', 'created_at' => now(), 'updated_at' => now()]);
        $idShipyard   = DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'SHIPYARD', 'created_at' => now(), 'updated_at' => now()]);
        $idGasIndustri= DB::table('bisnis_units')->insertGetId(['nama_bisnis_unit' => 'Gas industri (CNG)', 'created_at' => now(), 'updated_at' => now()]);


        // ==========================================
        // 2. INPUT DATA ANAK PERUSAHAAN KE TABEL `perusahaans`
        // ==========================================
        DB::table('perusahaans')->insert([
            // Under SPBU
            ['nama_pt' => 'PT SCK', 'bisnis_unit_id' => $idSpbu, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT MMS', 'bisnis_unit_id' => $idSpbu, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT IS', 'bisnis_unit_id' => $idSpbu, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT LEP', 'bisnis_unit_id' => $idSpbu, 'created_at' => now(), 'updated_at' => now()],

            // Under LPG PSO
            ['nama_pt' => 'PT SJN', 'bisnis_unit_id' => $idLpgPso, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT PJNP', 'bisnis_unit_id' => $idLpgPso, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT PJS', 'bisnis_unit_id' => $idLpgPso, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT LCPS', 'bisnis_unit_id' => $idLpgPso, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT BSN', 'bisnis_unit_id' => $idLpgPso, 'created_at' => now(), 'updated_at' => now()],

            // Under LPG NPSO
            ['nama_pt' => 'PT LBS', 'bisnis_unit_id' => $idLpgNpso, 'created_at' => now(), 'updated_at' => now()],

            // Under SPPBE
            ['nama_pt' => 'PT PKSP', 'bisnis_unit_id' => $idSppbe, 'created_at' => now(), 'updated_at' => now()],

            // Under BBM RETAIL
            ['nama_pt' => 'PT BKI', 'bisnis_unit_id' => $idBbmRetail, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT PJS ', 'bisnis_unit_id' => $idBbmRetail, 'created_at' => now(), 'updated_at' => now()], // Note: Ada spasi biar ga bentrok
            ['nama_pt' => 'PT ADHEL', 'bisnis_unit_id' => $idBbmRetail, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT TRP', 'bisnis_unit_id' => $idBbmRetail, 'created_at' => now(), 'updated_at' => now()],

            // Under INMAR
            ['nama_pt' => 'PT CNGM', 'bisnis_unit_id' => $idInmar, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT PIMS', 'bisnis_unit_id' => $idInmar, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT TEMARINDO', 'bisnis_unit_id' => $idInmar, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT SCP', 'bisnis_unit_id' => $idInmar, 'created_at' => now(), 'updated_at' => now()],

            // Under TRANSPORTASI LAUT
            ['nama_pt' => 'PT CPT', 'bisnis_unit_id' => $idTransLaut, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT MHM', 'bisnis_unit_id' => $idTransLaut, 'created_at' => now(), 'updated_at' => now()],

            // Under SHIPYARD
            ['nama_pt' => 'PT SBS', 'bisnis_unit_id' => $idShipyard, 'created_at' => now(), 'updated_at' => now()],

            // Under Gas Industri (CNG)
            ['nama_pt' => 'PT GVI', 'bisnis_unit_id' => $idGasIndustri, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pt' => 'PT MTG', 'bisnis_unit_id' => $idGasIndustri, 'created_at' => now(), 'updated_at' => now()],
        ]);


        // ==========================================
        // 3. INPUT AKUN DEFAULT KE TABEL `users`
        // ==========================================
        DB::table('users')->insert([
            [
                'nama_lengkap' => 'Admin Super',
                'username' => 'admin',
                'email' => 'admin@arsipin.com',
                'no_hp' => '081234567890',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'bisnis_unit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Imam (Pegawai Retail)',
                'username' => 'imam',
                'email' => 'imam.retail@arsipin.com',
                'no_hp' => '085711223344',
                'password' => Hash::make('password123'),
                'role' => 'pegawai-retail', // Sesuaikan dengan logika redirect kamu kemarin
                'bisnis_unit' => 'SPBU',      // Diarahkan memegang unit SPBU
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Pak Anas (Kepala BU SPBU)',
                'username' => 'anas',
                'email' => 'anas.kabu@arsipin.com',
                'no_hp' => '089988776655',
                'password' => Hash::make('password123'),
                'role' => 'kepala-bu',
                'bisnis_unit' => 'SPBU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}