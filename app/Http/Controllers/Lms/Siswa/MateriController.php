<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use Illuminate\Support\Facades\Auth;
use App\Models\Lms\Materi;
use App\Models\Lms\MateriSelesai;

class MateriController extends Controller
{
    private function authorizeKelas(PengampuMapel $pengampuMapel)
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa.');
        abort_unless($pengampuMapel->kelas_id === $siswa->kelas_id, 403, 'Mata pelajaran ini bukan untuk kelas Anda.');

        return $siswa;
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $siswa = $this->authorizeKelas($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'guru');
        $materi = $pengampuMapel->materi()->orderBy('urutan')->orderBy('created_at')->get();

        $selesaiIds = MateriSelesai::where('siswa_id', $siswa->id)
            ->whereIn('materi_id', $materi->pluck('id'))
            ->pluck('materi_id')
            ->all();

        // null = terbuka, string = alasan terkunci
        $statusAkses = $materi->mapWithKeys(fn($m) => [$m->id => $m->alasanTerkunci($siswa)]);

        return view('lms.siswa.materi', compact('pengampuMapel', 'materi', 'selesaiIds', 'statusAkses'));
    }
    public function tandaiSelesai(Materi $materi)
    {
        $siswa = $this->authorizeKelas($materi->pengampuMapel);

        // Gak boleh nandain selesai materi yang masih terkunci
        abort_if($materi->alasanTerkunci($siswa), 403, 'Materi ini masih terkunci.');

        MateriSelesai::firstOrCreate([
            'materi_id' => $materi->id,
            'siswa_id' => $siswa->id,
        ]);

        return back()->with('status', 'Materi ditandai selesai.');
    }
}
