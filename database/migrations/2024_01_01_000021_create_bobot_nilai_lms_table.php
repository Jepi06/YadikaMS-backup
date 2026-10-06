<?php
// FILE: database/migrations/2024_01_01_000021_create_bobot_nilai_lms_table.php
// Dijalankan setelah pengampu_mapel.
//
// Bobot penilaian (%) buat hitung Nilai Akhir, diatur PER pengampu_mapel
// (jadi guru bisa beda bobot antar kelas/mapel kalau perlu). Default
// 40/20/20/20 kalau guru belum pernah atur.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bobot_nilai_lms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_mapel_id')
                ->unique()
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('bobot_tugas')->default(40);
            $table->unsignedTinyInteger('bobot_sts')->default(20);
            $table->unsignedTinyInteger('bobot_sas')->default(20);
            $table->unsignedTinyInteger('bobot_sikap')->default(20);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_nilai_lms');
    }
};
