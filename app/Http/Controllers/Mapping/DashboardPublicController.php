<?php

namespace App\Http\Controllers\Mapping;

use Illuminate\Http\Request;

use App\Models\Mapping\PenempatanPkl;
use App\Models\Jurusan;
use App\Models\Kelas;

class DashboardPublicController extends Controller
{
    public function index(Request $request)
    {
        // Filter jurusan & kelas dipakai bersama oleh kedua tabel (approved & belum approved)
        $applyFilter = function ($query) use ($request) {
            if ($request->filled('jurusan')) {
                $query->whereHas('siswa.kelas', function ($q) use ($request) {
                    $q->where('jurusan_id', $request->jurusan);
                });
            }

            if ($request->filled('kelas')) {
                $query->whereHas('siswa.kelas', function ($q) use ($request) {
                    $q->where('id', $request->kelas);
                });
            }

            return $query;
        };

        // Data siswa yang SUDAH disetujui seluruh approval
        $dataQuery = PenempatanPkl::with([
            'siswa.kelas.jurusan',
            'guruPembimbing',
            'tempatPkl'
        ])->where('status', 'approved');

        $applyFilter($dataQuery);

        $data = $dataQuery->latest()->paginate(10, ['*'], 'approved_page')->withQueryString();

        // BARU: Data siswa yang pengajuannya masih berjalan / BELUM disetujui penuh
        // (status diajukan, tapi belum approved), supaya siswa/publik bisa memantau
        // sedang menunggu approval dari tahap mana.
        $belumApprovedQuery = PenempatanPkl::with([
            'siswa.kelas.jurusan',
            'guruPembimbing',
            'tempatPkl'
        ])->where('status', 'diajukan');

        $applyFilter($belumApprovedQuery);

        $belumApproved = $belumApprovedQuery->latest()->paginate(10, ['*'], 'proses_page')->withQueryString();

        $jurusan = Jurusan::orderBy('nama')->get();

        $kelas = Kelas::with('jurusan')
            ->orderBy('nama_kelas')
            ->get();

        return view('Mapping.public.public', compact(
            'data',
            'belumApproved',
            'jurusan',
            'kelas'
        ));
    }
}
