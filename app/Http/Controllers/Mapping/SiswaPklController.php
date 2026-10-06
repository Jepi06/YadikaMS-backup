<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\{PenempatanPkl, TempatPkl, GuruPembimbing};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Controller self-service untuk SISWA (guard 'pkl', role_pkl = 'siswa').
 *
 * Prasyarat penting: siswa HARUS sudah terdaftar lebih dulu di tabel `siswa`
 * oleh admin, dan akun login-nya (tabel users) sudah dihubungkan lewat
 * kolom `siswa.user_id`. Siswa tidak bisa mengajukan PKL kalau datanya
 * belum ada di master siswa — ini yang membuat admin bisa memantau siapa
 * saja yang belum diinput datanya vs yang sudah terdaftar tapi belum
 * mengajukan (lihat SiswaController::belumMengajukan()).
 */
class SiswaPklController extends Controller
{
    /**
     * Ambil data siswa dari user yang sedang login.
     * Melempar 403 kalau user login tidak terhubung ke data siswa manapun,
     * supaya siswa yang belum terdaftar di master data tidak bisa lanjut.
     */
    private function siswaAktif()
    {
        $user  = Auth::guard('pkl')->user();
        $siswa = $user?->siswa;

        abort_if(! $siswa, 403, 'Akun Anda belum terhubung dengan data siswa. Silakan hubungi admin.');

        return $siswa;
    }

    /**
     * Dashboard/status PKL milik siswa yang login:
     * - status pengajuan saat ini (draft/diajukan/approved/rejected)
     * - progres approval (berapa dari 4 approver sudah menyetujui)
     * - approver mana saja yang MASIH belum approve (yang ditunggu siswa)
     */
    public function status()
    {
        $siswa = $this->siswaAktif();

        $penempatan = $siswa->penempatanPkl()
            ->with([
                'tempatPkl', 'guruPembimbing',
                'approverWaliKelas', 'approverGuruBk',
                'approverKesiswaan', 'approverKepalaJurusan',
            ])
            ->whereNotIn('status', ['rejected'])
            ->latest()
            ->first();

        $riwayat = $siswa->penempatanPkl()->with('tempatPkl')->latest()->get();

        return view('Mapping.siswa-pkl.status', [
            'siswa'      => $siswa,
            'penempatan' => $penempatan,
            // getPendingApproversAttribute() di model -> daftar nama tahap yang masih pending
            'pendingApprovers' => $penempatan?->pending_approvers ?? [],
            'riwayat'    => $riwayat,
        ]);
    }

    /**
     * Form pengajuan tempat PKL oleh siswa sendiri.
     */
    public function create()
    {
        $siswa = $this->siswaAktif();

        if ($siswa->sudahDitempatkan()) {
            return redirect()
                ->route('siswa.pkl.status')
                ->with('error', 'Anda sudah memiliki pengajuan PKL yang aktif.');
        }

        $tempatPkl      = TempatPkl::orderBy('nama_tempat')->get();
        $guruPembimbing = GuruPembimbing::orderBy('nama')->get();

        return view('Mapping.siswa-pkl.create', compact('siswa', 'tempatPkl', 'guruPembimbing'));
    }

    /**
     * Simpan pengajuan tempat PKL oleh siswa.
     * Dibuat berstatus 'draft' dulu, lalu langsung diajukan ('diajukan')
     * supaya otomatis masuk antrian approval wali kelas.
     */
    public function store(Request $request)
    {
        $siswa = $this->siswaAktif();

        if ($siswa->sudahDitempatkan()) {
            throw ValidationException::withMessages([
                'siswa_id' => 'Anda sudah memiliki pengajuan PKL yang aktif dan tidak bisa mengajukan lagi.',
            ]);
        }

        $data = $request->validate([
            'tempat_pkl_id'      => 'required|exists:tempat_pkl,id',
            'guru_pembimbing_id' => 'required|exists:guru_pembimbing,id',
            'tanggal_mulai'      => 'required|date',
            'tanggal_selesai'    => 'required|date|after:tanggal_mulai',
            'tahun_ajaran'       => 'required|string|max:20',
            'keterangan'         => 'nullable|string',
        ]);

        $penempatan = PenempatanPkl::create([
            'siswa_id'           => $siswa->id, // dipaksa dari akun login, BUKAN dari input form
            'tempat_pkl_id'      => $data['tempat_pkl_id'],
            'guru_pembimbing_id' => $data['guru_pembimbing_id'],
            'tanggal_mulai'      => $data['tanggal_mulai'],
            'tanggal_selesai'    => $data['tanggal_selesai'],
            'tahun_ajaran'       => $data['tahun_ajaran'],
            'keterangan'         => $data['keterangan'] ?? null,
            'status'             => 'diajukan',
        ]);

        return redirect()
            ->route('siswa.pkl.status')
            ->with('success', 'Pengajuan tempat PKL berhasil dikirim dan menunggu approval Wali Kelas.');
    }
}
