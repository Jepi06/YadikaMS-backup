<?php
// FILE: database/migrations/2024_01_01_000013_create_tugas_table.php
// Dijalankan setelah pengampu_mapel.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengampu_mapel_id')
                ->constrained('pengampu_mapel')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->timestamp('batas_waktu');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
