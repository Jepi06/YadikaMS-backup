<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $jurusanList = DB::table('jurusan')->pluck('id', 'kode');

        $waliKelasId = DB::table('users')
            ->where('email', 'walas@smk.sch.id')
            ->value('id');

        $tingkatList = ['X', 'XI', 'XII'];

        foreach ($jurusanList as $kodeJurusan => $jurusanId) {

            foreach ($tingkatList as $tingkat) {

                for ($rombel = 1; $rombel <= 2; $rombel++) {

                    $wali = null;

                    if (
                        $kodeJurusan == 'PPLG'
                        && $tingkat == 'XI'
                    ) {
                        $wali = $waliKelasId;
                    }

                    DB::table('kelas')->insert([
                        'nama_kelas' => "{$tingkat} {$kodeJurusan} {$rombel}",
                        'tingkat' => $tingkat,
                        'jurusan_id' => $jurusanId,
                        'wali_kelas_id' => $wali,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
