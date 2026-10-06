<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\{PenempatanPkl, TempatPkl, GuruPembimbing, Kelas};
use App\Models\Siswa;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa'          => Siswa::count(),
            'total_penempatan'     => PenempatanPkl::count(),
            'sudah_ditempatkan'    => PenempatanPkl::aktif()->distinct('siswa_id')->count('siswa_id'),
            // BARU: jumlah siswa yang sama sekali belum mengajukan PKL (atau semua ditolak)
            'belum_mengajukan'     => Siswa::belumMengajukan()->count(),
            'menunggu_approval'    => PenempatanPkl::where('status', 'diajukan')->count(),
            'approved'             => PenempatanPkl::where('status', 'approved')->count(),
            'total_tempat'         => TempatPkl::count(),
            'total_guru'           => GuruPembimbing::count(),
        ];

        $recentPenempatan = PenempatanPkl::with(['siswa.kelas', 'tempatPkl', 'guruPembimbing'])
            ->latest()
            ->take(5)
            ->get();

        // BARU: contoh singkat siswa yang belum mengajukan, untuk ditampilkan di widget dashboard
        $siswaBelumMengajukan = Siswa::with('kelas')
            ->belumMengajukan()
            ->orderBy('nama')
            ->take(5)
            ->get();

        return view('Mapping.dashboard.index', compact('stats', 'recentPenempatan', 'siswaBelumMengajukan'));
    }
}
