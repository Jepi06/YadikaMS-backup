<?php

namespace App\Models\Lms;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * "Kelas mengajar": guru X mengajar mapel Y di kelas Z pada
 * tahun ajaran & semester tertentu. Jadi acuan untuk materi,
 * tugas, dan presensi.
 */
class PengampuMapel extends Model
{
    protected $table = 'pengampu_mapel';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'kelas_id',
        'tahun_ajaran',
        'semester',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function materi()
    {
        return $this->hasMany(Materi::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function presensi()
    {
        return $this->hasMany(PresensiLms::class);
    }

    public function sesiPresensi()
    {
        return $this->hasMany(SesiPresensi::class);
    }

    public function scopeTahunAjaranAktif($query, string $tahunAjaran, string $semester)
    {
        return $query->where('tahun_ajaran', $tahunAjaran)->where('semester', $semester);
    }
}
    