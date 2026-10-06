<?php
// FILE: database/migrations/2024_01_01_000011_create_pengampu_mapel_table.php
// Dijalankan setelah users, mata_pelajaran, dan kelas.
//
// Tabel ini adalah "kelas mengajar": guru X mengajar mapel Y
// di kelas Z pada tahun ajaran & semester tertentu.
// Ini jadi titik acuan (anchor) untuk materi, tugas, dan presensi.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengampu_mapel', function (Blueprint $table) {
            $table->id();

            // guru_id -> users dengan role "guru" di modul lms
            // (dicek lewat tabel user_role, bukan kolom enum)
            $table->foreignId('guru_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajaran')
                ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            $table->string('tahun_ajaran', 9); // contoh: 2024/2025
            $table->enum('semester', ['Ganjil', 'Genap']);

            $table->timestamps();

            $table->unique(
                ['guru_id', 'mata_pelajaran_id', 'kelas_id', 'tahun_ajaran', 'semester'],
                'pengampu_mapel_unik'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengampu_mapel');
    }
};
