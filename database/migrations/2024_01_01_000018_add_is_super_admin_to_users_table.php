<?php
// FILE: database/migrations/2024_01_01_000018_add_is_super_admin_to_users_table.php
// Dijalankan setelah users + modules/roles/user_role (000001, 000002).
//
// is_super_admin TERPISAH dari sistem modules/roles/user_role.
// Role (pkl.admin, spmb.admin, lms.admin, dst) itu ngatur akses ke
// SATU sistem tertentu. is_super_admin ngasih akses ke "Panel Super
// Admin" yang guard-agnostic — bisa dibuka dari login PKL, SPMB,
// ATAU LMS manapun, buat ngatur akses semua user lintas modul
// (siapa jadi guru di mana, siapa bisa login ke sistem apa, dst).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('is_active');
        });

        // Akun admin awal (yang sudah dikasih role admin di semua modul
        // lewat migration 000002) otomatis jadi super admin juga.
        DB::table('users')
            ->where('email', 'admin@smk.sch.id')
            ->update(['is_super_admin' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
