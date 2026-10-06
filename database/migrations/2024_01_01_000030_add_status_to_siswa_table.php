<?php
// database/migrations/2024_01_01_000030_add_status_to_siswa_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus', 'keluar'])
                ->default('aktif')
                ->after('kelas_id');
            $table->string('tahun_lulus', 9)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['status', 'tahun_lulus']);
        });
    }
};