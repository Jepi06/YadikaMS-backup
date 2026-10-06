<?php
// FILE: database/migrations/2024_01_01_000006_create_tempat_pkl_table.php
// Tidak ada FK ke tabel lain, bisa berdiri sendiri.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tempat_pkl', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tempat');
            $table->text('alamat');
            $table->string('bidang_usaha')->nullable();
            $table->string('nama_kontak')->nullable();
            $table->string('no_telp', 20)->nullable();
            // BARU: batas maksimal siswa yang bisa ditempatkan di tempat ini.
            // null artinya tidak ada batas (unlimited).
            $table->integer('kuota_maksimal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tempat_pkl');
    }
};
