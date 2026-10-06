<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /** Daftar kelas, bisa difilter per jurusan/tingkat lewat query string. */
    public function index(Request $request)
    {
        $kelas = Kelas::with(['jurusan', 'waliKelas'])
            ->withCount('siswa')
            ->when($request->filled('jurusan_id'), fn ($q) => $q->where('jurusan_id', $request->jurusan_id))
            ->when($request->filled('tingkat'), fn ($q) => $q->where('tingkat', $request->tingkat))
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where('nama_kelas', 'like', '%' . $request->cari . '%');
            })
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->paginate(15)
            ->withQueryString();

        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('admin.kelas.index', compact('kelas', 'jurusanList'));
    }

    public function create()
    {
        $jurusanList = Jurusan::orderBy('nama')->get();
        $guruList = $this->guruList();

        return view('admin.kelas.create', compact('jurusanList', 'guruList'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        Kelas::create($data);

        return redirect()
            ->route('admin.kelas.index')
            ->with('success', 'Kelas "' . $data['nama_kelas'] . '" berhasil ditambahkan.');
    }

    public function show(Kelas $kela)
    {
        $kela->load(['jurusan', 'waliKelas', 'siswa' => function ($q) {
            $q->orderBy('nama');
        }, 'pengampuMapel.mataPelajaran']);

        return view('admin.kelas.show', ['kelas' => $kela]);
    }

    public function edit(Kelas $kela)
    {
        $jurusanList = Jurusan::orderBy('nama')->get();
        $guruList = $this->guruList();

        return view('admin.kelas.edit', [
            'kelas' => $kela,
            'jurusanList' => $jurusanList,
            'guruList' => $guruList,
        ]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $data = $this->validateData($request, $kela->id);

        $kela->update($data);

        return redirect()
            ->route('admin.kelas.index')
            ->with('success', 'Kelas "' . $data['nama_kelas'] . '" berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        if ($kela->siswa()->exists()) {
            return back()->with(
                'error',
                'Kelas "' . $kela->nama_kelas . '" masih punya siswa terdaftar, pindahkan siswanya dulu sebelum menghapus kelas ini.'
            );
        }

        $nama = $kela->nama_kelas;
        $kela->delete();

        return redirect()
            ->route('admin.kelas.index')
            ->with('success', 'Kelas "' . $nama . '" berhasil dihapus.');
    }

    /** Validasi form tambah/edit kelas. */
    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                'unique:kelas,nama_kelas' . ($ignoreId ? ',' . $ignoreId : ''),
            ],
            'tingkat' => ['required', 'in:X,XI,XII'],
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
        ]);
    }

    /**
     * Daftar user yang bisa dijadikan wali kelas.
     * Sesuaikan filter ini kalau kamu punya kolom/relasi role khusus untuk guru.
     */
    private function guruList()
    {
        return User::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}