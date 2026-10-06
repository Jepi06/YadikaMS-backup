<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresensiLmsSeeder extends Seeder
{
    public function run(): void
    {
        $pengampuList = DB::table('pengampu_mapel')->get(['id', 'kelas_id']);

        $tanggalPertemuan = [
            Carbon::create(2024, 8, 5),
            Carbon::create(2024, 8, 12),
            Carbon::create(2024, 8, 19),
        ];

        foreach ($pengampuList as $pengampu) {
            $siswaIds = DB::table('siswa')->where('kelas_id', $pengampu->kelas_id)->pluck('id');

            foreach ($tanggalPertemuan as $tanggal) {
                foreach ($siswaIds as $siswaId) {
                    DB::table('presensi_lms')->insert([
                        'pengampu_mapel_id' => $pengampu->id,
                        'siswa_id' => $siswaId,
                        'tanggal' => $tanggal,
                        'status' => fake()->randomElement([
                            'Hadir', 'Hadir', 'Hadir', 'Hadir', 'Izin', 'Sakit', 'Alpa',
                        ]),
                        'keterangan' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
