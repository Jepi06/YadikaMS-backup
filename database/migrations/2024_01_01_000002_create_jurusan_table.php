<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->foreignId('kepala_jurusan_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->integer('kuota')->default(0);
            $table->timestamps();
        });

        // Data seed dipindah ke JurusanSeeder.php
        // supaya kepala_jurusan_id bisa otomatis link ke user kajur
        // yang sudah dibuat lebih dulu oleh UserSeeder.
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusan');
    }
};
