<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guruId = Auth::guard('lms')->id();

        $pengampuMapel = PengampuMapel::where('guru_id', $guruId)
            ->with(['mataPelajaran', 'kelas.siswa'])
            ->orderBy('tahun_ajaran', 'desc')
            ->get();

        $totalKelas  = $pengampuMapel->count();
        $totalSiswa  = $pengampuMapel->sum(fn($p) => $p->kelas->siswa->count() ?? 0);
        $tahunAjaran = $pengampuMapel->first()->tahun_ajaran ?? null;
        $semester    = $pengampuMapel->first()->semester ?? null;

        // Rata-rata kehadiran hari ini dari semua kelas yang diampu
        $presensiHariIni = PresensiLms::whereIn('pengampu_mapel_id', $pengampuMapel->pluck('id'))
            ->where('tanggal', now()->toDateString())
            ->get();

        $rataKehadiran = $presensiHariIni->count() > 0
            ? round($presensiHariIni->where('status', 'Hadir')->count() / $presensiHariIni->count() * 100)
            : null;

        // Belum ada fitur penilaian tugas di sini, jadi sementara 0.
        $tugasBelumDinilai = 0;

        return view('lms.guru.dashboard', compact(
            'pengampuMapel',
            'totalKelas',
            'totalSiswa',
            'tahunAjaran',
            'semester',
            'rataKehadiran',
            'tugasBelumDinilai'
        ));
    }
}