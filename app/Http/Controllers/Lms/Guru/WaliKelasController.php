<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\WaliKelasPeriode;
use App\Services\Lms\NilaiAkhirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class WaliKelasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('lms')->user();

        // PERBAIKAN: sebelumnya baca dari kolom LAMA kelas.wali_kelas_id
        // (global, gak ada dimensi tahun). Assignment wali kelas sekarang
        // disimpan di tabel wali_kelas_periode_lms (per tahun_ajaran +
        // semester) — jadi harus dibaca dari situ.
        $penugasanWali = WaliKelasPeriode::where('user_id', $user->id)
            ->with('kelas')
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();

        abort_if($penugasanWali->isEmpty(), 403, 'Anda bukan wali kelas manapun.');

        $kelasDiwalikan = $penugasanWali->pluck('kelas')->unique('id')->values();

        $kelasId = (int) $request->query('kelas_id', $kelasDiwalikan->first()->id);
        $kelas = $kelasDiwalikan->firstWhere('id', $kelasId) ?? $kelasDiwalikan->first();

        $kelas->load('siswa');

        $periodeList = PengampuMapel::where('kelas_id', $kelas->id)
            ->select('tahun_ajaran', 'semester')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();

        $tahunAjaran = $request->query('tahun_ajaran', $periodeList->first()->tahun_ajaran ?? null);
        $semester = $request->query('semester', $periodeList->first()->semester ?? null);

        $daftarPengampu = PengampuMapel::with('mataPelajaran', 'guru')
            ->where('kelas_id', $kelas->id)
            ->when($tahunAjaran, fn($q) => $q->where('tahun_ajaran', $tahunAjaran))
            ->when($semester, fn($q) => $q->where('semester', $semester))
            ->get();

        $rekapPerMapel = [];
        foreach ($daftarPengampu as $pengampu) {
            $pengampu->setRelation('kelas', $kelas);
            $hasil = NilaiAkhirService::hitung($pengampu);
            $rekapPerMapel[$pengampu->id] = $hasil['siswa'];
        }

        return view('lms.guru.wali-kelas', compact(
            'kelasDiwalikan',
            'kelas',
            'daftarPengampu',
            'rekapPerMapel',
            'periodeList',
            'tahunAjaran',
            'semester'
        ));
    }
    /** Rekap absensi per bulan + keseluruhan, buat wali kelas. */
   /** Rekap absensi per bulan + keseluruhan + detail harian, buat wali kelas. */
