<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class BobotNilai extends Model
{
    protected $table = 'bobot_nilai_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'bobot_tugas',
        'bobot_sts',
        'bobot_sas',
        'bobot_sikap',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }
}
