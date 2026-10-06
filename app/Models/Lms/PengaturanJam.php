<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class PengaturanJam extends Model
{
    protected $table = 'pengaturan_jam_lms';

    protected $fillable = ['menit_jp', 'menit_istirahat'];

    public static function ambil(): self
    {
        return static::first() ?? new static(['menit_jp' => 45, 'menit_istirahat' => 20]);
    }
}