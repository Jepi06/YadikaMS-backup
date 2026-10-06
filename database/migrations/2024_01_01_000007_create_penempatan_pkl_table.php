<?php
// FILE: database/migrations/2024_01_01_000008_create_penempatan_pkl_table.php
// Dijalankan setelah siswa, tempat_pkl, guru_pembimbing, dan users.
// Kolom approved_by_* mengacu ke users; validasi role-nya
// (wali_kelas/guru_bk/kesiswaan/kepala_jurusan) dicek lewat tabel
// user_role, bukan kolom enum di users lagi.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penempatan_pkl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();
            $table->foreignId('tempat_pkl_id')
                ->constrained('tempat_pkl')
                ->cascadeOnDelete();
            $table->foreignId('guru_pembimbing_id')
                ->nullable()
                ->constrained('guru_pembimbing')
                ->nullOnDelete();

            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->string('tahun_ajaran')
                ->default(date('Y') . '/' . (date('Y') + 1));
            $table->text('keterangan')->nullable();

            // Status utama penempatan
            $table->enum('status', ['draft', 'diajukan', 'approved', 'rejected'])
                ->default('draft');

            // ── Approval: Wali Kelas ────────────────────────────
            $table->enum('status_wali_kelas', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->text('catatan_wali_kelas')->nullable();
            $table->timestamp('approved_at_wali_kelas')->nullable();
            $table->foreignId('approved_by_wali_kelas')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ── Approval: Guru BK ───────────────────────────────
            $table->enum('status_guru_bk', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->text('catatan_guru_bk')->nullable();
            $table->timestamp('approved_at_guru_bk')->nullable();
            $table->foreignId('approved_by_guru_bk')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ── Approval: Kesiswaan ─────────────────────────────
            $table->enum('status_kesiswaan', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->text('catatan_kesiswaan')->nullable();
            $table->timestamp('approved_at_kesiswaan')->nullable();
            $table->foreignId('approved_by_kesiswaan')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ── Approval: Kepala Jurusan ────────────────────────
            $table->enum('status_kepala_jurusan', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->text('catatan_kepala_jurusan')->nullable();
            $table->timestamp('approved_at_kepala_jurusan')->nullable();
            $table->foreignId('approved_by_kepala_jurusan')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penempatan_pkl');
    }
};
