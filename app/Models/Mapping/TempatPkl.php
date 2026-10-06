<?php

namespace App\Models\Mapping;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama_tempat
 * @property string $alamat
 * @property string|null $bidang_usaha
 * @property string|null $nama_kontak
 * @property string|null $no_telp
 * @property int|null $kuota_maksimal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\PenempatanPkl> $penempatanPkl
 * @property-read int|null $penempatan_pkl_count
 * @mixin \Eloquent
 */
class TempatPkl extends Model
{
    protected $table = 'tempat_pkl';
    protected $fillable = ['nama_tempat', 'alamat', 'bidang_usaha', 'nama_kontak', 'no_telp', 'kuota_maksimal'];

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class);
    }

    /**
     * Hitung siswa yang masih "menempati" slot di tempat ini.
     * Status 'rejected' TIDAK dihitung — siswa yang ditolak dianggap
     * sudah keluar dari tempat ini dan boleh mengajukan ke tempat lain,
     * jadi slotnya otomatis kembali tersedia.
     */
    public function jumlahTerisi(): int
    {
        return $this->penempatanPkl()
            ->where('status', '!=', 'rejected')
            ->count();
    }

    /**
     * Sisa kuota. null artinya tidak ada batas (kuota_maksimal belum diisi admin).
     */
    public function sisaKuota(): ?int
    {
        if ($this->kuota_maksimal === null) {
            return null;
        }

        return max(0, $this->kuota_maksimal - $this->jumlahTerisi());
    }

    public function isPenuh(): bool
    {
        if ($this->kuota_maksimal === null) {
            return false;
        }

        return $this->jumlahTerisi() >= $this->kuota_maksimal;
    }

    /**
     * BARU: daftar tempat PKL yang MASIH TERSEDIA (belum penuh),
     * dipakai di form pengajuan (siswa) & form tambah penempatan (admin)
     * supaya tempat yang sudah full otomatis tersembunyi dari pilihan.
     * kuota_maksimal = null artinya tanpa batas, selalu dianggap tersedia.
     */
    public static function tersedia()
    {
        return static::withCount([
            'penempatanPkl as jumlah_terisi' => function ($q) {
                $q->where('status', '!=', 'rejected');
            }
        ])
            ->orderBy('nama_tempat')
            ->get()
            ->reject(fn($t) => $t->kuota_maksimal !== null && $t->jumlah_terisi >= $t->kuota_maksimal)
            ->values();
    }
}
