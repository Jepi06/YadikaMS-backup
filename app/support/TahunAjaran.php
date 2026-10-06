<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Ngitung tahun ajaran & semester OTOMATIS dari tanggal hari ini,
 * pakai konvensi tahun ajaran sekolah Indonesia:
 *
 * - Juli s.d. Desember  -> Semester GANJIL, tahun ajaran = tahun ini / tahun ini+1
 * - Januari s.d. Juni   -> Semester GENAP,  tahun ajaran = tahun lalu / tahun ini
 *
 * Dipakai di seeder (biar gak hardcode "2024/2025" terus) dan di
 * form mana pun yang butuh nilai default tahun ajaran/semester.
 */
class TahunAjaran
{
    /** Contoh: "2026/2027" */
    public static function sekarang(): string
    {
        $now = Carbon::now();

        if ($now->month >= 7) {
            return $now->year . '/' . ($now->year + 1);
        }

        return ($now->year - 1) . '/' . $now->year;
    }

    /** "Ganjil" atau "Genap" */
    public static function semesterSekarang(): string
    {
        return Carbon::now()->month >= 7 ? 'Ganjil' : 'Genap';
    }

    /** Sekaligus dua-duanya: ['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil'] */
    public static function sekarangArray(): array
    {
        return [
            'tahun_ajaran' => self::sekarang(),
            'semester' => self::semesterSekarang(),
        ];
    }
}
