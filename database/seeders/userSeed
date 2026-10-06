<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Contoh data guru/staff. Perhatikan array 'roles' pada beberapa
     * user berisi LEBIH DARI SATU kombinasi module+role — ini
     * menunjukkan bagaimana satu user bisa akses PKL, LMS, dan SPMB
     * sekaligus lewat tabel pivot user_role.
     *
     * Semua password default: "password"
     *
     * PENTING: jalankan SiswaSeeder DULU sebelum seeder ini, supaya ada
     * baris `siswa` (user_id masih null) yang bisa dihubungkan ke user
     * yang punya role lms.siswa di bawah.
     */
    public function run(): void
    {
        $superAdminId = DB::table('users')->where('email', 'admin@smk.sch.id')->value('id');

        $daftarUser = [
            [
                'name' => 'Hubin SMK',
                'email' => 'hubin@smk.sch.id',
                'roles' => [
                    ['pkl', 'admin'],
                    ['lms', 'admin'],
                    ['spmb', 'admin'],
                ],
            ],
            [
                'name' => 'Guru BK',
                'email' => 'bk@smk.sch.id',
                'roles' => [
                    ['pkl', 'guru_bk'],
                ],
            ],
            [
                'name' => 'Kajur PPLG',
                'email' => 'kajur@smk.sch.id',
                'roles' => [
                    ['pkl', 'kepala_jurusan'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Wali Kelas PPLG',
                'email' => 'walas@smk.sch.id',
                'roles' => [
                    ['pkl', 'wali_kelas'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Budi Santoso, S.Kom',
                'email' => 'budi.santoso@smk.sch.id',
                'roles' => [
                    ['pkl', 'wali_kelas'],
                    ['lms', 'wali_kelas'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Siti Aminah, S.Pd',
                'email' => 'siti.aminah@smk.sch.id',
                'roles' => [
                    ['pkl', 'guru_bk'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Ahmad Fauzi, S.Pd',
                'email' => 'ahmad.fauzi@smk.sch.id',
                'roles' => [
                    ['pkl', 'kesiswaan'],
                ],
            ],
            [
                'name' => 'Dedi Kurniawan, S.T',
                'email' => 'dedi.kurniawan@smk.sch.id',
                'roles' => [
                    ['pkl', 'kepala_jurusan'],
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Rina Wulandari, S.E',
                'email' => 'rina.wulandari@smk.sch.id',
                'roles' => [
                    ['spmb', 'admin'],
                ],
            ],
            [
                'name' => 'Andi Prasetyo, S.Kom',
                'email' => 'andi.prasetyo@smk.sch.id',
                'roles' => [
                    ['lms', 'guru'],
                    ['pkl', 'wali_kelas'],
                ],
            ],
            [
                'name' => 'Maya Sari, S.Pd',
                'email' => 'maya.sari@smk.sch.id',
                'roles' => [
                    ['lms', 'guru'],
                ],
            ],
            [
                'name' => 'Yusuf Hidayat, S.Pd',
                'email' => 'yusuf.hidayat@smk.sch.id',
                'roles' => [
                    ['lms', 'guru'],
                    ['pkl', 'wali_kelas'],
                ],
            ],
            [
                'name' => 'Farah Nabila, S.Pd',
                'email' => 'farah.nabila@smk.sch.id',
                'roles' => [
                    ['spmb', 'admin'],
                    ['lms', 'admin'],
                ],
            ],
            [
                'name' => 'Admin2',
                'email' => 'admin2@smk.sch.id',
                'roles' => [
                    ['pkl', 'admin'],
                    ['lms', 'admin'],
                    ['spmb', 'admin'],
                ],
            ],
            [
                'name' => 'Asep Maulana',
                'email' => 'asep.maulana@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Rina Febriani',
                'email' => ' ',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Dimas Saputra',
                'email' => 'dimas.saputra@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Muhammad Rizki',
                'email' => 'muhammad.rizki@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Nabila Putri',
                'email' => 'nabila.putri@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Fajar Hidayat',
                'email' => 'fajar.hidayat@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Putri Ayu',
                'email' => 'putri.ayu@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Yoga Pratama',
                'email' => 'yoga.pratama@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
            [
                'name' => 'Intan Permata',
                'email' => 'intan.permata@smk.sch.id',
                'roles' => [
                    ['lms', 'siswa'],
                ],
            ],
        ];

        foreach ($daftarUser as $data) {
            $userId = DB::table('users')->insertGetId([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $isSiswaLms = false;

            foreach ($data['roles'] as [$moduleKode, $roleKode]) {
                $roleId = DB::table('roles')
                    ->join('modules', 'modules.id', '=', 'roles.module_id')
                    ->where('modules.kode', $moduleKode)
                    ->where('roles.kode', $roleKode)
                    ->value('roles.id');

                if (!$roleId) {
                    continue;
                }

                DB::table('user_role')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'assigned_by' => $superAdminId,
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($moduleKode === 'lms' && $roleKode === 'siswa') {
                    $isSiswaLms = true;
                }
            }

            // ── Hubungkan akun ke tabel `siswa` ───────────────────────
            // SiswaSeeder membuat baris siswa TANPA user_id (data nama
            // masih fiktif dari faker). Di sini kita "adopsi" satu baris
            // siswa yang belum punya akun (user_id masih null) supaya
            // user LMS ini punya data siswa untuk login & dashboard.
            //
            // CATATAN: nama di tabel `siswa` yang ke-attach TIDAK akan
            // otomatis sama dengan $data['name'] di atas (karena datanya
            // masih hasil faker dari SiswaSeeder). Kalau mau nama-nya
            // konsisten, timpa juga kolom `nama` siswa dengan nama user.
            if ($isSiswaLms) {
                $siswaId = DB::table('siswa')->whereNull('user_id')->orderBy('id')->value('id');

                if ($siswaId) {
                    DB::table('siswa')->where('id', $siswaId)->update([
                        'user_id' => $userId,
                        'nama' => $data['name'], // samakan nama siswa dgn nama akun
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
