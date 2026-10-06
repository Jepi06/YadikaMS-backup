<?php

namespace App\Models;

use App\Models\Mapping\PenempatanPkl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'kelas_id',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function penempatanPkl(): HasMany
    {
        return $this->hasMany(PenempatanPkl::class);
    }

    /**
     * Penempatan yang masih berjalan/aktif (bukan yang ditolak),
     * dipakai untuk menampilkan status pengajuan PKL siswa saat ini.
     */
    public function penempatanAktif(): HasOne
    {
        return $this->hasOne(PenempatanPkl::class)->whereNotIn('status', ['rejected'])->latest();
    }

    public function sudahDitempatkan(): bool
    {
        return $this->penempatanPkl()
            ->whereNotIn('status', ['rejected'])
            ->exists();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeSearch(Builder $q, ?string $keyword): Builder
    {
        if (!$keyword) return $q;

        return $q->where(function (Builder $sub) use ($keyword) {
            $sub->where('nama', 'like', "%{$keyword}%")
                ->orWhere('nis', 'like', "%{$keyword}%")
                ->orWhere('no_hp', 'like', "%{$keyword}%");
        });
    }

    public function scopeFilterKelas(Builder $q, ?string $kelasId): Builder
    {
        return $kelasId ? $q->where('kelas_id', $kelasId) : $q;
    }

    public function scopeFilterJurusan(Builder $q, ?string $jurusanId): Builder
    {
        return $jurusanId
            ? $q->whereHas('kelas', fn($k) => $k->where('jurusan_id', $jurusanId))
            : $q;
    }

    public function scopeFilterTingkat(Builder $q, ?string $tingkat): Builder
    {
        return $tingkat
            ? $q->whereHas('kelas', fn($k) => $k->where('tingkat', $tingkat))
            : $q;
    }

    /**
     * Scope: siswa yang BELUM pernah mengajukan PKL sama sekali,
     * atau seluruh pengajuannya ditolak (jadi status efektifnya "belum mengajukan").
     */
    public function scopeBelumMengajukan(Builder $query): Builder
    {
        return $query->whereDoesntHave('penempatanPkl', function ($q) {
            $q->whereNotIn('status', ['rejected']);
        });
    }

    /**
     * Scope: siswa yang sudah punya pengajuan aktif (draft/diajukan/approved).
     */
    public function scopeSudahMengajukan(Builder $query): Builder
    {
        return $query->whereHas('penempatanPkl', function ($q) {
            $q->whereNotIn('status', ['rejected']);
        });
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}