<?php
// FILE: database/migrations/2024_01_01_000015_create_presensi_lms_table.php
// Dijalankan setelah pengampu_mapel dan siswa.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_lms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengampu_mapel_id')
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa']);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Satu siswa hanya 1 status presensi per pertemuan/tanggal
            $table->unique(['pengampu_mapel_id', 'siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_lms');
    }
};
