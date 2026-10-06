<?php
// FILE: database/migrations/2024_01_01_000022_create_nilai_ujian_lms_table.php
// Dijalankan setelah pengampu_mapel dan siswa.
//
// Nilai STS (Sumatif Tengah Semester) & SAS (Sumatif Akhir Semester),
// angka 0-100, per siswa per kelas-mengajar.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ujian_lms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_mapel_id')
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->decimal('nilai_sts', 5, 2)->nullable();
            $table->decimal('nilai_sas', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['pengampu_mapel_id', 'siswa_id'], 'nilai_ujian_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_ujian_lms');
    }
};
