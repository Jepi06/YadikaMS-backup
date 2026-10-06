<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        // Mapel umum: berlaku untuk semua jurusan (jurusan_id = NULL)
        $umum = [
            ['kode' => 'MTK', 'nama' => 'Matematika'],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Kewarganegaraan'],
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama'],
        ];

        foreach ($umum as $mapel) {
            DB::table('mata_pelajaran')->insert([
                'kode' => $mapel['kode'],
                'nama' => $mapel['nama'],
                'jurusan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Mapel produktif: khusus per jurusan
        $produktif = [
            'RPL' => [
                ['kode' => 'PPLG-PW', 'nama' => 'Pemrograman Web'],
                ['kode' => 'PPLG-BD', 'nama' => 'Basis Data'],
                ['kode' => 'PPLG-PG', 'nama' => 'Pemrograman Game'],
            ],
            'AKL' => [
                ['kode' => 'AK-AD', 'nama' => 'Akuntansi Dasar'],
                ['kode' => 'AK-PJ', 'nama' => 'Perpajakan'],
            ],
            'PHT' => [
                ['kode' => 'HTL-FO', 'nama' => 'Front Office'],
                ['kode' => 'HTL-TB', 'nama' => 'Tata Boga'],
            ],
        ];

        foreach ($produktif as $kodeJurusan => $daftarMapel) {
            $jurusanId = DB::table('jurusan')->where('kode', $kodeJurusan)->value('id');

            foreach ($daftarMapel as $mapel) {
                DB::table('mata_pelajaran')->insert([
                    'kode' => $mapel['kode'],
                    'nama' => $mapel['nama'],
                    'jurusan_id' => $jurusanId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
