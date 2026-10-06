<?php

namespace App\Models\Mapping;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenempatanPkl extends Model
{
    protected $table = 'penempatan_pkl';

    /** Ganti di sini kalau kode mata pelajaran BK di data Anda beda dari 'BK' */
    public const KODE_MAPEL_BK = 'BK';

    protected $fillable = [
        'siswa_id',
        'tempat_pkl_id',
        'guru_pembimbing_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tahun_ajaran',
        'keterangan',
        'status',
        'status_wali_kelas',
        'catatan_wali_kelas',
        'approved_at_wali_kelas',
        'approved_by_wali_kelas',
        'status_guru_bk',
        'catatan_guru_bk',
        'approved_at_guru_bk',
        'approved_by_guru_bk',
        'status_kesiswaan',
        'catatan_kesiswaan',
        'approved_at_kesiswaan',
        'approved_by_kesiswaan',
        'status_kepala_jurusan',
        'catatan_kepala_jurusan',
        'approved_at_kepala_jurusan',
        'approved_by_kepala_jurusan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at_wali_kelas' => 'datetime',
        'approved_at_guru_bk' => 'datetime',
        'approved_at_kesiswaan' => 'datetime',
        'approved_at_kepala_jurusan' => 'datetime',
    ];

    /**
     * Urutan tahapan approval PKL. Dipakai berulang kali di bawah,
     * jadi disatukan di sini supaya tidak duplikat & gampang diubah urutannya.
     */
    private const APPROVAL_CHAIN = [
        'wali_kelas' => 'Wali Kelas',
        'guru_bk' => 'Guru BK',
        'kesiswaan' => 'Kesiswaan',
        'kepala_jurusan' => 'Kepala Jurusan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tempatPkl()
    {
        return $this->belongsTo(TempatPkl::class);
    }

    public function guruPembimbing()
    {
        return $this->belongsTo(GuruPembimbing::class);
    }

    public function approverWaliKelas()
    {
        return $this->belongsTo(User::class, 'approved_by_wali_kelas');
    }

    public function approverGuruBk()
    {
        return $this->belongsTo(User::class, 'approved_by_guru_bk');
    }

    public function approverKesiswaan()
    {
        return $this->belongsTo(User::class, 'approved_by_kesiswaan');
    }

    public function approverKepalaJurusan()
    {
        return $this->belongsTo(User::class, 'approved_by_kepala_jurusan');
    }

    /**
     * PERBAIKAN BESAR: role sekarang many-to-many (satu user bisa punya lebih
     * dari satu role PKL sekaligus, misal admin yang juga wali_kelas), jadi
     * tidak bisa lagi pakai match() atas satu nilai $user->role_pkl (yang
     * sekarang malah sudah tidak ada). Method ini mengembalikan tahap MANA
     * yang sedang bisa diproses oleh $user, dengan scoping:
     *  - wali_kelas    : hanya kalau dia wali kelas dari kelas siswa ini
     *  - guru_bk       : hanya kalau dia mengampu mapel BK di kelas siswa ini
     *  - kesiswaan     : tanpa scope
     *  - kepala_jurusan: hanya kalau dia kepala dari jurusan siswa ini
     *
     * canApproveBy() dan ApprovalController keduanya pakai method ini supaya
     * logikanya cuma ada di satu tempat.
     */
    public function tahapUntukUser(User $user): ?string
    {
        if ($this->status !== 'diajukan') {
            return null;
        }

        if (
            $user->hasPklRole('wali_kelas')
            && $this->status_wali_kelas === 'pending'
            && $this->diampuOlehWaliKelas($user)
        ) {
            return 'wali_kelas';
        }

        if (
            $user->hasPklRole('guru_bk')
            && $this->status_wali_kelas === 'approved'
            && $this->status_guru_bk === 'pending'
            && $this->diampuOlehGuruBk($user)
        ) {
            return 'guru_bk';
        }

        if (
            $user->hasPklRole('kesiswaan')
            && $this->status_guru_bk === 'approved'
            && $this->status_kesiswaan === 'pending'
        ) {
            return 'kesiswaan';
        }

        if (
            $user->hasPklRole('kepala_jurusan')
            && $this->status_kesiswaan === 'approved'
            && $this->status_kepala_jurusan === 'pending'
            && $this->diampuOlehKepalaJurusan($user)
        ) {
            return 'kepala_jurusan';
        }

        return null;
    }

    public function canApproveBy(User $user): bool
    {
        return $this->tahapUntukUser($user) !== null;
    }

    private function diampuOlehWaliKelas(User $user): bool
    {
        $this->loadMissing('siswa.kelas');

        return $this->siswa?->kelas?->wali_kelas_id === $user->id;
    }

    private function diampuOlehKepalaJurusan(User $user): bool
    {
        $this->loadMissing('siswa.kelas.jurusan');

        return $this->siswa?->kelas?->jurusan?->kepala_jurusan_id === $user->id;
    }

    private function diampuOlehGuruBk(User $user): bool
    {
        $this->loadMissing('siswa');

        if (!$this->siswa) {
            return false;
        }

        return DB::table('pengampu_mapel')
            ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'pengampu_mapel.mata_pelajaran_id')
            ->where('pengampu_mapel.guru_id', $user->id)
            ->where('pengampu_mapel.kelas_id', $this->siswa->kelas_id)
            ->where('mata_pelajaran.kode', self::KODE_MAPEL_BK)
            ->exists();
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'diajukan' => '<span class="badge bg-primary">Diajukan</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-light text-dark">' . $this->status . '</span>',
        };
    }

    /** Hitung progres approval (0–4) */
    public function getApprovalProgressAttribute(): int
    {
        $count = 0;
        foreach (array_keys(self::APPROVAL_CHAIN) as $key) {
            if ($this->{"status_{$key}"} === 'approved') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Daftar nama tahapan yang MASIH menunggu approval, dalam urutan proses.
     */
    public function getPendingApproversAttribute(): array
    {
        if (in_array($this->status, ['draft', 'rejected', 'approved'])) {
            return [];
        }

        $pending = [];
        foreach (self::APPROVAL_CHAIN as $key => $label) {
            if ($this->{"status_{$key}"} === 'pending') {
                $pending[] = $label;
            }
        }

        return $pending;
    }

    public function getCurrentApprovalStageAttribute(): ?string
    {
        return $this->pending_approvers[0] ?? null;
    }

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', ['rejected']);
    }
}
