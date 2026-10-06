<?php

namespace App\Http\Controllers\Lms\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\MataPelajaran;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\Tugas;
use App\Models\Siswa;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa' => Siswa::count(),
            'total_kelas' => Kelas::count(),
            'total_mapel' => MataPelajaran::count(),
            'total_guru' => User::whereHas(
                'roles',
                fn($q) => $q->where('kode', 'guru')
                    ->whereHas('module', fn($qq) => $qq->where('kode', 'lms'))
            )->count(),
            'total_kelas_mengajar' => PengampuMapel::count(),
            'total_tugas_aktif' => Tugas::where('batas_waktu', '>=', now())->count(),
        ];

        $pengampuTerbaru = PengampuMapel::with(['guru', 'mataPelajaran', 'kelas'])
            ->latest()
            ->take(8)
            ->get();

        return view('lms.admin.dashboard', compact('stats', 'pengampuTerbaru'));
    }
}
