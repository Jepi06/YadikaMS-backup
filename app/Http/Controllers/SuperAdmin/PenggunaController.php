<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    /** Daftar semua pengguna + badge role di tiap modul. */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $pengguna = User::with('roles.module')
            ->when($q, fn($query) => $query->where(fn($sub) => $sub
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pengguna.index', compact('pengguna', 'q'));
    }

    /** Form kelola akses (module+role) untuk 1 user. */
    public function akses(User $user)
    {
        $modules = Module::with('roles')->orderBy('nama')->get();
        $roleIdsUser = $user->roles()->pluck('roles.id')->toArray();

        return view('admin.pengguna.akses', compact('user', 'modules', 'roleIdsUser'));
    }

    /** Simpan perubahan akses — sync total (checklist yang tercentang = akses akhir). */
    public function updateAkses(Request $request, User $user)
    {
        $superAdmin = $request->attributes->get('superAdminUser');

        $data = $request->validate([
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'is_super_admin' => ['nullable', 'boolean'],
        ]);

        $roleIds = $data['role_ids'] ?? [];

        // sync() otomatis nambah role baru yang dicentang & hapus yang
        // di-uncheck, sekalian isi kolom pivot assigned_by/assigned_at.
        $syncData = collect($roleIds)->mapWithKeys(fn($roleId) => [
            $roleId => [
                'assigned_by' => $superAdmin->id,
                'assigned_at' => now(),
            ],
        ])->toArray();

        $user->roles()->sync($syncData);

        // Super admin nggak boleh cabut status super admin dirinya
        // sendiri lewat form ini (biar gak ke-lock out dari panel).
        if ($user->id !== $superAdmin->id) {
            $user->update(['is_super_admin' => $request->boolean('is_super_admin')]);
        }

        return redirect()
            ->route('admin.pengguna.akses', $user)
            ->with('status', 'Akses ' . $user->name . ' berhasil diperbarui.');
    }
}
