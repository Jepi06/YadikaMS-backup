<?php

namespace App\Models;

use App\Models\Lms\PengampuMapel;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan_id',
        'wali_kelas_id',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function pengampuMapel(): HasMany
    {
        return $this->hasMany(PengampuMapel::class, 'kelas_id');
    }

    // ── Accessor ──────────────────────────────────────────────────────────────

    public function getLabelAttribute(): string
    {
        return "{$this->nama_kelas} (" . ($this->jurusan->kode ?? '-') . ")";
    }
}