<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PengumpulanTugas;
use App\Models\Lms\Tugas;
use App\Models\Lms\TugasKelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    private function siswaAtauAbort()
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa.');

        return $siswa;
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $siswa = $this->siswaAtauAbort();
        abort_unless($pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Mata pelajaran ini bukan untuk kelas Anda.');

        $pengampuMapel->load('mataPelajaran');

        $tugas = $pengampuMapel->tugas()
            ->with(['pengumpulan' => fn($q) => $q->where('siswa_id', $siswa->id)])
            ->latest('batas_waktu')
            ->get();

        return view('lms.siswa.tugas', compact('pengampuMapel', 'tugas'));
    }

    public function show(Tugas $tugas)
    {
        $siswa = $this->siswaAtauAbort();
        $tugas->load('pengampuMapel.mataPelajaran', 'pengampuMapel.kelas.siswa');
        abort_unless($tugas->pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Tugas ini bukan untuk kelas Anda.');

        $pengumpulanSaya = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        $kelompokSaya = null;
        if ($tugas->is_kelompok) {
            $kelompokSaya = TugasKelompok::where('tugas_id', $tugas->id)
                ->where(function ($q) use ($siswa) {
                    $q->where('ketua_siswa_id', $siswa->id)
                        ->orWhereHas('anggota', fn($qq) => $qq->where('siswa.id', $siswa->id));
                })
                ->with('ketua', 'anggota')
                ->first();
        }

        return view('lms.siswa.tugas-detail', compact('tugas', 'pengumpulanSaya', 'kelompokSaya', 'siswa'));
    }

    public function kumpul(Request $request, Tugas $tugas)
    {
        $siswa = $this->siswaAtauAbort();
        $tugas->load('pengampuMapel');
        abort_unless($tugas->pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Tugas ini bukan untuk kelas Anda.');

        $data = $request->validate([
            'catatan_siswa' => ['nullable', 'string', 'max:1000'],
            'file' => ['nullable', 'file', 'max:10240'],
            'link_jawaban' => ['nullable', 'url', 'max:500'],
        ]);
        $alasanTutup = $tugas->alasanTidakBisaKumpul();
        if ($alasanTutup) {
            return back()->withErrors(['file' => $alasanTutup]);
        }
        // Kalau tugas kelompok, cuma KETUA yang boleh kumpul, dan hasilnya
        // otomatis nempel ke SEMUA anggota kelompoknya.
        if ($tugas->is_kelompok) {
            $kelompok = TugasKelompok::where('tugas_id', $tugas->id)
                ->where('ketua_siswa_id', $siswa->id)
                ->first();

            if (! $kelompok) {
                return back()->withErrors(['file' => 'Anda belum jadi ketua kelompok manapun di tugas ini. Bikin kelompok dulu di bawah.']);
            }

            if (empty($data['link_jawaban']) && ! $request->hasFile('file')) {
                return back()->withErrors(['file' => 'Isi minimal salah satu: file atau link jawaban.'])->withInput();
            }
            $filePath = $request->hasFile('file')
                ? $request->file('file')->store('jawaban', 'public')
                : null;

            $anggotaIds = $kelompok->anggota()->pluck('siswa.id')->push($kelompok->ketua_siswa_id)->unique();

            foreach ($anggotaIds as $anggotaId) {

                PengumpulanTugas::updateOrCreate(
                    ['tugas_id' => $tugas->id, 'siswa_id' => $anggotaId],
                    [
                        'tugas_kelompok_id' => $kelompok->id,
                        'file_jawaban' => $filePath,
                        'link_jawaban' => $data['link_jawaban'] ?? null,
                        'catatan_siswa' => $data['catatan_siswa'] ?? null,
                        'dikumpulkan_at' => now(),
                        'nilai' => null,
                        'catatan_guru' => null,
                        'dinilai_at' => null,
                    ]
                );
            }

            return redirect()->route('lms.siswa.tugas.show', $tugas)->with('status', 'Tugas kelompok berhasil dikumpulkan.');
        }

        // Tugas individu — kayak sebelumnya
        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $tugas->id, 'siswa_id' => $siswa->id],
            [
                'file_jawaban' => $request->hasFile('file') ? $request->file('file')->store('jawaban', 'public') : null,
                'link_jawaban' => $data['link_jawaban'] ?? null,
                'catatan_siswa' => $data['catatan_siswa'] ?? null,
                'dikumpulkan_at' => now(),
                'nilai' => null,
                'catatan_guru' => null,
                'dinilai_at' => null,
            ]
        );

        return redirect()->route('lms.siswa.tugas.show', $tugas)->with('status', 'Tugas berhasil dikumpulkan.');
    }

    /** Bikin kelompok — siswa yang bikin otomatis jadi ketua. */
    public function buatKelompok(Request $request, Tugas $tugas)
    {
        $siswa = $this->siswaAtauAbort();
        $tugas->load('pengampuMapel.kelas.siswa');
        abort_unless($tugas->is_kelompok, 404);
        abort_unless($tugas->pengampuMapel->kelas_id === $siswa->kelas_id, 403);

        $sudahPunyaKelompok = TugasKelompok::where('tugas_id', $tugas->id)
            ->whereHas('anggota', fn($q) => $q->where('siswa.id', $siswa->id))
            ->orWhere(fn($q) => $q->where('tugas_id', $tugas->id)->where('ketua_siswa_id', $siswa->id))
            ->exists();

        abort_if($sudahPunyaKelompok, 403, 'Anda sudah tergabung di kelompok lain untuk tugas ini.');

        $data = $request->validate([
            'nama_kelompok' => ['nullable', 'string', 'max:100'],
            'anggota' => ['required', 'array', 'min:1'],
            'anggota.*' => ['exists:siswa,id'],
        ]);

        $kelompok = TugasKelompok::create([
            'tugas_id' => $tugas->id,
            'nama_kelompok' => $data['nama_kelompok'] ?? null,
            'ketua_siswa_id' => $siswa->id,
        ]);

        $kelompok->anggota()->sync($data['anggota']);

        return redirect()->route('lms.siswa.tugas.show', $tugas)->with('status', 'Kelompok berhasil dibuat. Anda jadi ketua.');
    }
}
