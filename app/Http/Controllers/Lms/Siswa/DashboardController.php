<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\Tugas;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('lms')->user();
        $siswa = $user->siswa;

        abort_if(!$siswa, 403, 'Akun Anda belum terhubung ke data siswa. Hubungi admin.');

        $mapelDiKelas = PengampuMapel::with(['guru', 'mataPelajaran'])
            ->where('kelas_id', $siswa->kelas_id)
            ->get();

        // Tugas aktif (belum lewat batas waktu) di kelas ini yang belum
        // punya baris pengumpulan (dikumpulkan_at terisi) dari siswa ini.
        $tugasBelumDikumpulkan = Tugas::whereIn('pengampu_mapel_id', $mapelDiKelas->pluck('id'))
            ->where('batas_waktu', '>=', now())
            ->whereDoesntHave('pengumpulan', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id)->whereNotNull('dikumpulkan_at');
            })
            ->count();

        return view('lms.siswa.dashboard', compact('siswa', 'mapelDiKelas', 'tugasBelumDikumpulkan'));
    }
}
