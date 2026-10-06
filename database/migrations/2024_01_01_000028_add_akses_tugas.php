<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->enum('mode_buka', ['bebas', 'manual', 'tanggal'])
                ->default('bebas')->after('batas_waktu');
            $table->boolean('dibuka_manual')->default(false)->after('mode_buka');
            $table->dateTime('mulai_pada')->nullable()->after('dibuka_manual');
            $table->boolean('ditutup_manual')->default(false)->after('mulai_pada');
        });
    }

    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn(['mode_buka', 'dibuka_manual', 'mulai_pada', 'ditutup_manual']);
        });
    }
};