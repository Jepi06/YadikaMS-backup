<?php

namespace App\Models\Lms;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode',
        'nama',
        'jurusan_id',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function pengampuMapel()
    {
        return $this->hasMany(PengampuMapel::class);
    }

    /** Mapel umum = berlaku untuk semua jurusan (jurusan_id null) */
    public function scopeUmum($query)
    {
        return $query->whereNull('jurusan_id');
    }
}
