<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\JadwalPelajaran;
use App\Models\Lms\MataPelajaran;
use App\Models\Lms\PengampuMapel;
use App\Models\User;
use App\Support\JamPelajaran;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JadwalImportController extends Controller
{
    private const HEADER = ['Hari', 'Guru (nama / email)', 'Mata Pelajaran', 'Kelas', 'JP Ke', 'Jumlah JP', 'Ruangan (opsional)', 'Durasi (info)'];

    public function form()
    {
        return view('admin.jadwal.import', ['grid' => JamPelajaran::hitung()['semua']]);
    }

    /** Template kosong + 3 contoh baris. */
    public function template()
    {
        return $this->unduh('Template-Import-Jadwal.xlsx', [
            ['Senin',  'Budi Santoso, S.Kom', 'Pemrograman Web',  'XI RPL 1',  1, 2, 'Lab RPL 1'],
            ['Senin',  'Budi Santoso, S.Kom', 'Pemrograman Web',  'XII RPL 1', 4, 3, 'Lab RPL 1'],
            ['Selasa', 'Siti Aminah, S.Pd',   'Bahasa Indonesia', 'XI RPL 1',  1, 2, ''],
        ]);
    }

    /** Export jadwal periode aktif, formatnya sama dengan template (bisa diedit lalu diimport lagi). */
    public function export()
    {
        $slots = JamPelajaran::hitung()['jp'];

        $data = JadwalPelajaran::with(['pengampuMapel.guru', 'pengampuMapel.mataPelajaran', 'pengampuMapel.kelas'])
            ->whereHas('pengampuMapel', fn ($q) => $q
                ->where('tahun_ajaran', TahunAjaran::sekarang())
                ->where('semester', TahunAjaran::semesterSekarang()))
            ->orderBy('hari')->orderBy('jam_mulai')
            ->get()
            ->map(function ($j) use ($slots) {
                $jpMulai = $j->jp_mulai;
                $jumlah  = $j->jumlah_jp;

                // jadwal lama / input manual: tebak dari jam
                if (! $jpMulai) {
                    $m = substr($j->jam_mulai, 0, 5);
                    $s = substr($j->jam_selesai, 0, 5);
                    $jpMulai = collect($slots)->firstWhere('mulai', $m)['jp'] ?? null;
                    $jumlah  = collect($slots)->filter(fn ($x) => $x['mulai'] >= $m && $x['selesai'] <= $s)->count() ?: null;
                }

                $p = $j->pengampuMapel;

                return [
                    JadwalPelajaran::HARI[$j->hari] ?? $j->hari,
                    $p->guru->name ?? '',
                    $p->mataPelajaran->nama ?? '',
                    $p->kelas->nama_kelas ?? '',
                    $jpMulai, $jumlah, $j->ruangan,
                ];
            })->all();

        return $this->unduh('Jadwal-Guru-' . now()->format('Ymd') . '.xlsx', $data);
    }

  public function proses(Request $request)
{
    $request->validate([
        'file'  => ['required', 'file', 'mimes:xlsx,xls'],
        'ganti' => ['nullable', 'boolean'],
    ]);

    $tahunAjaran = TahunAjaran::sekarang();
    $semester = TahunAjaran::semesterSekarang();

    $hariMap = collect(JadwalPelajaran::HARI)
        ->mapWithKeys(fn ($nama, $no) => [strtolower($nama) => $no])->all();

    $book  = IOFactory::load($request->file('file')->getRealPath());
    $sheet = $book->getSheetByName('Jadwal') ?? $book->getActiveSheet();
    $rows  = $sheet->toArray(null, true, true, true);

    $hasil = [];
    $gagal = [];

    DB::transaction(function () use ($request, $rows, $hariMap, $tahunAjaran, $semester, &$hasil, &$gagal) {

        if ($request->boolean('ganti')) {
            JadwalPelajaran::whereHas('pengampuMapel', fn ($q) => $q
                ->where('tahun_ajaran', $tahunAjaran)->where('semester', $semester))
                ->delete();
        }

        foreach ($rows as $i => $row) {
            if ($i === 1) {
                continue;
            }

            $hariTxt = strtolower(trim((string) ($row['A'] ?? '')));
            $guruTxt = trim((string) ($row['B'] ?? ''));
            if ($hariTxt === '' && $guruTxt === '') {
                continue;
            }

            $mapelTxt = trim((string) ($row['C'] ?? ''));
            $kelasTxt = trim((string) ($row['D'] ?? ''));
            $jpMulai  = (int) round((float) ($row['E'] ?? 0));
            $jumlah   = (int) round((float) ($row['F'] ?? 0));
            $ruangan  = trim((string) ($row['G'] ?? '')) ?: null;

            $hari = $hariMap[$hariTxt]
                ?? (ctype_digit($hariTxt) && isset(JadwalPelajaran::HARI[(int) $hariTxt]) ? (int) $hariTxt : null);

            if (! $hari) {
                $gagal[] = "Baris {$i}: hari '{$row['A']}' tidak dikenali.";
                continue;
            }

            $blok = JamPelajaran::blok($jpMulai, $jumlah);
            if (! $blok) {
                $gagal[] = "Baris {$i}: JP ke {$jpMulai} sebanyak {$jumlah} JP tidak valid (maksimal sampai JP " . JamPelajaran::JUMLAH_JP . ').';
                continue;
            }

            $guru  = User::where('email', $guruTxt)->orWhereRaw('LOWER(name) = ?', [strtolower($guruTxt)])->first();
            $mapel = MataPelajaran::whereRaw('LOWER(nama) = ?', [strtolower($mapelTxt)])->first();
            $kelas = Kelas::whereRaw('LOWER(nama_kelas) = ?', [strtolower($kelasTxt)])->first();

            if (! $guru)  { $gagal[] = "Baris {$i}: guru '{$guruTxt}' tidak ditemukan."; continue; }
            if (! $mapel) { $gagal[] = "Baris {$i}: mata pelajaran '{$mapelTxt}' tidak ditemukan."; continue; }
            if (! $kelas) { $gagal[] = "Baris {$i}: kelas '{$kelasTxt}' tidak ditemukan."; continue; }

            $pengampu = PengampuMapel::firstOrCreate([
                'guru_id'           => $guru->id,
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id'          => $kelas->id,
                'tahun_ajaran'      => $tahunAjaran,
                'semester'          => $semester,
            ]);

            $existing = JadwalPelajaran::where('pengampu_mapel_id', $pengampu->id)
                ->where('hari', $hari)->where('jam_mulai', $blok['mulai'])->first();

            if ($pesan = $this->bentrok($guru->id, $kelas->id, $hari, $blok['mulai'], $blok['selesai'], $existing?->id, $tahunAjaran, $semester)) {
                $gagal[] = "Baris {$i}: {$pesan}";
                continue;
            }

            $payload = [
                'jp_mulai'    => $jpMulai,
                'jumlah_jp'   => $jumlah,
                'jam_selesai' => $blok['selesai'],
                'ruangan'     => $ruangan,
            ];

            $existing
                ? $existing->update($payload)
                : JadwalPelajaran::create($payload + [
                    'pengampu_mapel_id' => $pengampu->id,
                    'hari'              => $hari,
                    'jam_mulai'         => $blok['mulai'],
                ]);

            $hasil[] = "{$guru->name} · {$mapel->nama} · {$kelas->nama_kelas} — "
                . JadwalPelajaran::HARI[$hari] . " JP {$jpMulai} ({$jumlah} JP = {$blok['menit']} menit, {$blok['mulai']}–{$blok['selesai']})";
        }
    });

    return view('admin.jadwal.import-hasil', compact('hasil', 'gagal'));
}
    // ── helper ─────────────────────────────────────────────

    private function unduh(string $nama, array $baris)
    {
        $menitJp = \App\Models\Lms\PengaturanJam::ambil()->menit_jp;

        $book  = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Jadwal');
        $sheet->fromArray(self::HEADER, null, 'A1');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0D47A1');

        $r = 2;
        foreach ($baris as $b) {
            $sheet->fromArray(array_slice($b, 0, 7), null, "A{$r}");
            // kolom H hanya info, tidak dibaca saat import
            $sheet->setCellValue("H{$r}", ! empty($b[5]) ? ($b[5] * $menitJp) . ' menit' : '');
            $r++;
        }
        foreach (range('A', 'H') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $ref = $book->createSheet();
        $ref->setTitle('Jam Pelajaran');
        $ref->fromArray(['JP', 'Mulai', 'Selesai'], null, 'A1');
        $ref->getStyle('A1:C1')->getFont()->setBold(true);
        $r = 2;
        foreach (JamPelajaran::hitung()['semua'] as $g) {
            $ref->setCellValue("A{$r}", $g['istirahat'] ? 'Istirahat' : $g['jp']);
            $ref->setCellValueExplicit("B{$r}", $g['mulai'], DataType::TYPE_STRING);
            $ref->setCellValueExplicit("C{$r}", $g['selesai'], DataType::TYPE_STRING);
            $r++;
        }

        $book->setActiveSheetIndex(0);
        $writer = new Xlsx($book);

        return response()->streamDownload(fn () => $writer->save('php://output'), $nama);
    }

    private function bentrok(int $guruId, int $kelasId, int $hari, string $mulai, string $selesai,
                             ?int $ignoreId, string $ta, string $sem): ?string
    {
        $irisan = fn () => JadwalPelajaran::where('hari', $hari)
            ->where('jam_mulai', '<', $selesai)
            ->where('jam_selesai', '>', $mulai)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId));

        $periode = fn ($q) => $q->where('tahun_ajaran', $ta)->where('semester', $sem);

        if ($irisan()->whereHas('pengampuMapel', fn ($q) => $periode($q)->where('guru_id', $guruId))->exists()) {
            return 'bentrok, guru sudah punya jadwal lain di jam tersebut.';
        }
        if ($irisan()->whereHas('pengampuMapel', fn ($q) => $periode($q)->where('kelas_id', $kelasId))->exists()) {
            return 'bentrok, kelas sudah punya pelajaran lain di jam tersebut.';
        }

        return null;
    }
}