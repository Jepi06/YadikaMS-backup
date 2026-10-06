<?php
// FILE: database/migrations/2024_01_01_000024_create_wali_kelas_periode_lms_table.php
// Dijalankan setelah kelas dan users.
//
// MASALAH: kolom `kelas.wali_kelas_id` itu SATU nilai global, gak ada
// dimensi tahun ajaran — dipakai bareng oleh sistem PKL (approval
// per kelas). Kalau kolom itu diupdate buat ganti wali kelas tahun
// depan, histori tahun-tahun sebelumnya (dipakai LMS buat rekap nilai
// wali kelas) ikut "berubah" jadi salah — retroaktif.
//
// SOLUSI (khusus sisi LMS, TIDAK mengubah/mengganggu kelas.wali_kelas_id
// yang masih dipakai PKL apa adanya): tabel baru ini nyimpen SIAPA wali
// kelas suatu kelas, DI SETIAP tahun_ajaran+semester secara terpisah.
// Assignment lama gak pernah ketimpa, tinggal tambah baris baru tiap
// tahun ajaran baru.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Support\TahunAjaran;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wali_kelas_periode_lms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('tahun_ajaran', 9);
            $table->enum('semester', ['Ganjil', 'Genap']);

            $table->timestamps();

            // 1 kelas cuma boleh punya 1 wali kelas per periode
            $table->unique(['kelas_id', 'tahun_ajaran', 'semester'], 'wali_kelas_periode_unik');
        });

        // ── BACKFILL ──────────────────────────────────────────
        // kelas.wali_kelas_id yang sekarang ada dianggap berlaku buat
        // tahun ajaran AKTIF SAAT INI (titik cutoff paling masuk akal,
        // karena sebelum ini data wali kelas emang belum dipisah per
        // tahun ajaran sama sekali).
        $tahunAjaran = TahunAjaran::sekarang();
        $semester = TahunAjaran::semesterSekarang();

        $kelasDenganWali = DB::table('kelas')->whereNotNull('wali_kelas_id')->get(['id', 'wali_kelas_id']);

        foreach ($kelasDenganWali as $kelas) {
            DB::table('wali_kelas_periode_lms')->insert([
                'kelas_id' => $kelas->id,
                'user_id' => $kelas->wali_kelas_id,
                'tahun_ajaran' => $tahunAjaran,
                'semester' => $semester,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_kelas_periode_lms');
    }
};
