<?php

namespace App\Models\Lms;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Wali kelas SATU periode (tahun_ajaran + semester) tertentu — beda
 * dari kelas.wali_kelas_id yang global/gak ada dimensi tahun (masih
 * dipakai apa adanya oleh sistem PKL, gak disentuh di sini).
 */
class WaliKelasPeriode extends Model
{
    protected $table = 'wali_kelas_periode_lms';

    protected $fillable = [
        'kelas_id',
        'user_id',
        'tahun_ajaran',
        'semester',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
