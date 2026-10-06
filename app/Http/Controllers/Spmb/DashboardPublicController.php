<?php

namespace App\Http\Controllers\Spmb;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\SPMB\Pendaftar;
use Illuminate\Http\Request;

class DashboardPublicController extends Controller
{
    /**
     * Landing page publik: siswa mencari statusnya sendiri lewat
     * nomor pendaftaran atau nama lengkap. Hanya menampilkan
     * data non-sensitif: nama, jurusan, dan status diterima/tidak.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('cari'));
        $hasil = collect();

        if ($keyword !== '') {
            $hasil = Pendaftar::query()
                ->with('jurusan:id,nama')
                ->where(function ($q) use ($keyword) {
                    $q->where('no_pendaftaran', 'like', "%{$keyword}%")
                        ->orWhere('nama_lengkap', 'like', "%{$keyword}%");
                })
                ->orderBy('nama_lengkap')
                ->limit(20)
                ->get([
                    'id',
                    'no_pendaftaran',
                    'nama_lengkap',
                    'jurusan_id',
                    'status',
                ]);
        }

        $rekapKuota = Jurusan::withCount('pendaftar')->get(['id', 'kode', 'nama', 'kuota']);

        return view('spmb.public.dashboard', compact('hasil', 'keyword', 'rekapKuota'));
    }
}
