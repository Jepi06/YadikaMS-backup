<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SesiPresensi extends Model
{
    /** QR berganti tiap 30 detik. */
    public const TOKEN_TTL = 30;

    /** Token masih diterima sampai 4 slot ke belakang (4 x 30 dtk = 2 menit), supaya siswa sempat scan. */
    public const TOKEN_GRACE_SLOTS = 4;

    protected $table = 'sesi_presensi_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'token',
        'tanggal',
        'dibuka_at',
        'ditutup_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'dibuka_at' => 'datetime',
        'ditutup_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (self $sesi) {
            $sesi->token = $sesi->token ?: (string) Str::uuid();
            $sesi->dibuka_at = $sesi->dibuka_at ?: now();
        });
    }

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    public function dibukaOleh()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function presensi()
    {
        return $this->hasMany(PresensiLms::class, 'sesi_presensi_id');
    }

    public function getMasihAktifAttribute(): bool
    {
        return $this->dibuka_at !== null
            && ($this->ditutup_at === null || $this->ditutup_at->isFuture());
    }

    /** Token QR yang berubah tiap TOKEN_TTL detik, ditandatangani HMAC. */
    public function tokenDinamis(?int $slot = null): string
    {
        $slot ??= intdiv(time(), self::TOKEN_TTL);

        return $this->token . '.' . $slot . '.' . $this->tandatangani($this->token, $slot);
    }

    /** Sisa detik sebelum token berganti. */
    public static function sisaDetik(): int
    {
        return self::TOKEN_TTL - (time() % self::TOKEN_TTL);
    }

    /** Cari sesi dari token dinamis. Return null kalau salah/kedaluwarsa. */
    public static function cariDariTokenDinamis(string $tokenDinamis): ?self
    {
        $parts = explode('.', $tokenDinamis);
        if (count($parts) < 3) {
            Log::warning('QR: format token salah', ['token' => $tokenDinamis]);
            return null;
        }

        $sig  = array_pop($parts);
        $slot = array_pop($parts);
        $base = implode('.', $parts);

        if (! ctype_digit($slot)) {
            Log::warning('QR: slot bukan angka', ['slot' => $slot]);
            return null;
        }

        $now = intdiv(time(), self::TOKEN_TTL);
        if ((int) $slot > $now || $now - (int) $slot > self::TOKEN_GRACE_SLOTS) {
            Log::warning('QR: slot di luar rentang', ['slot' => $slot, 'now' => $now]);
            return null;
        }

        $sesi = static::where('token', $base)->first();
        if (! $sesi) {
            Log::warning('QR: sesi tidak ditemukan', ['base' => $base]);
            return null;
        }

        if (! hash_equals($sesi->tandatangani($base, (int) $slot), $sig)) {
            Log::warning('QR: tanda tangan tidak cocok');
            return null;
        }

        return $sesi;
    }

    protected function tandatangani(string $base, int $slot): string
    {
        return substr(hash_hmac('sha256', $base . '|' . $slot, config('app.key')), 0, 16);
    }
}