<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendaftarSeeder extends Seeder
{
    public function run(): void
    {
        $jurusanIds = DB::table('jurusan')->pluck('id');

        $adminspmbId = DB::table('user_role')
            ->join('roles', 'roles.id', '=', 'user_role.role_id')
            ->join('modules', 'modules.id', '=', 'roles.module_id')
            ->where('modules.kode', 'spmb')
            ->where('roles.kode', 'admin')
            ->value('user_role.user_id');

        for ($i = 1; $i <= 40; $i++) {
            // 70% daftar mandiri lewat form publik tanpa login,
            // 30% diinput manual oleh admin spmb
            $daftarMandiri = fake()->boolean(70);

            $nominal = fake()->randomElement([0, 500000, 1500000, 2500000, 3000000, 3500000]);

            if ($nominal >= 2500000) {
                $status = 'diterima';
                $sudahDiproses = true;
            } elseif (fake()->boolean(30)) {
                $status = 'ditolak';
                $sudahDiproses = true;
            } else {
                $status = 'pending';
                $sudahDiproses = false;
            }

            DB::table('pendaftar')->insert([
                'no_pendaftaran' => 'spmb-2025-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'nama_lengkap' => fake()->name(),
                'jenis_kelamin' => fake()->randomElement(['L', 'P']),
                'alamat' => fake()->address(),
                'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']),
                'nama_orang_tua' => fake()->name(),
                'asal_sekolah' => fake()->randomElement([
                    'SMPN 1 Bandung',
                    'SMPN 2 Bandung',
                    'SMP Kartika',
                    'MTs Al-Ikhlas',
                    'SMPN 5 Bandung',
                ]),
                'no_hp' => fake()->numerify('08##########'),
                'jurusan_id' => $jurusanIds->random(),
                'status' => $status,
                'nominal_pembayaran' => $nominal,
                'catatan_admin' => $status === 'ditolak'
                    ? 'Berkas tidak lengkap / pembayaran belum memenuhi syarat.'
                    : null,
                // NULL = belum diproses (masih pending)
                'processed_by' => $sudahDiproses ? $adminspmbId : null,
                'processed_at' => $sudahDiproses ? now() : null,
                // NULL = pendaftar isi sendiri lewat form publik (tanpa login)
                // terisi = diinput manual oleh admin spmb
                'created_by' => $daftarMandiri ? null : $adminspmbId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
