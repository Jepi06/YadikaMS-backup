<?php

namespace App\Http\Controllers\Mapping;

use App\Models\{Siswa, Kelas};
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas.jurusan', 'penempatanAktif']);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nis', 'like', "%{$request->search}%");
            });
        }
        // Filter tambahan: lihat khusus siswa yang belum/sudah mengajukan PKL
        if ($request->filled('status_pengajuan')) {
            $request->status_pengajuan === 'belum'
                ? $query->belumMengajukan()
                : $query->sudahMengajukan();
        }

        $siswa = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kelas = Kelas::with('jurusan')->orderBy('nama_kelas')->get();

        // Statistik ringkas untuk ditampilkan di halaman index (badge/counter)
        $totalSiswa         = Siswa::count();
        $totalBelumMengajukan = Siswa::belumMengajukan()->count();

        return view('Mapping.siswa.index', compact(
            'siswa', 'kelas', 'totalSiswa', 'totalBelumMengajukan'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nis'           => 'required|string|unique:siswa,nis',
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id'      => 'required|exists:kelas,id',
            'alamat'        => 'nullable|string',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        Siswa::create($data);

        return back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'nis'           => 'required|string|unique:siswa,nis,' . $siswa->id,
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id'      => 'required|exists:kelas,id',
            'alamat'        => 'nullable|string',
            'no_hp'         => 'nullable|string|max:20',
        ]);

        $siswa->update($data);

        return back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    /**
     * Halaman/endpoint khusus admin: daftar siswa yang BELUM mengajukan
     * tempat PKL sama sekali (atau semua pengajuannya ditolak).
     */
    public function belumMengajukan(Request $request)
    {
        $query = Siswa::with('kelas.jurusan')->belumMengajukan();

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswa = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kelas = Kelas::with('jurusan')->orderBy('nama_kelas')->get();

        return view('Mapping.siswa.belum-mengajukan', compact('siswa', 'kelas'));
    }

    /**
     * API endpoint: get siswa berdasarkan kelas (untuk popup penempatan)
     */
    public function byKelas(Kelas $kelas)
    {
        $siswa = $kelas->siswa()
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama', 'jenis_kelamin']);

        // Tandai siswa yang sudah ditempatkan (bukan rejected)
        $siswa->each(function ($s) {
            $s->sudah_ditempatkan = $s->sudahDitempatkan();
        });

        return response()->json($siswa);
    }
}
