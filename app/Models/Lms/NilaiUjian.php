<?php

namespace App\Models\Lms;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;

class NilaiUjian extends Model
{
    protected $table = 'nilai_ujian_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'siswa_id',
        'nilai_sts',
        'nilai_sas',
    ];

    protected $casts = [
        'nilai_sts' => 'float',
        'nilai_sas' => 'float',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
