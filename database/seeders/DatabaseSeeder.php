<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan HARUS seperti ini karena mengikuti dependensi FK
     * (mis. Siswa butuh Kelas, PenempatanPkl butuh Siswa+TempatPkl+
     * GuruPembimbing, dst).
     *
     * Catatan: user admin, modules, roles, dan jurusan SUDAH di-seed
     * lewat migrasi (2024_01_01_000001, 000002, 000003) sebagai
     * data dasar wajib. Seeder di sini menambahkan data dummy
     * untuk kebutuhan development/testing.
     *
     * PENTING: UserSeeder DIPINDAH ke setelah KelasSeeder + SiswaSeeder.
     * UserSeeder butuh baris `siswa` yang sudah ada (user_id masih null)
     * untuk "diadopsi" ke akun-akun dengan role lms.siswa — kalau
     * UserSeeder jalan duluan, tabel siswa masih kosong dan linknya
     * gagal semua (itu penyebab error "Akun Anda belum terhubung ke
     * data siswa").
     */
    public function run(): void
    {
        $this->call([
            jurusanSeeder::class,
            KelasSeeder::class,          // rombel per jurusan & tingkat
                  // siswa per kelas — harus SEBELUM UserSeeder
            UserSeeder::class,  
            SinkronWaliKelasSeeder::class,          // guru/staff + kombinasi role lintas modul + link ke siswa
            TempatPklSeeder::class,      // perusahaan/instansi mitra PKL
            GuruPembimbingSeeder::class, // pembimbing lapangan (master data)
            PenempatanPklSeeder::class,  // penempatan PKL + alur approval
            PendaftarSeeder::class,      // pendaftar SPMB (mandiri & input admin)
            MataPelajaranSeeder::class,  // master mapel untuk LMS
            PengampuMapelSeeder::class,
            SinkronKelasSiswaLmsSeeder::class,  // guru mengajar mapel di kelas — butuh UserSeeder
            MateriSeeder::class,
            TugasSeeder::class,
            PengumpulanTugasSeeder::class,
            PresensiLmsSeeder::class,
        ]);
    }
}
// nomer 6 kalkulus lanjut

