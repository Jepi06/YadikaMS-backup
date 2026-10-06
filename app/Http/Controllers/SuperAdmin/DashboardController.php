<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $superAdmin = $request->attributes->get('superAdminUser');

        $modules = Module::withCount('roles')->get();

        $stats = [
            'total_pengguna' => User::count(),
            'total_super_admin' => User::where('is_super_admin', true)->count(),
            'total_guru_lms' => User::whereHas(
                'roles',
                fn($q) => $q->where('kode', 'guru')->whereHas('module', fn($qq) => $qq->where('kode', 'lms'))
            )->count(),
        ];

        return view('admin.dashboard', compact('superAdmin', 'modules', 'stats'));
    }
}
