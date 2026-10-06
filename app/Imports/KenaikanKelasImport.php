<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Support\TahunAjaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KenaikanKelasImport implements SkipsEmptyRows, ToCollection, WithChunkReading
{
    private int $updatedCount = 0;

    private int $lulusCount = 0;

    private array $skipped = [];

    private array $errors = [];

    private array $preview = [];

    private bool $dryRun = false;

    public function setDryRun(bool $dryRun): self
    {
        $this->dryRun = $dryRun;

        return $this;
    }

    public function collection(Collection $rows): void
    {
        $allKelas = Kelas::get()->keyBy(fn ($k) => strtolower(trim($k->nama_kelas)));

        // Baris 1 (index 0): A=Kelas, B=nama_kelas_lama, F=Kelas Baru, G=nama_kelas_baru
        // Tapi karena ToCollection, kolom diakses by index: 0=A, 1=B, ..., 5=F, 6=G
        $kelasLamaNama = strtolower(trim((string) ($rows[0][1] ?? '')));
        $kelasBaru = strtolower(trim((string) ($rows[0][6] ?? '')));
        $status = strtolower(trim((string) ($rows[1][6] ?? 'aktif')));

        $kelasLama = $allKelas[$kelasLamaNama] ?? null;
        if (! $kelasLama) {
            $this->errors[] = "Kelas lama '{$kelasLamaNama}' tidak ditemukan.";

            return;
        }

        // Cari heading baris nis
        $dataStartIndex = null;
        foreach ($rows as $index => $row) {
            if (strtolower(trim((string) $row[0])) === 'nis') {
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if ($dataStartIndex === null) {
            $this->errors[] = "Heading 'nis' tidak ditemukan.";

            return;
        }

        // Tandai lulus/keluar
        if (in_array($status, ['lulus', 'keluar'])) {
            foreach ($rows as $index => $row) {
                if ($index < $dataStartIndex) {
                    continue;
                }

                $nis = trim((string) ($row[0] ?? ''));
                if (empty($nis)) {
                    continue;
                }

                $siswa = Siswa::where('nis', $nis)->first();
                if (! $siswa) {
                    $this->skipped[] = ['nis' => $nis, 'alasan' => 'NIS tidak ditemukan'];

                    continue;
                }

                $this->preview[] = [
                    'nis' => $nis,
                    'nama' => $siswa->nama,
                    'kelas_lama' => $kelasLama->nama_kelas,
                    'kelas_baru' => '-',
                    'aksi' => strtoupper($status),
                ];

                if (! $this->dryRun) {
                    $siswa->update([
                        'status' => $status,
                        'tahun_lulus' => TahunAjaran::sekarang(),
                    ]);
                }
                $this->lulusCount++;
            }

            return;
        }

        // Naik kelas
        $objKelasBaru = $allKelas[$kelasBaru] ?? null;
        if (! $objKelasBaru) {
            $this->errors[] = "Kelas baru '{$kelasBaru}' tidak ditemukan. Pastikan cell G1 sudah diisi.";

            return;
        }

        foreach ($rows as $index => $row) {
            if ($index < $dataStartIndex) {
                continue;
            }

            $nis = trim((string) ($row[0] ?? ''));
            if (empty($nis)) {
                continue;
            }

            $siswa = Siswa::where('nis', $nis)->first();
            if (! $siswa) {
                $this->skipped[] = ['nis' => $nis, 'alasan' => 'NIS tidak ditemukan'];

                continue;
            }

            $this->preview[] = [
                'nis' => $nis,
                'nama' => $siswa->nama,
                'kelas_lama' => $kelasLama->nama_kelas,
                'kelas_baru' => $objKelasBaru->nama_kelas,
                'aksi' => 'NAIK KELAS',
            ];

            if (! $this->dryRun) {
                $siswa->update(['kelas_id' => $objKelasBaru->id, 'status' => 'aktif']);
            }
            $this->updatedCount++;
        }
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getLulusCount(): int
    {
        return $this->lulusCount;
    }

    public function getSkipped(): array
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getPreview(): array
    {
        return $this->preview;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function downloadTemplate(int $kelasId): StreamedResponse
    {
        $kelas = Kelas::with(['jurusan', 'waliKelas', 'siswa'])->findOrFail($kelasId);
        $siswaDiKelas = $kelas->siswa->where('status', 'aktif');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($kelas->nama_kelas, 0, 31));

        // ── Info kelas baris 1-4 ─────────────────────────────────────────
        $sheet->setCellValue('A1', 'Kelas');
        $sheet->setCellValue('B1', $kelas->nama_kelas);
        $sheet->setCellValue('A2', 'Jurusan');
        $sheet->setCellValue('B2', $kelas->jurusan->nama ?? '-');
        $sheet->setCellValue('A3', 'Wali Kelas');
        $sheet->setCellValue('B3', $kelas->waliKelas->name ?? 'Belum ada');
        $sheet->setCellValue('A4', 'Tingkat');
        $sheet->setCellValue('B4', $kelas->tingkat);

        // Kolom F: info kenaikan
        $sheet->setCellValue('F1', 'Kelas Baru');
        $sheet->setCellValue('G1', ''); // ← admin isi di sini
        $sheet->setCellValue('F2', 'Status');
        $sheet->setCellValue('G2', $kelas->tingkat === 'XII' ? 'lulus' : 'aktif');

        // Style info kelas
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A1:B4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('EFF6FF');

        // Style info kenaikan
        $sheet->getStyle('F1:F2')->getFont()->setBold(true);
        $sheet->getStyle('F1:G2')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB($kelas->tingkat === 'XII' ? 'FEF9C3' : 'DCFCE7');

        // Highlight G1 agar admin tahu harus diisi
        $sheet->getStyle('G1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FEE2E2');
        $sheet->getStyle('G1')->getFont()->setBold(true)->getColor()->setRGB('DC2626');

        // ── Heading baris 6 ──────────────────────────────────────────────
        $headers = [
            'A6' => 'nis',
            'B6' => 'nama',
            'C6' => 'jenis_kelamin',
            'D6' => 'alamat',
            'E6' => 'no_hp',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1D4ED8');
        }

        // ── Data siswa mulai baris 7 ─────────────────────────────────────
        $row = 7;
        foreach ($siswaDiKelas as $siswa) {
            $sheet->setCellValue("A{$row}", $siswa->nis);
            $sheet->setCellValue("B{$row}", $siswa->nama);
            $sheet->setCellValue("C{$row}", $siswa->jenis_kelamin);
            $sheet->setCellValue("D{$row}", $siswa->alamat ?? '');
            $sheet->setCellValue("E{$row}", $siswa->no_hp ?? '');
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A7');

        $namaFile = 'kenaikan_kelas_'.str_replace(' ', '_', $kelas->nama_kelas).'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
