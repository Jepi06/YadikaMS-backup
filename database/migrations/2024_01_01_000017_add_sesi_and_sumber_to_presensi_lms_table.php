<?php
// FILE: database/migrations/2024_01_01_000017_add_sesi_and_sumber_to_presensi_lms_table.php
// Dijalankan setelah sesi_presensi_lms dibuat.
//
// Menambahkan jejak asal data presensi: hasil scan barcode (terhubung
// ke sesi_presensi_lms) atau input manual oleh guru.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('presensi_lms', function (Blueprint $table) {
            $table->foreignId('sesi_presensi_id')
                ->nullable()
                ->after('siswa_id')
                ->constrained('sesi_presensi_lms')
                ->nullOnDelete();

            $table->enum('sumber', ['manual', 'barcode'])
                ->default('manual')
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('presensi_lms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sesi_presensi_id');
            $table->dropColumn('sumber');
        });
    }
};
