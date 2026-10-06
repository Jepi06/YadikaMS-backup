<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa.');

        $mapelDiKelas = PengampuMapel::with(['guru', 'mataPelajaran'])
            ->where('kelas_id', $siswa->kelas_id)
            ->orderBy('tahun_ajaran', 'desc')
            ->get();

        return view('lms.siswa.kelas', compact('siswa', 'mapelDiKelas'));
    }
}
