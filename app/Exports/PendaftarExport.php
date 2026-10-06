<?php

namespace App\Exports;

use App\Models\Jurusan;
use App\Models\SPMB\Pendaftar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendaftarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    /**
     * @param int|null $jurusanId Jika diisi, hanya mengekspor pendaftar
     *                            dari jurusan tersebut. Jika null, ekspor semua.
     */
    public function __construct(private ?int $jurusanId = null)
    {
    }

    /**
     * Seluruh data peserta didik baru (atau terfilter per jurusan),
     * lengkap dengan biodata dan nominal.
     */
    public function collection()
    {
        return Pendaftar::with('jurusan:id,nama')
            ->when($this->jurusanId, fn($q) => $q->where('jurusan_id', $this->jurusanId))
            ->orderBy('jurusan_id')
            ->orderBy('nama_lengkap')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Pendaftaran',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Alamat',
            'Agama',
            'Nama Orang Tua',
            'Asal Sekolah',
            'No. HP',
            'Jurusan',
            'Nominal Pembayaran',
            'Status',
            'Lunas',
            'Catatan Admin',
            'Tanggal Daftar',
        ];
    }

    public function map($pendaftar): array
    {
        return [
            $pendaftar->no_pendaftaran,
            $pendaftar->nama_lengkap,
            $pendaftar->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $pendaftar->alamat,
            $pendaftar->agama,
            $pendaftar->nama_orang_tua,
            $pendaftar->asal_sekolah,
            $pendaftar->no_hp,
            optional($pendaftar->jurusan)->nama,
            (float) $pendaftar->nominal_pembayaran,
            $pendaftar->status_label,
            $pendaftar->is_lunas ? 'Ya' : 'Belum',
            $pendaftar->catatan_admin,
            optional($pendaftar->created_at)->format('d-m-Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Nama sheet menyesuaikan: nama jurusan jika difilter, atau "Semua Pendaftar".
     */
    public function title(): string
    {
        if ($this->jurusanId) {
            $nama = Jurusan::find($this->jurusanId)?->nama ?? 'Jurusan';
            return substr($nama, 0, 31); // batas Excel: max 31 karakter nama sheet
        }

        return 'Semua Pendaftar';
    }
}
