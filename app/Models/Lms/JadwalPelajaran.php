<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran_lms';

    protected $fillable = ['pengampu_mapel_id', 'hari', 'jp_mulai', 'jumlah_jp', 'jam_mulai', 'jam_selesai', 'ruangan'];

    public const HARI = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class, 'pengampu_mapel_id');
    }

    public function getNamaHariAttribute(): string
    {
        return self::HARI[$this->hari] ?? '-';
    }
}
