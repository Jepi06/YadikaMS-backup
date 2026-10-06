<?php
// FILE: database/migrations/2024_01_01_000016_create_sesi_presensi_lms_table.php
// Dijalankan setelah pengampu_mapel dan users.
//
// Sesi presensi barcode: guru pengampu "membuka" sesi di awal pertemuan,
// sistem generate token unik yang dirender jadi QR code. Siswa scan
// lewat akun LMS masing-masing untuk tercatat hadir otomatis.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sesi_presensi_lms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengampu_mapel_id')
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();

            $table->uuid('token')->unique();

            $table->date('tanggal');
            $table->timestamp('dibuka_at')->nullable();
            $table->timestamp('ditutup_at')->nullable();

            // guru yang membuka sesi (biasanya = pengampu_mapel.guru_id)
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            // satu pengampu_mapel hanya boleh 1 sesi per tanggal
            $table->unique(['pengampu_mapel_id', 'tanggal'], 'sesi_presensi_unik_per_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_presensi_lms');
    }
};
