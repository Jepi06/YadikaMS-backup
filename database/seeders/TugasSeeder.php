<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TugasSeeder extends Seeder
{
    public function run(): void
    {
        $pengampuList = DB::table('pengampu_mapel')->pluck('id');

        foreach ($pengampuList as $pengampuId) {
            for ($ke = 1; $ke <= 2; $ke++) {
                DB::table('tugas')->insert([
                    'pengampu_mapel_id' => $pengampuId,
                    'judul' => "Tugas {$ke}: " . fake()->sentence(3),
                    'deskripsi' => fake()->paragraph(),
                    'file_lampiran' => null,
                    'batas_waktu' => Carbon::create(2024, 8, 1)->addWeeks($ke * 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
