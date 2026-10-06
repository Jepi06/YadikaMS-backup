<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'pengampu_mapel_id',
        'judul',
        'deskripsi',
        'file_lampiran',
        'batas_waktu',
        'is_kelompok',
        'mode_buka',
        'dibuka_manual',
        'mulai_pada',
        'ditutup_manual',
    ];

    protected $casts = [
        'batas_waktu' => 'datetime',
        'mulai_pada' => 'datetime',
        'dibuka_manual' => 'boolean',
        'ditutup_manual' => 'boolean',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    public function getSudahLewatBatasWaktuAttribute(): bool
    {
        return $this->batas_waktu?->isPast() ?? false;
    }
    /** Null = masih bisa dikumpulkan. String = alasan ditolak. */
    public function alasanTidakBisaKumpul(): ?string
    {
        if ($this->ditutup_manual) {
            return 'Tugas ini sudah ditutup oleh guru.';
        }

        if (now()->gt($this->batas_waktu)) {
            return 'Sudah melewati batas waktu (' . $this->batas_waktu->translatedFormat('d M Y, H:i') . ').';
        }

        switch ($this->mode_buka) {
            case 'manual':
                return $this->dibuka_manual ? null : 'Tugas ini belum dibuka oleh guru.';
            case 'tanggal':
                if ($this->mulai_pada && now()->lt($this->mulai_pada)) {
                    return 'Tugas dibuka mulai ' . $this->mulai_pada->translatedFormat('d M Y, H:i') . '.';
                }
                return null;
            default:
                return null;
        }
    }
    public function kelompok()
    {
        return $this->hasMany(TugasKelompok::class);
    }
}
