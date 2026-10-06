<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $kajurId = DB::table('users')
            ->where('email', 'kajur@smk.sch.id')
            ->value('id');

        DB::table('jurusan')->insert([
            [
                'nama' => 'Perhotelan',
                'kode' => 'HTL',
                'kepala_jurusan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pengembangan Perangkat Lunak Dan Gim',
                'kode' => 'PPLG',
                'kepala_jurusan_id' => $kajurId,   // ← di sini datanya
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Akuntansi',
                'kode' => 'AK',
                'kepala_jurusan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
