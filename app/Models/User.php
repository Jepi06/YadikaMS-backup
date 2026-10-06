<?php

namespace App\Models;

use App\Models\Siswa;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

   protected $fillable = [
    'name',
    'email',
    'password',
    'is_active',
    'is_super_admin',
    'must_change_password',
    'avatar',   // ← ganti dari 'photo'
];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    // ── Relasi ─────────────────────────────────────────────────

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')
            ->withPivot(['assigned_by', 'assigned_at']);
    }

    /** Kelas-kelas yang diampu (relevan untuk role guru di modul LMS) */
    public function pengampuMapel()
    {
        return $this->hasMany(\App\Models\Lms\PengampuMapel::class, 'guru_id');
    }

    /**
     * Kelas yang diwali-i user ini PADA PERIODE TERTENTU (default:
     * periode/tahun ajaran yang sedang berjalan sekarang). Ganti dari
     * versi lama yang cuma ngecek kelas.wali_kelas_id global (gak ada
     * dimensi tahun) — sekarang lewat tabel wali_kelas_periode_lms
     * yang emang dibikin buat nyimpen ini per tahun_ajaran+semester.
     */
    public function kelasWaliPeriode(?string $tahunAjaran = null, ?string $semester = null)
    {
        $tahunAjaran ??= \App\Support\TahunAjaran::sekarang();
        $semester ??= \App\Support\TahunAjaran::semesterSekarang();

        return \App\Models\Lms\WaliKelasPeriode::where('user_id', $this->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->with('kelas')
            ->get()
            ->pluck('kelas');
    }

    /** Semua penugasan wali kelas user ini, lintas SEMUA periode (buat dropdown pilihan). */
    public function semuaPenugasanWaliKelas()
    {
        return \App\Models\Lms\WaliKelasPeriode::where('user_id', $this->id)
            ->with('kelas')
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();
    }

    public function isWaliKelas(?string $tahunAjaran = null, ?string $semester = null): bool
    {
        return $this->kelasWaliPeriode($tahunAjaran, $semester)->isNotEmpty();
    }

    // ── Helper: cek akses per sistem ──────────────────────────

    public function hasModuleAccess(string $moduleKode): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Super admin gak perlu role spesifik per modul buat bisa LOGIN
        // ke sistem manapun (PKL/SPMB/LMS) — tapi menu/fitur di dalam
        // tiap sistem tetap ngikutin role dia (kalau gak punya role di
        // modul itu, sidebar/menu-nya ya kosong, cuma dashboard polos).
        if ($this->is_super_admin) {
            return true;
        }

        return $this->roles()
            ->whereHas('module', fn($q) => $q->where('kode', $moduleKode))
            ->exists();
    }

    public function hasPklAccess(): bool
    {
        return $this->hasModuleAccess('pkl');
    }

    /** Diganti dari hasPpdbAccess() -> hasSpmbAccess() */
    public function hasSpmbAccess(): bool
    {
        return $this->hasModuleAccess('spmb');
    }

    public function hasLmsAccess(): bool
    {
        return $this->hasModuleAccess('lms');
    }

    public function hasRoleInModule(string $moduleKode, string ...$roleKodes): bool
    {
        return $this->roles()
            ->whereHas('module', fn($q) => $q->where('kode', $moduleKode))
            ->whereIn('kode', $roleKodes)
            ->exists();
    }

    public function hasPklRole(string ...$roles): bool
    {
        return $this->hasRoleInModule('pkl', ...$roles);
    }

    /** Diganti dari hasPpdbRole() -> hasSpmbRole() */
    public function hasSpmbRole(string ...$roles): bool
    {
        return $this->hasRoleInModule('spmb', ...$roles);
    }

    /** ← BARU: role check untuk modul LMS (dipakai middleware role.lms) */
    public function hasLmsRole(string ...$roles): bool
    {
        return $this->hasRoleInModule('lms', ...$roles);
    }

    public function isSiswa(): bool
    {
        return $this->hasPklRole('siswa');
    }

    public function isApproverPkl(): bool
    {
        return $this->hasPklRole('wali_kelas', 'guru_bk', 'kesiswaan', 'kepala_jurusan');
    }

    /** dipakai middleware/route untuk membatasi menu CRUD ke admin & hubin saja */
    public function isAdminAtauHubin(): bool
    {
        return $this->hasPklRole('admin');
    }

    /** Admin SPMB — satu-satunya role yang boleh login ke panel SPMB */
    public function isAdminSpmb(): bool
    {
        return $this->hasSpmbRole('admin');
    }

    // ← BARU: helper role LMS

    public function isAdminLms(): bool
    {
        return $this->hasLmsRole('admin');
    }

    public function isGuruLms(): bool
    {
        return $this->hasLmsRole('guru');
    }

    public function isSiswaLms(): bool
    {
        return $this->hasLmsRole('siswa');
    }

    // ← BARU: Super Admin — TERPISAH dari role per-modul di atas.
    // Nggak nunjuk ke satu sistem tertentu; ini akses ke Panel Super
    // Admin yang guard-agnostic (bisa dibuka dari login PKL/SPMB/LMS
    // manapun selama akunnya ditandai is_super_admin = true).

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /**
     * Ambil user yang sedang login, DI GUARD MANAPUN dia login
     * (pkl/spmb/lms) — dipakai Panel Super Admin karena panel itu
     * gak terikat ke satu guard tertentu.
     */
    public static function resolveAnyGuardUser(): ?self
    {
        return Auth::guard('pkl')->user()
            ?? Auth::guard('spmb')->user()
            ?? Auth::guard('lms')->user();
    }

    // ── Accessors ─────────────────────────────────────────────

    protected function labelForModule(string $moduleKode, array $labels): string
    {
        $kodeRoles = $this->roles()
            ->whereHas('module', fn($q) => $q->where('kode', $moduleKode))
            ->pluck('kode');

        if ($kodeRoles->isEmpty()) {
            return '-';
        }

        return $kodeRoles->map(fn($k) => $labels[$k] ?? $k)->implode(', ');
    }

    public function getRolePklLabelAttribute(): string
    {
        return $this->labelForModule('pkl', [
            'admin' => 'Admin PKL',
            'wali_kelas' => 'Wali Kelas',
            'guru_bk' => 'Guru BK',
            'kesiswaan' => 'Kesiswaan',
            'kepala_jurusan' => 'Kepala Jurusan',
            'siswa' => 'Siswa',
        ]);
    }

    /** Diganti dari getRolePpdbLabelAttribute() -> getRoleSpmbLabelAttribute() */
    public function getRoleSpmbLabelAttribute(): string
    {
        return $this->labelForModule('spmb', [
            'admin' => 'Admin SPMB',
        ]);
    }

    public function getRoleLmsLabelAttribute(): string
    {
        return $this->labelForModule('lms', [
            'admin' => 'Admin LMS',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
        ]);
    }
}