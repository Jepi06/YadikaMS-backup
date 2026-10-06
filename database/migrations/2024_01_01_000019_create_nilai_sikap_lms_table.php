<?php
// FILE: database/migrations/2024_01_01_000019_create_nilai_sikap_lms_table.php
// Dijalankan setelah pengampu_mapel dan siswa.
//
// Nilai sikap = predikat kualitatif (bukan angka 0-100 seperti nilai
// tugas), sesuai standar rapor: Sangat Baik / Baik / Cukup / Kurang.
// Satu baris = 1 nilai sikap untuk 1 siswa, di 1 kelas-mengajar
// (pengampu_mapel) tertentu.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_sikap_lms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_mapel_id')
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->enum('predikat', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])
                ->default('Baik');

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique(['pengampu_mapel_id', 'siswa_id'], 'nilai_sikap_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_sikap_lms');
    }
};
