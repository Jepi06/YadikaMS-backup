<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajaran_lms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengampu_mapel_id')
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('hari');
            $table->unsignedTinyInteger('jp_mulai')->nullable();
            $table->unsignedTinyInteger('jumlah_jp')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 50)->nullable();
            $table->timestamps();

            $table->index(['hari', 'jam_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran_lms');
    }
};