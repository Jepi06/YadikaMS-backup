<?php
// FILE: database/migrations/2024_01_01_000007_create_guru_pembimbing_table.php
// Tidak ada FK ke tabel lain. Ini adalah master data pembimbing lapangan,
// TIDAK harus punya akun login di sistem.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_pembimbing', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique()->nullable();
            $table->string('nama');
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_pembimbing');
    }
};
