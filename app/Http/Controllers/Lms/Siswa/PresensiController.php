<?php

namespace App\Http\Controllers\Lms\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    private function siswaAtauAbort()
    {
        $siswa = Auth::guard('lms')->user()->siswa;
        abort_if(! $siswa, 403, 'Akun Anda belum terhubung ke data siswa. Hubungi admin.');

        return $siswa;
    }

    /**
     * Dibuka lewat scan QR (link berisi token). Siswa harus sudah login
     * (guard lms) di device yang dipakai scan — kalau belum, middleware
     * auth.lms otomatis lempar ke halaman login dulu.
     */
    public function scan(string $token)
    {
        $siswa = $this->siswaAtauAbort();
        $siswa->loadMissing('kelas');

        $sesi = SesiPresensi::cariDariTokenDinamis($token);

        if (! $sesi) {
            return view('lms.siswa.presensi-scan', [
                'berhasil' => false,
                'pesan' => 'QR tidak dikenali atau sudah tidak berlaku. Scan ulang QR terbaru di layar.',
            ]);
        }

        $sesi->load('pengampuMapel.mataPelajaran', 'pengampuMapel.guru');

        if (! $sesi->masih_aktif) {
            return view('lms.siswa.presensi-scan', [
                'berhasil' => false,
                'pesan' => 'Sesi presensi ini sudah ditutup oleh guru.',
            ]);
        }

        if ($sesi->pengampuMapel->kelas_id != $siswa->kelas_id) {
            return view('lms.siswa.presensi-scan', [
                'berhasil' => false,
                'pesan' => 'QR ini bukan untuk kelas Anda.',
            ]);
        }

        $presensi = PresensiLms::updateOrCreate(
            [
                'pengampu_mapel_id' => $sesi->pengampu_mapel_id,
                'siswa_id' => $siswa->id,
                'tanggal' => $sesi->tanggal,
            ],
            [
                'status' => 'Hadir',
                'sumber' => 'barcode',
                'sesi_presensi_id' => $sesi->id,
            ]
        );

        $mp = $sesi->pengampuMapel->mataPelajaran;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? '-'));
        $guru = $sesi->pengampuMapel->guru;

        return view('lms.siswa.presensi-scan', [
            'berhasil' => true,
            'pesan' => 'Anda tercatat HADIR pada mata pelajaran ' . $namaMapel . '.',
            'waktu' => $presensi->updated_at,
            'siswa' => $siswa,
            'mapel' => $namaMapel,
            'guru' => $guru->nama ?? ($guru->name ?? null),
        ]);
    }

    /** Halaman "Presensi Sekarang" — buka kamera langsung di dalam LMS. */
    public function kamera()
    {
        $siswa = $this->siswaAtauAbort();
        $siswa->loadMissing('kelas');

        return view('lms.siswa.presensi-kamera', compact('siswa'));
    }

    public function riwayat(Request $request)
    {
        $siswa = $this->siswaAtauAbort();

        $dasar = PresensiLms::where('siswa_id', $siswa->id);

        // Statistik seluruh riwayat (bukan hanya halaman ini)
        $hitung = (clone $dasar)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $totalQr = (clone $dasar)->whereIn('sumber', ['barcode', 'qr', 'scan'])->count();

        $statusValid = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

        $riwayat = (clone $dasar)
            ->with('pengampuMapel.mataPelajaran', 'pengampuMapel.guru')
            ->when(in_array($request->query('status'), $statusValid, true), fn($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('mapel'), fn($q) => $q->where('pengampu_mapel_id', (int) $request->query('mapel')))
            ->orderByDesc('tanggal')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $daftarMapel = PengampuMapel::with('mataPelajaran')
            ->where('kelas_id', $siswa->kelas_id)
            ->get();

        return view('lms.siswa.presensi-riwayat', compact('riwayat', 'hitung', 'totalQr', 'daftarMapel'));
    }
}