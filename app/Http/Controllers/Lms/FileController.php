<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\Materi;
use App\Models\Lms\ModulAjar;
use App\Models\Lms\PengumpulanTugas;
use App\Models\Lms\Tugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Serve file materi/tugas/jawaban/modul-ajar LEWAT LARAVEL (bukan
 * symlink public/storage) — lebih portable + ada pengecekan otorisasi.
 */
class FileController extends Controller
{
    public function materi(Materi $materi)
    {
        $this->authorizeAksesKelas($materi->pengampuMapel->guru_id, $materi->pengampuMapel->kelas_id);

        // Guru pemilik selalu boleh; siswa harus lolos aturan kunci materi.
        $user = Auth::guard('lms')->user();
        if ($user->id !== $materi->pengampuMapel->guru_id) {
            $alasan = $materi->alasanTerkunci($user->siswa);
            abort_if($alasan, 403, $alasan);
        }

        abort_unless($materi->file_path, 404);

        abort_unless($materi->file_path, 404);

        return Storage::disk('public')->response($materi->file_path);
    }

    public function tugasLampiran(Tugas $tugas)
    {
        $this->authorizeAksesKelas($tugas->pengampuMapel->guru_id, $tugas->pengampuMapel->kelas_id);

        abort_unless($tugas->file_lampiran, 404);

        return Storage::disk('public')->response($tugas->file_lampiran);
    }

    public function jawaban(PengumpulanTugas $pengumpulan)
    {
        $user = Auth::guard('lms')->user();
        $guruPengampuId = $pengumpulan->tugas->pengampuMapel->guru_id;
        $siswaPemilikId = $pengumpulan->siswa->user_id;

        abort_unless(
            $user->id === $guruPengampuId || $user->id === $siswaPemilikId,
            403,
            'Anda tidak berhak mengakses file ini.'
        );

        abort_unless($pengumpulan->file_jawaban, 404);

        return Storage::disk('public')->response($pengumpulan->file_jawaban);
    }

    /**
     * Modul Ajar TIDAK BOLEH diakses siswa sama sekali — cuma guru
     * pemiliknya. (Super Admin punya jalur file terpisah, lihat
     * SuperAdmin\ModulAjarController::file(), karena panel itu
     * guard-agnostic dan gak lewat middleware auth.lms.)
     */
    public function modulAjar(ModulAjar $modulAjar)
    {
        $user = Auth::guard('lms')->user();

        abort_unless(
            $user && $user->id === $modulAjar->pengampuMapel->guru_id,
            403,
            'Modul ajar ini bukan milik Anda.'
        );

        return Storage::disk('public')->response($modulAjar->file_path);
    }

    /** Guru pengampu ATAU siswa di kelas yang sama boleh akses. */
    private function authorizeAksesKelas(int $guruId, int $kelasId): void
    {
        $user = Auth::guard('lms')->user();

        if ($user->id === $guruId) {
            return;
        }

        $siswa = $user->siswa;
        abort_unless($siswa && $siswa->kelas_id === $kelasId, 403, 'Anda tidak berhak mengakses file ini.');
    }
}
