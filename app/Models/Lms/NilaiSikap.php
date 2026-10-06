<?php

namespace App\Models\Lms;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;

class NilaiSikap extends Model
{
    protected $table = 'nilai_sikap_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'siswa_id',
        'predikat',
        'catatan',
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
