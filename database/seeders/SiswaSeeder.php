<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = DB::table('kelas')->get(['id', 'tingkat']);
        $nisCounter = 1;
        $prefixAngkatan = [
            'X' => '24',
            'XI' => '23',
            'XII' => '22',
        ];

        foreach ($kelasList as $kelas) {
            $prefix = $prefixAngkatan[$kelas->tingkat] ?? '00';

            for ($i = 1; $i <= 8; $i++) {
                $jk = fake()->randomElement(['L', 'P']);

                DB::table('siswa')->insert([
                    'nis' => $prefix . str_pad((string) $nisCounter, 4, '0', STR_PAD_LEFT),
                    'nama' => fake()->name($jk === 'L' ? 'male' : 'female'),
                    'jenis_kelamin' => $jk,
                    'alamat' => fake()->address(),
                    'no_hp' => fake()->numerify('08##########'),
                    'kelas_id' => $kelas->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $nisCounter++;
            }
        }
    }
}
