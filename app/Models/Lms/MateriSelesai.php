<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class MateriSelesai extends Model
{
    protected $table = 'materi_selesai';
    protected $fillable = ['materi_id', 'siswa_id', 'selesai_at'];
}