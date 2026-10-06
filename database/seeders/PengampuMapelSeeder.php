<?php

namespace Database\Seeders;
use App\Support\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengampuMapelSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya user dengan role "guru" di modul lms yang boleh mengajar
        $guruLmsIds = DB::table('user_role')
            ->join('roles', 'roles.id', '=', 'user_role.role_id')
            ->join('modules', 'modules.id', '=', 'roles.module_id')
            ->where('modules.kode', 'lms')
            ->where('roles.kode', 'guru')
            ->pluck('user_role.user_id')
            ->unique()
            ->values();

        if ($guruLmsIds->isEmpty()) {
            return;
        }

        $kelasList = DB::table('kelas')->get(['id', 'jurusan_id']);
        $mapelUmumIds = DB::table('mata_pelajaran')->whereNull('jurusan_id')->pluck('id');

        $index = 0;

        foreach ($kelasList as $kelas) {
            $mapelProduktifIds = DB::table('mata_pelajaran')
                ->where('jurusan_id', $kelas->jurusan_id)
                ->pluck('id');

            // 1 mapel umum + 1 mapel produktif (jika ada) untuk tiap kelas
            $mapelUntukKelas = collect([$mapelUmumIds->random()]);
            if ($mapelProduktifIds->isNotEmpty()) {
                $mapelUntukKelas->push($mapelProduktifIds->random());
            }

            foreach ($mapelUntukKelas as $mapelId) {
                $guruId = $guruLmsIds[$index % $guruLmsIds->count()];

                DB::table('pengampu_mapel')->insert([
                    'guru_id' => $guruId,
                    'mata_pelajaran_id' => $mapelId,
                    'kelas_id' => $kelas->id,
                    'tahun_ajaran' => TahunAjaran::sekarang(),
                    'semester' => TahunAjaran::semesterSekarang(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $index++;
            }
        }
    }
}
