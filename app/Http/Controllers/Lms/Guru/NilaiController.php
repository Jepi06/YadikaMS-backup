<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\BobotNilai;
use App\Models\Lms\NilaiSikap;
use App\Models\Lms\NilaiUjian;
use App\Models\Lms\PengampuMapel;
use App\Services\Lms\NilaiAkhirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NilaiController extends Controller
{
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    /** Rekap nilai lengkap: tugas, STS, SAS, sikap -> nilai akhir. */
    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas.siswa');

        $rekap = NilaiAkhirService::hitung($pengampuMapel);

        return view('lms.guru.nilai', [
            'pengampuMapel' => $pengampuMapel,
            'bobot' => $rekap['bobot'],
            'rekapSiswa' => $rekap['siswa'],
        ]);
    }

    /** Simpan bobot penilaian (harus total 100%). */
    public function simpanBobot(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'bobot_tugas' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_sts' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_sas' => ['required', 'integer', 'min:0', 'max:100'],
            'bobot_sikap' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $total = $data['bobot_tugas'] + $data['bobot_sts'] + $data['bobot_sas'] + $data['bobot_sikap'];

        if ($total !== 100) {
            return back()
                ->withErrors(['bobot_tugas' => "Total bobot harus tepat 100% (sekarang {$total}%)."])
                ->withInput();
        }

        BobotNilai::updateOrCreate(['pengampu_mapel_id' => $pengampuMapel->id], $data);

        return back()->with('status', 'Bobot penilaian tersimpan.');
    }

    /** Simpan nilai sikap + STS + SAS seluruh siswa sekaligus (bulk). */
    public function simpanSikap(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'sikap' => ['required', 'array'],
            'sikap.*.predikat' => ['required', 'in:Sangat Baik,Baik,Cukup,Kurang'],
            'sikap.*.catatan' => ['nullable', 'string', 'max:255'],
            'ujian' => ['nullable', 'array'],
            'ujian.*.sts' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ujian.*.sas' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($data['sikap'] as $siswaId => $row) {
            NilaiSikap::updateOrCreate(
                ['pengampu_mapel_id' => $pengampuMapel->id, 'siswa_id' => $siswaId],
                ['predikat' => $row['predikat'], 'catatan' => $row['catatan'] ?? null]
            );
        }

        foreach ($data['ujian'] ?? [] as $siswaId => $row) {
            NilaiUjian::updateOrCreate(
                ['pengampu_mapel_id' => $pengampuMapel->id, 'siswa_id' => $siswaId],
                ['nilai_sts' => $row['sts'] ?? null, 'nilai_sas' => $row['sas'] ?? null]
            );
        }

        return back()->with('status', 'Nilai sikap, STS, dan SAS tersimpan.');
    }

    /** Export rekap nilai ke Excel — per tugas + STS/SAS/Sikap/Nilai Akhir. */
    public function exportExcel(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas.siswa');

        $rekap = NilaiAkhirService::hitung($pengampuMapel);
        $rekapSiswa = $rekap['siswa'];
        $bobot = $rekap['bobot'];

        $daftarTugas = DB::table('tugas')
            ->where('pengampu_mapel_id', $pengampuMapel->id)
            ->orderBy('batas_waktu')
            ->get(['id', 'judul']);

        $nilaiPerTugas = DB::table('pengumpulan_tugas')
            ->whereIn('tugas_id', $daftarTugas->pluck('id'))
            ->whereNotNull('dinilai_at')
            ->get(['siswa_id', 'tugas_id', 'nilai'])
            ->groupBy('siswa_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai');

        $namaMapel = $pengampuMapel->mataPelajaran->nama ?? '-';
        $namaKelas = $pengampuMapel->kelas->nama_kelas ?? '-';

        $sheet->setCellValue('A1', 'Rekap Nilai — ' . $namaMapel);
        $sheet->setCellValue('A2', 'Kelas: ' . $namaKelas . ' | Tahun Ajaran: ' . $pengampuMapel->tahun_ajaran . ' ' . $pengampuMapel->semester);
        $sheet->setCellValue('A3', "Bobot: Tugas {$bobot->bobot_tugas}% | STS {$bobot->bobot_sts}% | SAS {$bobot->bobot_sas}% | Sikap {$bobot->bobot_sikap}%");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);

        $baseRow = 5;

        $header = ['No', 'Nama Siswa'];
        foreach ($daftarTugas as $tugas) {
            $header[] = $tugas->judul;
        }
        $header = array_merge($header, ['Rata Tugas', 'STS', 'SAS', 'Sikap', 'Nilai Akhir', 'Ket.']);

        foreach ($header as $i => $label) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $baseRow, $label);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex(count($header));
        $headerRange = 'A' . $baseRow . ':' . $lastColLetter . $baseRow;

        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('0D47A1');
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $row = $baseRow + 1;
        foreach ($pengampuMapel->kelas->siswa as $i => $siswa) {
            $col = 1;
            $sheet->setCellValueByColumnAndRow($col++, $row, $i + 1);
            $sheet->setCellValueByColumnAndRow($col++, $row, $siswa->nama);

            $nilaiSiswaTugas = $nilaiPerTugas->get($siswa->id, collect())->keyBy('tugas_id');
            foreach ($daftarTugas as $tugas) {
                $n = $nilaiSiswaTugas->get($tugas->id);
                $sheet->setCellValueByColumnAndRow($col++, $row, $n ? (float) $n->nilai : '-');
            }

            $r = $rekapSiswa->get($siswa->id);
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->rata_tugas !== null ? round($r->rata_tugas, 1) : '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->nilai_sts ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->nilai_sas ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->sikap_predikat ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->nilai_akhir);
            $sheet->setCellValueByColumnAndRow($col++, $row, $r->lengkap ? 'Lengkap' : 'Belum Lengkap');

            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A{$baseRow}:{$lastColLetter}{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', $lastColLetter) as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $namaFile = 'Rekap-Nilai-' . str_replace(' ', '-', $namaMapel) . '-' . str_replace(' ', '-', $namaKelas) . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
