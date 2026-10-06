<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models\Mapping{
/**
 * @property int $id
 * @property string|null $nip
 * @property string $nama
 * @property string|null $no_hp
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\PenempatanPkl> $penempatanPkl
 * @property-read int|null $penempatan_pkl_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruPembimbing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class GuruPembimbing extends \Eloquent {}
}

namespace App\Models\Mapping{
/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string|null $deskripsi
 * @property int $kuota
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\Kelas> $kelas
 * @property-read int|null $kelas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereKuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Jurusan extends \Eloquent {}
}

namespace App\Models\Mapping{
/**
 * @property int $id
 * @property string $nama_kelas
 * @property string $tingkat
 * @property int $jurusan_id
 * @property int|null $wali_kelas_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mapping\Jurusan $jurusan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\Siswa> $siswa
 * @property-read int|null $siswa_count
 * @property-read User|null $waliKelas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereJurusanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereNamaKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereTingkat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereWaliKelasId($value)
 * @mixin \Eloquent
 */
	class Kelas extends \Eloquent {}
}

namespace App\Models\Mapping{
/**
 * @property int $id
 * @property int $siswa_id
 * @property int $tempat_pkl_id
 * @property int $guru_pembimbing_id
 * @property \Illuminate\Support\Carbon $tanggal_mulai
 * @property \Illuminate\Support\Carbon $tanggal_selesai
 * @property string $tahun_ajaran
 * @property string|null $keterangan
 * @property string $status
 * @property string $status_wali_kelas
 * @property string|null $catatan_wali_kelas
 * @property \Illuminate\Support\Carbon|null $approved_at_wali_kelas
 * @property int|null $approved_by_wali_kelas
 * @property string $status_guru_bk
 * @property string|null $catatan_guru_bk
 * @property \Illuminate\Support\Carbon|null $approved_at_guru_bk
 * @property int|null $approved_by_guru_bk
 * @property string $status_kesiswaan
 * @property string|null $catatan_kesiswaan
 * @property \Illuminate\Support\Carbon|null $approved_at_kesiswaan
 * @property int|null $approved_by_kesiswaan
 * @property string $status_kepala_jurusan
 * @property string|null $catatan_kepala_jurusan
 * @property \Illuminate\Support\Carbon|null $approved_at_kepala_jurusan
 * @property int|null $approved_by_kepala_jurusan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $approverGuruBk
 * @property-read User|null $approverKepalaJurusan
 * @property-read User|null $approverKesiswaan
 * @property-read User|null $approverWaliKelas
 * @property-read int $approval_progress
 * @property-read string|null $current_approval_stage
 * @property-read array $pending_approvers
 * @property-read string $status_badge
 * @property-read \App\Models\Mapping\GuruPembimbing $guruPembimbing
 * @property-read \App\Models\Mapping\Siswa $siswa
 * @property-read \App\Models\Mapping\TempatPkl $tempatPkl
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl aktif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedAtWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereApprovedByWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCatatanWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereGuruPembimbingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereSiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusGuruBk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusKepalaJurusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusKesiswaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereStatusWaliKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTahunAjaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereTempatPklId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenempatanPkl whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class PenempatanPkl extends \Eloquent {}
}

namespace App\Models\Mapping{
/**
 * @property int $id
 * @property string $nis
 * @property string $nama
 * @property string $jenis_kelamin
 * @property string|null $alamat
 * @property string|null $no_hp
 * @property int $kelas_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mapping\Kelas $kelas
 * @property-read \App\Models\Mapping\PenempatanPkl|null $penempatanAktif
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\PenempatanPkl> $penempatanPkl
 * @property-read int|null $penempatan_pkl_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa belumMengajukan()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa sudahMengajukan()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Siswa extends \Eloquent {}
}

namespace App\Models\Mapping{
/**
 * @property int $id
 * @property string $nama_tempat
 * @property string $alamat
 * @property string|null $bidang_usaha
 * @property string|null $nama_kontak
 * @property string|null $no_telp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mapping\PenempatanPkl> $penempatanPkl
 * @property-read int|null $penempatan_pkl_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereBidangUsaha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereNamaKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereNamaTempat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereNoTelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TempatPkl whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class TempatPkl extends \Eloquent {}
}

namespace App\Models\PPDB{
/**
 * @property int $id
 * @property string $kode
 * @property string $nama
 * @property string|null $deskripsi
 * @property int $kuota
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $sisa_kuota
 * @property-read int $total_diterima
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PPDB\Pendaftar> $pendaftar
 * @property-read int|null $pendaftar_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PPDB\Pendaftar> $pendaftarDiterima
 * @property-read int|null $pendaftar_diterima_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereKuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jurusan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Jurusan extends \Eloquent {}
}

namespace App\Models\PPDB{
/**
 * @property int $id
 * @property string $no_pendaftaran
 * @property string $nama_lengkap
 * @property string $jenis_kelamin
 * @property string $alamat
 * @property string $agama
 * @property string $nama_orang_tua
 * @property string $asal_sekolah
 * @property string $no_hp
 * @property int $jurusan_id
 * @property string $status
 * @property numeric $nominal_pembayaran
 * @property string|null $catatan_admin
 * @property int|null $processed_by
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $jenis_kelamin_label
 * @property-read string $no_hp_formatted
 * @property-read string $nominal_formatted
 * @property-read string $status_badge
 * @property-read string $status_label
 * @property-read string $whatsapp_url
 * @property-read \App\Models\PPDB\Jurusan $jurusan
 * @property-read User|null $processor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar byJurusan($jurusanId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar diterima()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereAgama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereAsalSekolah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereCatatanAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereJurusanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNamaOrangTua($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNoPendaftaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereNominalPembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pendaftar whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Pendaftar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_active
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $role_lms_label
 * @property-read string $role_pkl_label
 * @property-read string $role_ppdb_label
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Siswa|null $siswa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

