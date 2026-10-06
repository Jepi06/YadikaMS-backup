<?php

namespace App\Models\Lms;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;

/**
 * Satu baris = status kehadiran 1 siswa pada 1 pertemuan (tanggal)
 * untuk 1 pengampu_mapel. Bisa diisi 2 cara:
 *  - 'barcode' : siswa scan QR/token yang ditampilkan guru saat sesi dibuka
 *  - 'manual'  : guru input/koreksi langsung (untuk siswa yg tidak scan, izin, sakit, dll)
 */
class PresensiLms extends Model
{
    protected $table = 'presensi_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'siswa_id',
        'sesi_presensi_id',
        'tanggal',
        'status',
        'sumber',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPresensi::class, 'sesi_presensi_id');
    }
}
