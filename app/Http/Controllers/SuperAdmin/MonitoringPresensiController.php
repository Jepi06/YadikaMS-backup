<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lms\JadwalPelajaran;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\PresensiLms;
use App\Models\Lms\SesiPresensi;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MonitoringPresensiController extends Controller
{
    /** Boleh membuka presensi sekian menit sebelum jam mulai dan tetap dihitung untuk jadwal itu. */
    private const TOLERANSI_MENIT = 15;

    /**
     * Tentukan sesi presensi hari ini "milik" jadwal yang mana.
     *
     * Sesi hanya ada 1 per pengampu per hari, jadi tanpa pemetaan ini semua jadwal
     * kelas yang sama ikut dianggap "dibuka". Aturan:
     *  1. Jadwal yang rentang jamnya (dikurangi toleransi) mencakup waktu dibuka_at.
     *  2. Kalau dibuka di luar semua jam: jadwal berikutnya yang belum selesai,
     *     atau jadwal terakhir kalau semuanya sudah lewat.
     *
     * @return int|null id jadwal pemilik sesi, null jika tidak ada sesi / jadwal.
     */
    private function jadwalPemilikSesi(?SesiPresensi $sesi, Collection $slots, string $hariIni): ?int
    {
        if (! $sesi || ! $sesi->dibuka_at || $slots->isEmpty()) {
            return null;
        }

        $dibuka = Carbon::parse($sesi->dibuka_at);

        foreach ($slots as $j) {
            $mulai   = Carbon::parse("{$hariIni} {$j->jam_mulai}")->subMinutes(self::TOLERANSI_MENIT);
            $selesai = Carbon::parse("{$hariIni} {$j->jam_selesai}");

            if ($dibuka->gte($mulai) && $dibuka->lt($selesai)) {
                return $j->id;
            }
        }

        $berikut = $slots->first(
            fn($j) => $dibuka->lt(Carbon::parse("{$hariIni} {$j->jam_selesai}"))
        );

        return ($berikut ?? $slots->last())->id;
    }

    /** Status 1 jadwal: dibuka | menunggu | belum_buka | terlewat. */
    private function statusJadwal($j, ?int $idPemilikSesi, string $hariIni): string
    {
        $mulai   = Carbon::parse("{$hariIni} {$j->jam_mulai}");
        $selesai = Carbon::parse("{$hariIni} {$j->jam_selesai}");

        return match (true) {
            $idPemilikSesi !== null && $idPemilikSesi === $j->id => 'dibuka',
            now()->lt($mulai)                                    => 'menunggu',
            now()->lt($selesai)                                  => 'belum_buka',
            default                                              => 'terlewat',
        };
    }

    public function index()
    {
        $hariIni = now()->toDateString();

        // Semua sesi hari ini (aktif maupun sudah ditutup)
        $sesiHariIni = SesiPresensi::whereDate('tanggal', $hariIni)->get()->keyBy('pengampu_mapel_id');

        $sesiAktif = $sesiHariIni->filter(fn($s) => $s->dibuka_at <= now() && $s->ditutup_at > now());

        $sudahPresensi = PresensiLms::whereDate('tanggal', $hariIni)
            ->selectRaw('pengampu_mapel_id, COUNT(*) as total')
            ->groupBy('pengampu_mapel_id')
            ->pluck('total', 'pengampu_mapel_id');

        // Semua slot jadwal hari ini per pengampu (dipakai untuk memetakan sesi -> jadwal)
        $jadwalMap = JadwalPelajaran::where('hari', now()->dayOfWeekIso)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('pengampu_mapel_id');

        // ── A. Jadwal hari ini vs realisasi presensi ───────────
        $urutan = ['belum_buka' => 0, 'terlewat' => 1, 'dibuka' => 2, 'menunggu' => 3];

        $jadwalHariIni = JadwalPelajaran::with([
            'pengampuMapel.guru',
            'pengampuMapel.mataPelajaran',
            'pengampuMapel.kelas' => fn($q) => $q->withCount('siswa'),
        ])
            ->where('hari', now()->dayOfWeekIso)
            ->whereHas('pengampuMapel', fn($q) => $q
                ->where('tahun_ajaran', TahunAjaran::sekarang())
                ->where('semester', TahunAjaran::semesterSekarang()))
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($j) use ($hariIni, $sesiHariIni, $sesiAktif, $sudahPresensi, $jadwalMap) {
                $p    = $j->pengampuMapel;
                $sesi = $sesiHariIni->get($p->id);

                $idPemilik = $this->jadwalPemilikSesi($sesi, $jadwalMap->get($p->id, collect()), $hariIni);
                $status    = $this->statusJadwal($j, $idPemilik, $hariIni);
                $dibuka    = $status === 'dibuka';

                return [
                    'pengampu_id' => $p->id,
                    'guru'        => $p->guru->name ?? '-',
                    'mapel'       => $p->mataPelajaran->nama ?? '-',
                    'kelas'       => $p->kelas->nama_kelas ?? '-',
                    'ruangan'     => $j->ruangan,
                    'mulai'       => substr($j->jam_mulai, 0, 5),
                    'selesai'     => substr($j->jam_selesai, 0, 5),
                    'status'      => $status,
                    'sesi_aktif'  => $dibuka && $sesiAktif->has($p->id),
                    'dibuka_at'   => $dibuka ? $sesi?->dibuka_at : null,
                    // hitungan siswa hanya bermakna untuk jadwal yang sesinya benar-benar dibuka
                    'sudah'       => $dibuka ? ($sudahPresensi[$p->id] ?? 0) : null,
                    'total'       => $p->kelas->siswa_count ?? 0,
                ];
            })
            ->sortBy(fn($r) => $urutan[$r['status']])
            ->values();

        $ringkas = $jadwalHariIni->countBy('status');

        // ── B. Status per guru (dengan peringatan jadwal) ───────
        $pengampu = PengampuMapel::with(['guru', 'mataPelajaran', 'kelas' => fn($q) => $q->withCount('siswa')])->get();

        $daftarGuru = $pengampu->groupBy('guru_id')
            ->map(function ($items) use ($sesiAktif, $sesiHariIni, $sudahPresensi, $jadwalMap, $hariIni) {

                $semuaKelas = $items->map(function ($p) use ($sesiAktif, $sesiHariIni, $sudahPresensi, $jadwalMap, $hariIni) {
                    $sesiA = $sesiAktif->get($p->id);

                    $slots     = $jadwalMap->get($p->id, collect());
                    $idPemilik = $this->jadwalPemilikSesi($sesiHariIni->get($p->id), $slots, $hariIni);

                    $jadwalStatus = null;
                    $jadwalJam = null;
                    $rank = ['belum_buka' => 0, 'terlewat' => 1, 'dibuka' => 2, 'menunggu' => 3];

                    foreach ($slots as $j) {
                        $st = $this->statusJadwal($j, $idPemilik, $hariIni);

                        if ($jadwalStatus === null || $rank[$st] < $rank[$jadwalStatus]) {
                            $jadwalStatus = $st;
                            $jadwalJam = substr($j->jam_mulai, 0, 5) . '–' . substr($j->jam_selesai, 0, 5);
                        }
                    }

                    return [
                        'pengampu_id'   => $p->id,
                        'kelas'         => $p->kelas->nama_kelas ?? '-',
                        'mapel'         => $p->mataPelajaran->nama ?? '-',
                        'aktif'         => (bool) $sesiA,
                        'ditutup_at'    => $sesiA?->ditutup_at,
                        'sudah'         => $sudahPresensi[$p->id] ?? 0,
                        'total'         => $p->kelas->siswa_count ?? 0,
                        'jadwal_status' => $jadwalStatus,
                        'jadwal_jam'    => $jadwalJam,
                    ];
                })->sortBy(fn($k) => match (true) {
                    $k['aktif']                          => 0,
                    $k['jadwal_status'] === 'belum_buka' => 1,
                    $k['jadwal_status'] === 'terlewat'   => 2,
                    $k['jadwal_status'] !== null         => 3,
                    default                              => 4,
                })->values();

                $status = match (true) {
                    $semuaKelas->contains('aktif', true)                 => 'buka',
                    $semuaKelas->contains('jadwal_status', 'belum_buka') => 'harus_buka',
                    $semuaKelas->contains('jadwal_status', 'terlewat')   => 'terlewat',
                    default                                              => 'tutup',
                };

                return [
                    'guru'   => $items->first()->guru,
                    'status' => $status,
                    'kelas'  => $semuaKelas,
                ];
            })
            ->filter(fn($row) => $row['guru'])
            ->sortBy(fn($row) => ['harus_buka' => 0, 'terlewat' => 1, 'buka' => 2, 'tutup' => 3][$row['status']])
            ->values();

        $jumlahBuka      = $daftarGuru->where('status', 'buka')->count();
        $jumlahHarusBuka = $daftarGuru->where('status', 'harus_buka')->count();
        $jumlahTerlewat  = $daftarGuru->where('status', 'terlewat')->count();
        $jumlahTutup     = $daftarGuru->where('status', 'tutup')->count();

        return view('admin.monitoring-presensi', compact(
            'jadwalHariIni',
            'ringkas',
            'daftarGuru',
            'jumlahBuka',
            'jumlahHarusBuka',
            'jumlahTerlewat',
            'jumlahTutup'
        ));
    }

    /** Detail: siapa saja siswa yang sudah dan belum presensi di 1 kelas/mapel. */
    public function detail(Request $request, PengampuMapel $pengampuMapel)
    {
        $tanggal = $request->query('tanggal');
        $tanggal = ($tanggal && Carbon::hasFormat($tanggal, 'Y-m-d')) ? $tanggal : now()->toDateString();

        $pengampuMapel->load(['guru', 'mataPelajaran', 'kelas.siswa']);

        $presensi = PresensiLms::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        $sesi = SesiPresensi::where('pengampu_mapel_id', $pengampuMapel->id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        $rows = $pengampuMapel->kelas->siswa
            ->map(fn($s) => ['siswa' => $s, 'presensi' => $presensi->get($s->id)])
            ->sortBy(fn($r) => $r['presensi'] ? 1 : 0)   // yang belum presensi di atas
            ->values();

        $sudah = $rows->filter(fn($r) => $r['presensi'])->count();
        $belum = $rows->count() - $sudah;

        return view('admin.monitoring-presensi-detail', compact(
            'pengampuMapel',
            'tanggal',
            'sesi',
            'rows',
            'sudah',
            'belum'
        ));
    }
}