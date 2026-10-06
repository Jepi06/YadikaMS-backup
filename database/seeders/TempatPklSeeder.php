<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TempatPklSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['nama_tempat' => 'PT Teknologi Nusantara', 'bidang_usaha' => 'Software House'],
            ['nama_tempat' => 'CV Digital Kreasi', 'bidang_usaha' => 'Digital Agency'],
            ['nama_tempat' => 'Kantor Akuntan Publik Wijaya & Rekan', 'bidang_usaha' => 'Jasa Akuntansi'],
            ['nama_tempat' => 'Hotel Grand Harmoni', 'bidang_usaha' => 'Perhotelan'],
            ['nama_tempat' => 'Hotel Permata Biru', 'bidang_usaha' => 'Perhotelan'],
            ['nama_tempat' => 'PT Solusi Data Indonesia', 'bidang_usaha' => 'IT Consultant'],
            ['nama_tempat' => 'KPP Pratama Bandung', 'bidang_usaha' => 'Perpajakan'],
            ['nama_tempat' => 'Restoran Nusantara Rasa', 'bidang_usaha' => 'Kuliner & Perhotelan'],
        ];

        foreach ($daftar as $item) {
            DB::table('tempat_pkl')->insert([
                'nama_tempat' => $item['nama_tempat'],
                'alamat' => fake()->address(),
                'bidang_usaha' => $item['bidang_usaha'],
                'nama_kontak' => fake()->name(),
                'no_telp' => fake()->numerify('021#######'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
