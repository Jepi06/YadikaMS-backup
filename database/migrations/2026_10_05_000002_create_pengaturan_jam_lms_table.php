<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_jam_lms', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('menit_jp')->default(45);
            $table->unsignedSmallInteger('menit_istirahat')->default(20);
            $table->timestamps();
        });

        DB::table('pengaturan_jam_lms')->insert([
            'menit_jp' => 45, 'menit_istirahat' => 20,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_jam_lms');
    }
};