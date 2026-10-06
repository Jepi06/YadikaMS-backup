<?php

namespace App\Models\SPMB;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pendaftar extends Model
{
    use HasFactory;

    protected $table = 'pendaftar';

    /**
     * Batas nominal pembayaran administrasi agar otomatis "diterima".
     * Sesuai ketentuan sekolah: Rp 2.000.000
     */
    const MINIMAL_DITERIMA = 2000000;

    const STATUS_PENDING = 'pending';
    const STATUS_DITERIMA = 'diterima';
    const STATUS_DITOLAK = 'ditolak';

    protected $fillable = [
        'no_pendaftaran',
        'nama_lengkap',
        'jenis_kelamin',
        'alamat',
        'agama',
        'nama_orang_tua',
        'asal_sekolah',
        'no_hp',
        'jurusan_id',
        'status',
        'nominal_pembayaran',
        'catatan_admin',
        'processed_by',
        'processed_at',
        'created_by',
        'is_lunas',
        'lunas_at',
        'lunas_by',
    ];

    protected $casts = [
        'nominal_pembayaran' => 'decimal:2',
        'processed_at' => 'datetime',
        'lunas_at' => 'datetime',
        'is_lunas' => 'boolean',
    ];

    /**
     * Relasi ke jurusan HANYA untuk kebutuhan tampilan (nama jurusan),
     * tanpa constraint FK di database. Jika data jurusan dihapus,
     * pendaftar tetap aman (nullOnDelete tidak dipasang di migration).
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lunasBy()
    {
        return $this->belongsTo(User::class, 'lunas_by');
    }

    /* ================= SCOPES ================= */

    public function scopeDiterima($query)
    {
        return $query->where('status', self::STATUS_DITERIMA);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopePerJurusan($query, $jurusanId)
    {
        return $query->where('jurusan_id', $jurusanId);
    }

    public function scopeBelumBayar($query)
    {
        return $query->whereNull('nominal_pembayaran')->orWhere('nominal_pembayaran', 0);
    }

    /* ================= HELPERS / BUSINESS LOGIC ================= */

    /**
     * Generate nomor pendaftaran otomatis: SPMB-2026-0001
     */
    public static function generateNoPendaftaran(): string
    {
        $tahun = now()->year;
        $last = self::where('no_pendaftaran', 'like', "SPMB-{$tahun}-%")
            ->orderByDesc('id')
            ->first();

        $urut = 1;
        if ($last) {
            $urut = (int) substr($last->no_pendaftaran, -4) + 1;
        }

        return sprintf('SPMB-%s-%04d', $tahun, $urut);
    }

    /**
     * Cek apakah nominal memenuhi syarat otomatis diterima (>= Rp 2.000.000)
     */
    public function isEligibleForAutoAccept(): bool
    {
        return (float) $this->nominal_pembayaran >= self::MINIMAL_DITERIMA;
    }

    public function belumAdaNominal(): bool
    {
        return empty($this->nominal_pembayaran) || (float) $this->nominal_pembayaran <= 0;
    }

    /**
     * Terapkan aturan: kalau nominal >= Rp 2.000.000 -> status otomatis diterima.
     * Dipanggil setiap kali admin menambah/mengubah nominal.
     */
    public function applyAutoAcceptRule(): void
    {
        if ($this->isEligibleForAutoAccept() && $this->status !== self::STATUS_DITOLAK) {
            $this->status = self::STATUS_DITERIMA;
            $this->processed_by = Auth::guard('spmb')->id();
            $this->processed_at = now();
        }
    }

    /**
     * Tandai pembayaran lunas secara manual oleh admin (checklist hijau).
     * Terpisah dari status penerimaan siswa baru — murni penanda administrasi.
     */
    public function tandaiLunas(): void
    {
        $this->is_lunas = true;
        $this->lunas_at = now();
        $this->lunas_by = Auth::guard('spmb')->id();
    }

    public function batalkanLunas(): void
    {
        $this->is_lunas = false;
        $this->lunas_at = null;
        $this->lunas_by = null;
    }

    /**
     * Nomor HP dirapikan ke format internasional (62xxxx) untuk link WhatsApp
     */
    public function getWhatsappNumberAttribute(): string
    {
        $number = preg_replace('/\D/', '', $this->no_hp ?? '');

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (!str_starts_with($number, '62')) {
            $number = '62' . $number;
        }

        return $number;
    }

    /**
     * Pesan WhatsApp otomatis, disesuaikan dengan status pendaftar.
     * - DITERIMA  -> ucapan selamat resmi diterima di SMK Yadika Soreang
     * - lainnya   -> pesan umum terkait status pendaftaran
     */
    public function getWhatsappMessageAttribute(): string
    {
        if ($this->status === self::STATUS_DITERIMA) {
            return "Assalamualaikum, {$this->nama_lengkap}.\n\n"
                . "Selamat! Anda telah *DITERIMA* sebagai siswa baru di *SMK Yadika Soreang* "
                . "untuk jurusan *{$this->jurusan?->nama}*.\n\n"
                . "Nomor Pendaftaran: {$this->no_pendaftaran}\n\n"
                . "Mohon segera menghubungi pihak sekolah untuk informasi daftar ulang. Terima kasih.";
        }

        return "Assalamualaikum, {$this->nama_lengkap}.\n\n"
            . "Kami dari SMK Yadika Soreang ingin menginformasikan terkait pendaftaran Anda "
            . "dengan nomor {$this->no_pendaftaran}.\n\n"
            . "Status saat ini: {$this->status_label}.";
    }

    public function getWhatsappLinkAttribute(): string
    {
        return "https://wa.me/{$this->whatsapp_number}?text=" . urlencode($this->whatsapp_message);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DITERIMA => 'Diterima',
            self::STATUS_DITOLAK => 'Belum Diterima',
            default => 'Menunggu Verifikasi',
        };
    }

    public function getSisaTagihanAttribute(): float
    {
        return max(0, self::MINIMAL_DITERIMA - (float) $this->nominal_pembayaran);
    }

    /**
     * Auto-generate no_pendaftaran saat create
     */
    protected static function booted()
    {
        static::creating(function (Pendaftar $pendaftar) {
            if (empty($pendaftar->no_pendaftaran)) {
                $pendaftar->no_pendaftaran = self::generateNoPendaftaran();
            }
            if (empty($pendaftar->status)) {
                $pendaftar->status = self::STATUS_PENDING;
            }
        });
    }
}
