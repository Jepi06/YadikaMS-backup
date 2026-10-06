<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

/**
 * Dokumen administratif guru (RPP/modul ajar resmi). BEDA dari Materi
 * (bahan belajar untuk siswa) — ini gak pernah ditampilkan ke siswa,
 * cuma buat arsip guru sendiri + Panel Super Admin (admin/kepsek).
 */
class ModulAjar extends Model
{
    protected $table = 'modul_ajar_lms';

    protected $fillable = [
        'pengampu_mapel_id',
        'judul',
        'deskripsi',
        'file_path',
    ];

    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }
}
