<?php

// FILE: database/migrations/2024_01_01_000001_create_users_table.php
// Tabel ini harus dijalankan PERTAMA karena semua tabel lain FK ke sini.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel users kini HANYA menyimpan identitas & kredensial.
     *
     * Kolom role_pkl / role_ppdb / role_lms (enum tunggal) DIHAPUS,
     * karena satu user perlu bisa punya LEBIH DARI SATU akses
     * sekaligus (mis. guru yang sekaligus wali_kelas di PKL,
     * guru di LMS, dan admin di PPDB) — enum tunggal tidak cukup.
     *
     * Semua akses modul + role sekarang disimpan lewat tabel
     * modules, roles, dan pivot user_role pada migrasi berikutnya,
     * supaya admin/super admin bisa mengatur akses seorang user
     * secara dinamis dari UI, tanpa perlu migrasi ulang.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->string('avatar')->nullable();              // ← tambah
            $table->boolean('must_change_password')->default(false); // ← tambah
            $table->rememberToken();
            $table->timestamps();
        });

        // ── Seed: akun super admin awal ─────────────────────────
        // Role akses (pkl/ppdb/lms) diberikan di migrasi 000002,
        // setelah tabel modules & roles tersedia.
        DB::table('users')->insert([
            [
                'name' => 'Administrator',
                'email' => 'admin@smk.sch.id',
                'password' => Hash::make('admin123'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
