<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    public function index()
    {
        $pengampuMapel = Auth::guard('lms')->user()
            ->pengampuMapel()
            ->with(['mataPelajaran', 'kelas.siswa'])
            ->orderBy('tahun_ajaran', 'desc')
            ->get();

        $totalKelas  = $pengampuMapel->count();
        $tahunAjaran = $pengampuMapel->first()->tahun_ajaran ?? null;
        $semester    = $pengampuMapel->first()->semester ?? null;

        return view('lms.guru.kelas', compact(
            'pengampuMapel',
            'totalKelas',
            'tahunAjaran',
            'semester'
        ));
    }
}