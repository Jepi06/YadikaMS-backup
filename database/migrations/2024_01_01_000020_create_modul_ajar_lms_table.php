<?php
// FILE: database/migrations/2024_01_01_000020_create_modul_ajar_lms_table.php
// Dijalankan setelah pengampu_mapel.
//
// Modul Ajar = dokumen ADMINISTRATIF guru (RPP/modul ajar resmi),
// BEDA dari `materi` (bahan belajar yang dilihat siswa). Modul Ajar
// TIDAK ditampilkan ke siswa sama sekali — cuma bisa diakses guru
// pemiliknya dan Panel Super Admin (arsip admin/kepala sekolah).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_ajar_lms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_mapel_id')
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_ajar_lms');
    }
};
