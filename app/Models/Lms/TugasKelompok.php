<?php

namespace App\Models\Lms;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;

class TugasKelompok extends Model
{
    protected $table = 'tugas_kelompok';
    protected $fillable = ['tugas_id', 'nama_kelompok', 'ketua_siswa_id'];

    public function tugas() { return $this->belongsTo(Tugas::class); }
    public function ketua() { return $this->belongsTo(Siswa::class, 'ketua_siswa_id'); }

    public function anggota()
    {
        return $this->belongsToMany(Siswa::class, 'tugas_kelompok_anggota', 'tugas_kelompok_id', 'siswa_id');
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }
}