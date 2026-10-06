<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->boolean('is_kelompok')->default(false)->after('batas_waktu');
        });

        Schema::create('tugas_kelompok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->string('nama_kelompok')->nullable();
            $table->foreignId('ketua_siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['tugas_id', 'ketua_siswa_id'], 'ketua_unik_per_tugas');
        });

        Schema::create('tugas_kelompok_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_kelompok_id')->constrained('tugas_kelompok')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['tugas_kelompok_id', 'siswa_id'], 'anggota_unik');
        });

        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->string('link_jawaban')->nullable()->after('file_jawaban');
            $table->foreignId('tugas_kelompok_id')->nullable()->after('siswa_id')
                ->constrained('tugas_kelompok')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tugas_kelompok_id');
            $table->dropColumn('link_jawaban');
        });
        Schema::dropIfExists('tugas_kelompok_anggota');
        Schema::dropIfExists('tugas_kelompok');
        Schema::table('tugas', fn(Blueprint $t) => $t->dropColumn('is_kelompok'));
    }
};