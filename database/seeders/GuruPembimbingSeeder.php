<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruPembimbingSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            DB::table('guru_pembimbing')->insert([
                'nip' => fake()->unique()->numerify('19#############'),
                'nama' => fake()->name(),
                'no_hp' => fake()->numerify('08##########'),
                'email' => fake()->unique()->safeEmail(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
