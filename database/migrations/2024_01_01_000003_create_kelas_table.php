<?php
// FILE: database/migrations/2024_01_01_000004_create_kelas_table.php
// Dijalankan setelah users dan jurusan.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas'); // contoh: "XI RPL 1"
            $table->enum('tingkat', ['X', 'XI', 'XII']);

            // FK ke jurusan (1 tabel, tidak lagi duplikat)
            $table->foreignId('jurusan_id')
                ->constrained('jurusan')
                ->cascadeOnDelete();

            // Wali kelas -> user yang punya role "wali_kelas" di modul pkl
            // (dicek lewat tabel user_role, bukan kolom enum lagi)
            // nullable: kelas boleh belum punya wali kelas
            $table->foreignId('wali_kelas_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
