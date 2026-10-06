<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * WAJIB dijalankan SETELAH KelasSeeder DAN UserSeeder (butuh keduanya
 * sudah ada).
 *
 * MASALAH: UserSeeder cuma kasih ROLE 'pkl.wali_kelas' (izin akses),
 * tapi gak pernah nyetel kolom `kelas.wali_kelas_id` (yang nunjuk
 * PERSIS kelas mana yang dia wali-in). Dua hal ini beda:
 * - role pkl.wali_kelas   = izin akses fitur approval PKL
 * - kelas.wali_kelas_id   = status wali kelas SEBENARNYA, dipakai
 *                           bareng oleh PKL (filter approval per
 *                           kelas) DAN LMS (User::isWaliKelas())
 *
 * Seeder ini nyambungin keduanya: tiap user yang punya role
 * pkl.wali_kelas otomatis di-assign jadi wali kelas di 1 baris
 * `kelas` yang wali_kelas_id-nya masih kosong (round-robin kalau ada
 * lebih dari 1 user wali_kelas).
 */
class SinkronWaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        $userWaliKelas = DB::table('users')
            ->join('user_role', 'user_role.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'user_role.role_id')
            ->join('modules', 'modules.id', '=', 'roles.module_id')
            ->where('modules.kode', 'pkl')
            ->where('roles.kode', 'wali_kelas')
            ->select('users.id', 'users.name')
            ->distinct()
            ->get();

        if ($userWaliKelas->isEmpty()) {
            $this->command?->warn('SinkronWaliKelasSeeder: tidak ada user dengan role pkl.wali_kelas, dilewati.');
            return;
        }

        $i = 0;
        foreach ($userWaliKelas as $user) {
            $kelasKosong = DB::table('kelas')->whereNull('wali_kelas_id')->orderBy('id')->value('id');

            if (! $kelasKosong) {
                $this->command?->warn("SinkronWaliKelasSeeder: sudah tidak ada kelas kosong buat {$user->name}, dilewati.");
                continue;
            }

            DB::table('kelas')->where('id', $kelasKosong)->update([
                'wali_kelas_id' => $user->id,
                'updated_at' => now(),
            ]);

            $i++;
        }

        $this->command?->info("SinkronWaliKelasSeeder: {$i} user di-assign jadi wali kelas.");
    }
}