public function absensi(Request $request)
{
    $user = Auth::guard('lms')->user();

    $penugasanWali = WaliKelasPeriode::where('user_id', $user->id)
        ->with('kelas')
        ->orderByDesc('tahun_ajaran')
        ->orderByDesc('semester')
        ->get();

    abort_if($penugasanWali->isEmpty(), 403, 'Anda bukan wali kelas manapun.');

    $kelasDiwalikan = $penugasanWali->pluck('kelas')->unique('id')->values();

    $kelasId = (int) $request->query('kelas_id', $kelasDiwalikan->first()->id);
    $kelas = $kelasDiwalikan->firstWhere('id', $kelasId) ?? $kelasDiwalikan->first();
    $kelas->load('siswa');

    $periodeList = PengampuMapel::where('kelas_id', $kelas->id)
        ->select('tahun_ajaran', 'semester')
        ->distinct()
        ->orderByDesc('tahun_ajaran')
        ->orderByDesc('semester')
        ->get();

    $tahunAjaran = $request->query('tahun_ajaran', $periodeList->first()->tahun_ajaran ?? \App\Support\TahunAjaran::sekarang());
    $semester = $request->query('semester', $periodeList->first()->semester ?? \App\Support\TahunAjaran::semesterSekarang());

    $pengampuIds = PengampuMapel::where('kelas_id', $kelas->id)
        ->where('tahun_ajaran', $tahunAjaran)
        ->where('semester', $semester)
        ->pluck('id');

    // Normalisasi tanggal ke Y-m-d (jaga-jaga kalau kolomnya datetime)
    $presensi = DB::table('presensi_lms')
        ->whereIn('pengampu_mapel_id', $pengampuIds)
        ->get(['siswa_id', 'tanggal', 'status'])
        ->map(function ($p) {
            $p->tanggal = Carbon::parse($p->tanggal)->format('Y-m-d');
            return $p;
        });

    // "Hari Masuk" per bulan = tanggal unik yang punya presensi (mapel manapun)
    $hariMasukPerBulan = $presensi
        ->groupBy(fn($p) => Carbon::parse($p->tanggal)->format('Y-m'))
        ->map(fn($grup) => $grup->pluck('tanggal')->unique()->count())
        ->sortKeys();

    $totalHariMasuk = $presensi->pluck('tanggal')->unique()->count();
    $jumlahSiswa = $kelas->siswa->count();

    // Rekap KESELURUHAN KELAS per bulan
    $rekapKelasPerBulan = [];
    foreach ($hariMasukPerBulan as $bulan => $hariAktif) {
        $slot = $hariAktif * $jumlahSiswa;
        $presensiBulanIni = $presensi->filter(fn($p) => Carbon::parse($p->tanggal)->format('Y-m') === $bulan);

        $rekapKelasPerBulan[$bulan] = [
            'hari_aktif' => $hariAktif,
            'slot' => $slot,
            'hadir' => $h = $presensiBulanIni->where('status', 'Hadir')->count(),
            'izin' => $i = $presensiBulanIni->where('status', 'Izin')->count(),
            'sakit' => $s = $presensiBulanIni->where('status', 'Sakit')->count(),
            'alpa' => $a = $presensiBulanIni->where('status', 'Alpa')->count(),
            'persen_hadir' => $slot > 0 ? round($h / $slot * 100, 1) : 0,
            'persen_izin' => $slot > 0 ? round($i / $slot * 100, 1) : 0,
            'persen_sakit' => $slot > 0 ? round($s / $slot * 100, 1) : 0,
            'persen_alpa' => $slot > 0 ? round($a / $slot * 100, 1) : 0,
        ];
    }

    $slotTotal = $totalHariMasuk * $jumlahSiswa;
    $rekapKelasTotal = [
        'slot' => $slotTotal,
        'hadir' => $h = $presensi->where('status', 'Hadir')->count(),
        'izin' => $i = $presensi->where('status', 'Izin')->count(),
        'sakit' => $s = $presensi->where('status', 'Sakit')->count(),
        'alpa' => $a = $presensi->where('status', 'Alpa')->count(),
        'persen_hadir' => $slotTotal > 0 ? round($h / $slotTotal * 100, 1) : 0,
        'persen_izin' => $slotTotal > 0 ? round($i / $slotTotal * 100, 1) : 0,
        'persen_sakit' => $slotTotal > 0 ? round($s / $slotTotal * 100, 1) : 0,
        'persen_alpa' => $slotTotal > 0 ? round($a / $slotTotal * 100, 1) : 0,
    ];

    // Rekap per siswa per bulan
    $rekap = [];
    foreach ($kelas->siswa as $siswa) {
        $presensiSiswa = $presensi->where('siswa_id', $siswa->id);

        $perBulan = [];
        foreach ($hariMasukPerBulan as $bulan => $hariMasuk) {
            $hadirBulanIni = $presensiSiswa
                ->filter(fn($p) => Carbon::parse($p->tanggal)->format('Y-m') === $bulan && $p->status === 'Hadir')
                ->pluck('tanggal')->unique()->count();

            $perBulan[$bulan] = [
                'hadir' => $hadirBulanIni,
                'hari_masuk' => $hariMasuk,
                'persen' => $hariMasuk > 0 ? round($hadirBulanIni / $hariMasuk * 100, 1) : 0,
            ];
        }

        $totalHadir = $presensiSiswa->where('status', 'Hadir')->pluck('tanggal')->unique()->count();

        $rekap[] = (object) [
            'siswa' => $siswa,
            'per_bulan' => $perBulan,
            'total_hadir' => $totalHadir,
            'total_hari_masuk' => $totalHariMasuk,
            'total_persen' => $totalHariMasuk > 0 ? round($totalHadir / $totalHariMasuk * 100, 1) : 0,
        ];
    }

    // ── DETAIL HARIAN (siswa x tanggal) untuk 1 bulan terpilih ──────────
    $bulanTersedia = $hariMasukPerBulan->keys();
    $bulanDipilih = $request->query('bulan');
    if (! $bulanTersedia->contains($bulanDipilih)) {
        $bulanDipilih = $bulanTersedia->last(); // default: bulan terbaru
    }

    $tanggalList = collect();
    $detailHarian = []; // [siswa_id][Y-m-d] = ['status' => 'Hadir', 'rincian' => 'Hadir 3, Alpa 1']

    if ($bulanDipilih) {
        $prioritas = ['Alpa' => 4, 'Sakit' => 3, 'Izin' => 2, 'Hadir' => 1];

        $presensiBulan = $presensi->filter(fn($p) => str_starts_with($p->tanggal, $bulanDipilih));
        $tanggalList = $presensiBulan->pluck('tanggal')->unique()->sort()->values();

        foreach ($presensiBulan->groupBy('siswa_id') as $siswaId => $barisSiswa) {
            foreach ($barisSiswa->groupBy('tanggal') as $tgl => $barisHari) {
                $statusList = $barisHari->pluck('status');

                $detailHarian[$siswaId][$tgl] = [
                    'status' => $statusList
                        ->sortByDesc(fn($st) => $prioritas[$st] ?? 0)
                        ->first(),
                    'rincian' => $statusList->countBy()
                        ->map(fn($n, $st) => "$st $n")
                        ->implode(', '),
                ];
            }
        }
    }

    return view('lms.guru.wali-kelas-absensi', compact(
        'kelasDiwalikan',
        'kelas',
        'periodeList',
        'tahunAjaran',
        'semester',
        'hariMasukPerBulan',
        'totalHariMasuk',
        'rekap',
        'rekapKelasPerBulan',
        'rekapKelasTotal',
        'bulanDipilih',
        'tanggalList',
        'detailHarian'
    ));
}
}
