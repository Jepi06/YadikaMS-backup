<?php

namespace App\Models;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jurusan extends Model
{
    protected $table = 'jurusan';

    protected $fillable = [
        'kode',
        'nama',
        'kepala_jurusan_id',
        'deskripsi',
        'kuota',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function kepalaJurusan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kepala_jurusan_id');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    /** Dipakai modul SPMB */
    public function pendaftar(): HasMany
    {
        return $this->hasMany(\App\Models\SPMB\Pendaftar::class, 'jurusan_id');
    }

    public function pendaftarDiterima(): HasMany
    {
        return $this->hasMany(\App\Models\SPMB\Pendaftar::class, 'jurusan_id')
            ->where('status', \App\Models\SPMB\Pendaftar::STATUS_DITERIMA);
    }

    // ── Accessors (SPMB) ──────────────────────────────────────────────────────

    public function getTotalDiterimaAttribute(): int
    {
        return $this->pendaftar()->where('status', \App\Models\SPMB\Pendaftar::STATUS_DITERIMA)->count();
    }

    public function getTotalPendaftarAttribute(): int
    {
        return $this->pendaftar()->count();
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota - $this->total_diterima);
    }
}