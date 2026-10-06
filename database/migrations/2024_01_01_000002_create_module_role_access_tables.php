<?php
// FILE: database/migrations/2024_01_01_000002_create_module_role_access_tables.php
// Dijalankan setelah users. Ini INTI dari sistem multi-akses.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * - modules   : daftar sistem/fitur yang ada (PKL, spmb/SPMB, LMS).
     * - roles     : daftar peran, DI-SCOPE per modul. Jadi role "admin"
     *               di modul pkl adalah baris berbeda dengan "admin"
     *               di modul SPMB, meski nama kodenya sama.
     * - user_role : pivot many-to-many antara users dan roles.
     *               Satu user bisa punya banyak baris di sini
     *               -> bisa sekaligus jadi guru di LMS, wali_kelas
     *               di PKL, dan admin di SPMB.
     *
     * Untuk mengatur "guru ini bisa akses apa saja", admin/super admin
     * tinggal tambah/hapus baris di tabel user_role lewat UI —
     * tidak perlu ubah struktur database lagi.
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // pkl, SPMB, lms
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')
                ->constrained('modules')
                ->cascadeOnDelete();
            $table->string('kode', 30); // admin, wali_kelas, guru_bk, guru, siswa, dst
            $table->string('nama');
            $table->timestamps();

            $table->unique(['module_id', 'kode']);
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();

            // Audit trail: admin mana yang memberi akses ini & kapan
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });

        // ── Seed modul ──────────────────────────────────────────
        DB::table('modules')->insert([
            ['id' => 1, 'kode' => 'pkl', 'nama' => 'Praktik Kerja Lapangan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'kode' => 'spmb', 'nama' => 'Penerimaan Peserta Didik Baru (SPMB)', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'kode' => 'lms', 'nama' => 'Learning Management System', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Seed role per modul ─────────────────────────────────
        DB::table('roles')->insert([
            // PKL
            ['module_id' => 1, 'kode' => 'admin', 'nama' => 'Admin PKL', 'created_at' => now(), 'updated_at' => now()],
            ['module_id' => 1, 'kode' => 'wali_kelas', 'nama' => 'Wali Kelas', 'created_at' => now(), 'updated_at' => now()],
            ['module_id' => 1, 'kode' => 'guru_bk', 'nama' => 'Guru BK', 'created_at' => now(), 'updated_at' => now()],
            ['module_id' => 1, 'kode' => 'kesiswaan', 'nama' => 'Kesiswaan', 'created_at' => now(), 'updated_at' => now()],
            ['module_id' => 1, 'kode' => 'kepala_jurusan', 'nama' => 'Kepala Jurusan', 'created_at' => now(), 'updated_at' => now()],
            // SPMB / SPMB
            ['module_id' => 2, 'kode' => 'admin', 'nama' => 'Admin SPMB', 'created_at' => now(), 'updated_at' => now()],
            // LMS
            ['module_id' => 3, 'kode' => 'admin', 'nama' => 'Admin LMS', 'created_at' => now(), 'updated_at' => now()],
            ['module_id' => 3, 'kode' => 'guru', 'nama' => 'Guru', 'created_at' => now(), 'updated_at' => now()],
            ['module_id' => 3, 'kode' => 'siswa', 'nama' => 'Siswa', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Beri akses admin awal ke SEMUA modul sebagai admin ───
        $adminUserId = DB::table('users')->where('email', 'admin@smk.sch.id')->value('id');
        $adminRoleIds = DB::table('roles')->where('kode', 'admin')->pluck('id');

        foreach ($adminRoleIds as $roleId) {
            DB::table('user_role')->insert([
                'user_id' => $adminUserId,
                'role_id' => $roleId,
                'assigned_by' => $adminUserId,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('modules');
    }
};
