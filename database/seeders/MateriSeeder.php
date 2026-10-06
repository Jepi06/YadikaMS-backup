<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        $pengampuList = DB::table('pengampu_mapel')->pluck('id');

        foreach ($pengampuList as $pengampuId) {
            for ($pertemuan = 1; $pertemuan <= 3; $pertemuan++) {
                DB::table('materi')->insert([
                    'pengampu_mapel_id' => $pengampuId,
                    'judul' => "Pertemuan {$pertemuan}: " . fake()->sentence(4),
                    'deskripsi' => fake()->paragraph(),
                    'file_path' => null,
                    'urutan' => $pertemuan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
