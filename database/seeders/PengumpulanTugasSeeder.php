<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengumpulanTugasSeeder extends Seeder
{
    public function run(): void
    {
        $tugasList = DB::table('tugas')
            ->join('pengampu_mapel', 'pengampu_mapel.id', '=', 'tugas.pengampu_mapel_id')
            ->select('tugas.id as tugas_id', 'pengampu_mapel.kelas_id')
            ->get();

        foreach ($tugasList as $tugas) {
            $siswaIds = DB::table('siswa')->where('kelas_id', $tugas->kelas_id)->pluck('id');

            foreach ($siswaIds as $siswaId) {
                // 80% siswa mengumpulkan tugas, sisanya belum
                if (! fake()->boolean(80)) {
                    continue;
                }

                $sudahDinilai = fake()->boolean(70);

                DB::table('pengumpulan_tugas')->insert([
                    'tugas_id' => $tugas->tugas_id,
                    'siswa_id' => $siswaId,
                    'file_jawaban' => null,
                    'catatan_siswa' => null,
                    'nilai' => $sudahDinilai ? fake()->numberBetween(70, 100) : null,
                    'catatan_guru' => $sudahDinilai ? fake()->optional(0.4)->sentence() : null,
                    'dikumpulkan_at' => now(),
                    'dinilai_at' => $sudahDinilai ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
