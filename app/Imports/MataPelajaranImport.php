<?php

namespace App\Imports;

use App\Models\Jurusan;
use App\Models\Lms\MataPelajaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MataPelajaranImport implements ToCollection, WithChunkReading, SkipsEmptyRows
{
    private int   $importedCount = 0;
    private array $skippedRows   = [];
    private array $errors        = [];

    public function collection(Collection $rows): void
    {
        // Cache semua jurusan by kode (uppercase)
        $jurusanMap = Jurusan::all()->keyBy(fn($j) => strtoupper(trim($j->kode)));

        // Cari baris heading
        $dataStartIndex = null;
        foreach ($rows as $index => $row) {
            if (strtolower(trim((string) $row[0])) === 'kode') {
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if ($dataStartIndex === null) {
            $this->errors[] = 'Heading "kode" tidak ditemukan. Pastikan format template benar.';
            return;
        }

        foreach ($rows as $index => $row) {
            if ($index < $dataStartIndex) continue;

            $kode       = strtoupper(trim((string) ($row[0] ?? '')));
            $nama       = trim((string) ($row[1] ?? ''));
            $jurusanKode= strtoupper(trim((string) ($row[2] ?? '')));

            if (empty($kode) || empty($nama)) continue;

            // Validasi panjang kode
            if (strlen($kode) > 20) {
                $this->errors[] = "Kode '{$kode}': melebihi 20 karakter.";
                continue;
            }

            // Cek duplikat kode
            if (MataPelajaran::where('kode', $kode)->exists()) {
                $this->skippedRows[] = [
                    'kode'   => $kode,
                    'nama'   => $nama,
                    'alasan' => 'Kode sudah terdaftar',
                ];
                continue;
            }

            // Resolusi jurusan
            $jurusanId = null;
            if (!empty($jurusanKode)) {
                $jurusan = $jurusanMap[$jurusanKode] ?? null;
                if (!$jurusan) {
                    $this->errors[] = "Kode '{$kode}': jurusan '{$jurusanKode}' tidak ditemukan. Baris dilewati.";
                    continue;
                }
                $jurusanId = $jurusan->id;
            }

            MataPelajaran::create([
                'kode'       => $kode,
                'nama'       => $nama,
                'jurusan_id' => $jurusanId,
            ]);

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedRows(): array  { return $this->skippedRows; }
    public function getErrors(): array       { return $this->errors; }
    public function chunkSize(): int         { return 500; }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mata Pelajaran');

        // Heading
        $headers = ['A1' => 'kode', 'B1' => 'nama', 'C1' => 'jurusan'];
        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1D4ED8');
        }

        // Contoh data
        $contoh = [
            ['MTK',      'Matematika',                    ''],
            ['BIN',      'Bahasa Indonesia',              ''],
            ['PPLG-PW',  'Pemrograman Web',               'RPL'],
            ['PPLG-BD',  'Basis Data',                    'RPL'],
            ['AK-AD',    'Akuntansi Dasar',               'AKL'],
            ['HTL-FO',   'Front Office',                  'PHT'],
        ];

        $row = 2;
        foreach ($contoh as $c) {
            $sheet->fromArray($c, null, "A{$row}");
            // Style baris contoh
            $sheet->getStyle("A{$row}:C{$row}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F8FAFC');
            $row++;
        }

        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        // Sheet petunjuk
        $sheetInfo = $spreadsheet->createSheet();
        $sheetInfo->setTitle('Petunjuk');

        $petunjuk = [
            ['Kolom',    'Wajib?',  'Keterangan'],
            ['kode',     'YA',      'Kode unik mapel, maks 20 karakter. Akan diuppercase otomatis.'],
            ['nama',     'YA',      'Nama lengkap mata pelajaran.'],
            ['jurusan',  'TIDAK',   'Kode jurusan (RPL/AKL/PHT). Kosongkan jika mapel umum (semua jurusan).'],
        ];

        foreach ($petunjuk as $i => $rowData) {
            $sheetInfo->fromArray($rowData, null, 'A' . ($i + 1));
            if ($i === 0) {
                $sheetInfo->getStyle('A1:C1')->getFont()->setBold(true);
            }
        }

        // Sheet referensi jurusan
        $sheetJurusan = $spreadsheet->createSheet();
        $sheetJurusan->setTitle('Daftar Jurusan');
        $sheetJurusan->setCellValue('A1', 'kode');
        $sheetJurusan->setCellValue('B1', 'nama');
        $sheetJurusan->getStyle('A1:B1')->getFont()->setBold(true);

        $semuaJurusan = Jurusan::orderBy('nama')->get();
        $jr = 2;
        foreach ($semuaJurusan as $j) {
            $sheetJurusan->setCellValue("A{$jr}", $j->kode);
            $sheetJurusan->setCellValue("B{$jr}", $j->nama);
            $jr++;
        }
        foreach (['A', 'B'] as $col) {
            $sheetJurusan->getColumnDimension($col)->setAutoSize(true);
            $sheetInfo->getColumnDimension($col)->setAutoSize(true);
        }
        $sheetInfo->getColumnDimension('C')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template_import_mata_pelajaran.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}