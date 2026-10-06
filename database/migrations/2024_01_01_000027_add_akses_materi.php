<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->enum('mode_akses', ['bebas', 'berurutan', 'manual', 'tanggal'])
                ->default('bebas')->after('urutan');
            $table->boolean('dibuka_manual')->default(false)->after('mode_akses');
            $table->dateTime('buka_pada')->nullable()->after('dibuka_manual');
        });

        Schema::create('materi_selesai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->timestamp('selesai_at')->useCurrent();
            $table->timestamps();
            $table->unique(['materi_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_selesai');
        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumn(['mode_akses', 'dibuka_manual', 'buka_pada']);
        });
    }
};