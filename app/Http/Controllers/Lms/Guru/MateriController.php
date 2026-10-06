<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\Materi;
use App\Models\Lms\PengampuMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas');
        $materi = $pengampuMapel->materi()->orderBy('urutan')->orderBy('created_at')->get();

        return view('lms.guru.materi', compact('pengampuMapel', 'materi'));
    }

    public function store(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB
            'link_url' => ['nullable', 'url:http,https', 'max:2048'],
            'mode_akses' => ['required', 'in:bebas,berurutan,manual,tanggal'],
            'buka_pada' => ['nullable', 'date', 'required_if:mode_akses,tanggal'],
        ]);

        $urutan = $pengampuMapel->materi()->max('urutan') + 1;

        Materi::create([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $request->hasFile('file')
                ? $request->file('file')->store('materi', 'public')
                : null,
            'link_url' => $data['link_url'] ?? null,
            'urutan' => $urutan,
            'mode_akses' => $data['mode_akses'],
            'buka_pada' => $data['mode_akses'] === 'tanggal' ? $data['buka_pada'] : null,
        ]);
        return back()->with('status', 'Materi berhasil ditambahkan.');
    }

    public function destroy(Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return back()->with('status', 'Materi dihapus.');
    }
    /** Buka/kunci manual — khusus materi mode "Dibuka Guru". */
    public function toggleBuka(Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);
        abort_unless($materi->mode_akses === 'manual', 422, 'Materi ini bukan mode "Dibuka Guru".');

        $materi->update(['dibuka_manual' => ! $materi->dibuka_manual]);

        return back()->with('status', $materi->dibuka_manual ? 'Materi dibuka untuk siswa.' : 'Materi dikunci lagi.');
    }
    /** Ubah/hapus link materi yang sudah ada. */
    public function updateLink(Request $request, Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        $data = $request->validate([
            'link_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        $materi->update(['link_url' => $data['link_url'] ?? null]);

        return back()->with('status', 'Link materi diperbarui.');
    }
    /** Ganti mode akses materi yang sudah ada. */
    public function updateAkses(Request $request, Materi $materi)
    {
        $this->authorizePengampu($materi->pengampuMapel);

        $data = $request->validate([
            'mode_akses' => ['required', 'in:bebas,berurutan,manual,tanggal'],
            'buka_pada' => ['nullable', 'date', 'required_if:mode_akses,tanggal'],
        ]);

        $materi->update([
            'mode_akses' => $data['mode_akses'],
            'buka_pada' => $data['mode_akses'] === 'tanggal' ? $data['buka_pada'] : null,
            'dibuka_manual' => $data['mode_akses'] === 'manual' ? $materi->dibuka_manual : false,
        ]);

        return back()->with('status', 'Pengaturan akses materi diperbarui.');
    }
}
