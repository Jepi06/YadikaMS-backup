<?php

namespace App\Support;

use App\Models\Lms\PengaturanJam;
use Illuminate\Support\Carbon;

class JamPelajaran
{
    public const JAM_MASUK = '06:30';
    public const JUMLAH_JP = 11;
    /** Istirahat jatuh setelah JP ini. */
    public const ISTIRAHAT_SETELAH = [4, 7];

    /**
     * 'jp'    => [nomor JP => ['jp','mulai','selesai']]
     * 'semua' => JP + istirahat berurutan, untuk tampilan
     */
    public static function hitung(): array
    {
        $p = PengaturanJam::ambil();
        $t = Carbon::createFromFormat('H:i', self::JAM_MASUK);

        $jp = [];
        $semua = [];

        for ($i = 1; $i <= self::JUMLAH_JP; $i++) {
            $mulai = $t->copy();
            $t->addMinutes($p->menit_jp);

            $row = ['jp' => $i, 'mulai' => $mulai->format('H:i'), 'selesai' => $t->format('H:i')];
            $jp[$i] = $row;
            $semua[] = $row + ['istirahat' => false];

            if ($i < self::JUMLAH_JP && $p->menit_istirahat > 0 && in_array($i, self::ISTIRAHAT_SETELAH, true)) {
                $m = $t->copy();
                $t->addMinutes($p->menit_istirahat);
                $semua[] = ['jp' => null, 'mulai' => $m->format('H:i'), 'selesai' => $t->format('H:i'), 'istirahat' => true];
            }
        }

        return ['jp' => $jp, 'semua' => $semua];
    }

    /** Jam mulai, jam selesai, dan menit efektif untuk blok "JP ke X sebanyak N JP". */
    public static function blok(int $jpMulai, int $jumlah): ?array
    {
        $slots = self::hitung()['jp'];
        $jpAkhir = $jpMulai + $jumlah - 1;

        if ($jumlah < 1 || ! isset($slots[$jpMulai]) || ! isset($slots[$jpAkhir])) {
            return null;
        }

        return [
            'mulai'   => $slots[$jpMulai]['mulai'],
            'selesai' => $slots[$jpAkhir]['selesai'],
            'menit'   => $jumlah * PengaturanJam::ambil()->menit_jp,
        ];
    }
}