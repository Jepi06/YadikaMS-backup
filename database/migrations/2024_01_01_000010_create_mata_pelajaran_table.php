<?php
// FILE: database/migrations/2024_01_01_000010_create_mata_pelajaran_table.php
// Dijalankan setelah jurusan. Master data mata pelajaran untuk LMS.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');

            // NULL     = mapel umum, berlaku untuk semua jurusan (mis. Matematika)
            // terisi   = mapel produktif khusus jurusan tsb (mis. Pemrograman Web -> RPL)
            $table->foreignId('jurusan_id')
                ->nullable()
                ->constrained('jurusan')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
