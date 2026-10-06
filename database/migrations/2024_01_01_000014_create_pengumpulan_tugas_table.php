<?php
// FILE: database/migrations/2024_01_01_000014_create_pengumpulan_tugas_table.php
// Dijalankan setelah tugas dan siswa.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')
                ->constrained('tugas')
                ->cascadeOnDelete();
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->string('file_jawaban')->nullable();
            $table->text('catatan_siswa')->nullable();

            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('catatan_guru')->nullable();

            $table->timestamp('dikumpulkan_at')->nullable();
            $table->timestamp('dinilai_at')->nullable();

            $table->timestamps();

            // Satu siswa hanya boleh punya 1 baris pengumpulan per tugas
            $table->unique(['tugas_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
