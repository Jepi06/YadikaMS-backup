<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin kalau belum ada
        $adminId = DB::table('users')->where('email', 'admin@smk.sch.id')->value('id');

        if (!$adminId) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Super Admin',
                'email' => 'admin@smk.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign role admin (sesuaikan kode module & role-nya)
        $roleId = DB::table('roles')
            ->join('modules', 'modules.id', '=', 'roles.module_id')
            ->where('modules.kode', 'core')       // ganti sesuai kode module admin kamu
            ->where('roles.kode', 'superadmin')   // ganti sesuai kode role admin kamu
            ->value('roles.id');

        if ($roleId) {
            $sudahAda = DB::table('user_role')
                ->where('user_id', $adminId)
                ->where('role_id', $roleId)
                ->exists();

            if (!$sudahAda) {
                DB::table('user_role')->insert([
                    'user_id' => $adminId,
                    'role_id' => $roleId,
                    'assigned_by' => $adminId,
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}