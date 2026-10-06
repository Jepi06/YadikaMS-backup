<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'pengampu_mapel_id',
        'judul',
        'deskripsi',
        'file_path',
        'link_url',
        'urutan',
        'mode_akses',
        'dibuka_manual',
        'buka_pada',
    ];
    protected $casts = [
        'dibuka_manual' => 'boolean',
        'buka_pada' => 'datetime',
    ];
    public function pengampuMapel()
    {
        return $this->belongsTo(PengampuMapel::class);
    }
    /**
     * Return null kalau materi TERBUKA untuk siswa ini, atau string
     * alasan kenapa masih TERKUNCI.
     */
    public function alasanTerkunci(?\App\Models\Siswa $siswa): ?string
    {
        if (! $siswa) {
            return null; // bukan siswa (misal guru pemilik) — gak dikunci
        }

        switch ($this->mode_akses) {
            case 'manual':
                return $this->dibuka_manual ? null : 'Materi ini akan dibuka oleh guru.';

            case 'tanggal':
                if ($this->buka_pada && now()->lt($this->buka_pada)) {
                    return 'Materi dibuka pada ' . $this->buka_pada->translatedFormat('d M Y, H:i') . '.';
                }
                return null;

            case 'berurutan':
                $sebelumnya = self::where('pengampu_mapel_id', $this->pengampu_mapel_id)
                    ->where(function ($q) {
                        $q->where('urutan', '<', $this->urutan)
                            ->orWhere(fn($qq) => $qq->where('urutan', $this->urutan)->where('id', '<', $this->id));
                    })
                    ->orderByDesc('urutan')
                    ->orderByDesc('id')
                    ->first();

                if (! $sebelumnya) {
                    return null; // materi pertama, gak ada yang harus diselesaikan dulu
                }

                $sudahSelesai = MateriSelesai::where('materi_id', $sebelumnya->id)
                    ->where('siswa_id', $siswa->id)
                    ->exists();

                return $sudahSelesai ? null : 'Selesaikan dulu materi "' . $sebelumnya->judul . '".';

            default:
                return null;
        }
    }
}
