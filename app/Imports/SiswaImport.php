<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaImport implements ToCollection, WithChunkReading, SkipsEmptyRows
{
    public ?int $kelasId = null;

    private int   $importedCount = 0;
    private array $skippedRows   = [];
    private array $errors        = [];

    public function collection(Collection $rows): void
    {
        $kelas = isset($this->kelasId)
            ? Kelas::find($this->kelasId)
            : null;

        $roleSiswaId = \DB::table('roles')
            ->join('modules', 'modules.id', '=', 'roles.module_id')
            ->where('modules.kode', 'lms')
            ->where('roles.kode', 'siswa')
            ->value('roles.id');

        // Cari baris heading
        $dataStartIndex = null;
        foreach ($rows as $index => $row) {
            if (strtolower(trim((string) $row[0])) === 'nis') {
                $dataStartIndex = $index + 1;
                break;
            }
        }

        if ($dataStartIndex === null) {
            $this->errors[] = 'Heading "nis" tidak ditemukan. Pastikan format template benar.';
            return;
        }

        foreach ($rows as $index => $row) {
            if ($index < $dataStartIndex) continue;

            $nis    = trim((string) ($row[0] ?? ''));
            $nama   = trim((string) ($row[1] ?? ''));
            $jk     = strtoupper(trim((string) ($row[2] ?? '')));
            $alamat = trim((string) ($row[3] ?? ''));
            $noHp   = trim((string) ($row[4] ?? ''));
            $status = trim((string) ($row[5] ?? ''));

            if (empty($nis) || empty($nama)) continue;

            // Skip baris yang sudah ditandai imported
            if (str_contains($status, 'Sudah diimport')) continue;

            $jkNorm = $this->normalizeJK($jk);
            if (!$jkNorm) {
                $this->errors[] = "NIS {$nis}: jenis kelamin '{$jk}' tidak dikenali.";
                continue;
            }

            if (\App\Models\Siswa::where('nis', $nis)->exists()) {
                $this->skippedRows[] = ['nis' => $nis, 'nama' => $nama, 'alasan' => 'NIS duplikat'];
                continue;
            }

            if (!$kelas) {
                $this->errors[] = "NIS {$nis}: kelas tidak ditemukan.";
                continue;
            }

            \DB::transaction(function () use ($nis, $nama, $jkNorm, $alamat, $noHp, $kelas, $roleSiswaId) {
                $userId = \DB::table('users')->insertGetId([
                    'name'       => $nama,
                    'email'      => $nis,
                    'password'   => \Hash::make('password'),
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($roleSiswaId) {
                    \DB::table('user_role')->insert([
                        'user_id'     => $userId,
                        'role_id'     => $roleSiswaId,
                        'assigned_at' => now(),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }

                \App\Models\Siswa::create([
                    'user_id'       => $userId,
                    'nis'           => $nis,
                    'nama'          => $nama,
                    'jenis_kelamin' => $jkNorm,
                    'alamat'        => $alamat ?: null,
                    'no_hp'         => $noHp   ?: null,
                    'kelas_id'      => $kelas->id,
                ]);
            });

            $this->importedCount++;
        }
    }

    private function normalizeJK(string $raw): ?string
    {
        $map = [
            'L'         => 'L',
            'LAKI'      => 'L',
            'LAKI-LAKI' => 'L',
            'LAKI LAKI' => 'L',
            'P'         => 'P',
            'PEREMPUAN' => 'P',
            'WANITA'    => 'P',
        ];
        return $map[strtoupper($raw)] ?? null;
    }

    public function getImportedCount(): int { return $this->importedCount; }
    public function getSkippedRows(): array  { return $this->skippedRows; }
    public function getErrors(): array       { return $this->errors; }
    public function chunkSize(): int         { return 500; }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        $semuaKelas = Kelas::with(['jurusan', 'waliKelas', 'siswa'])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $sheetIndex = 0;

        foreach ($semuaKelas as $kelas) {
            $sheet = $sheetIndex === 0
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet();

            $sheet->setTitle(substr($kelas->nama_kelas, 0, 31));

            $sheet->setCellValue('A1', 'Kelas');     $sheet->setCellValue('B1', $kelas->nama_kelas);
            $sheet->setCellValue('A2', 'Jurusan');   $sheet->setCellValue('B2', $kelas->jurusan->nama ?? '-');
            $sheet->setCellValue('A3', 'Wali Kelas');$sheet->setCellValue('B3', $kelas->waliKelas->name ?? 'Belum ada');
            $sheet->setCellValue('A4', 'Tingkat');   $sheet->setCellValue('B4', $kelas->tingkat);

            $sheet->getStyle('A1:A4')->getFont()->setBold(true);
            $sheet->getStyle('A1:B4')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('EFF6FF');

            $headers = ['A6' => 'nis', 'B6' => 'nama', 'C6' => 'jenis_kelamin', 'D6' => 'alamat', 'E6' => 'no_hp', 'F6' => 'status'];
            foreach ($headers as $cell => $label) {
                $sheet->setCellValue($cell, $label);
                $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle($cell)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('1D4ED8');
            }

            $row = 7;
            foreach ($kelas->siswa as $siswa) {
                $sheet->fromArray([
                    $siswa->nis,
                    $siswa->nama,
                    $siswa->jenis_kelamin,
                    $siswa->alamat ?? '',
                    $siswa->no_hp ?? '',
                    '✓ Sudah diimport',
                ], null, "A{$row}");

                $sheet->getStyle("A{$row}:F{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('DCFCE7');
                $sheet->getStyle("F{$row}")->getFont()->setBold(true)->getColor()->setRGB('15803D');
                $row++;
            }

            for ($i = 0; $i < 10; $i++) {
                $sheet->setCellValue("F{$row}", '← isi data baru di sini');
                $sheet->getStyle("F{$row}")->getFont()->setItalic(true)->getColor()->setRGB('9CA3AF');
                $row++;
            }

            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            $sheet->freezePane('A7');
            $sheetIndex++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template_import_siswa_perkelas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}