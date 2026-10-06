<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * WAJIB dijalankan SETELAH PengampuMapelSeeder (dan sebelum
 * PengumpulanTugasSeeder / PresensiLmsSeeder biar data itu ikut
 * konsisten dengan kelas yang sudah diselaraskan).
 *
 * MASALAH: SiswaSeeder & UserSeeder gak tau kelas mana yang beneran
 * "diajar" (punya baris di pengampu_mapel) — itu baru ditentukan
 * belakangan di PengampuMapelSeeder. Akibatnya akun demo siswa LMS
 * (Asep, Rina, dst) bisa aja "kesasar" ke kelas yang sama sekali gak
 * ada guru pengampunya. Waktu testing scan QR presensi, hasilnya
 * selalu "QR ini bukan untuk kelas Anda" — bukan karena fitur
 * presensinya salah, tapi karena kelas si siswa & kelas yang diajar
 * guru itu emang beda.
 *
 * Seeder ini mindahin SEMUA siswa yang punya akun LMS (siswa.user_id
 * != null) ke salah satu kelas yang beneran diajar, disebar merata
 * (round-robin) kalau ada lebih dari 1 kelas yang diajar.
 */
class SinkronKelasSiswaLmsSeeder extends Seeder
{
    public function run(): void
    {
        $kelasDiajar = DB::table('pengampu_mapel')
            ->distinct()
            ->pluck('kelas_id')
            ->values();

        if ($kelasDiajar->isEmpty()) {
            $this->command?->warn('SinkronKelasSiswaLmsSeeder: belum ada data di pengampu_mapel, dilewati.');
            return;
        }

        $siswaLms = DB::table('siswa')->whereNotNull('user_id')->get(['id', 'kelas_id', 'nama']);

        $i = 0;
        foreach ($siswaLms as $s) {
            if (! $kelasDiajar->contains($s->kelas_id)) {
                $kelasBaru = $kelasDiajar[$i % $kelasDiajar->count()];

                DB::table('siswa')->where('id', $s->id)->update([
                    'kelas_id' => $kelasBaru,
                    'updated_at' => now(),
                ]);

                $i++;
            }
        }

        $this->command?->info("SinkronKelasSiswaLmsSeeder: {$i} siswa ber-akun LMS dipindah ke kelas yang diajar.");
    }
}
