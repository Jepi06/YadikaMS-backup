<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\{TempatPkl, PenempatanPkl};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Siswa;
use App\Models\Kelas;
/**
 * Pengajuan tempat PKL secara PUBLIK (tanpa login) oleh siswa.
 *
 * Alur:
 * 1. Siswa pilih Kelas -> daftar nama siswa di kelas tsb dimuat via AJAX
 *    (reuse SiswaController::byKelas), lengkap dengan fitur search & tanda
 *    siswa yang sudah punya pengajuan aktif (tidak bisa dipilih lagi).
 * 2. Siswa centang satu atau beberapa nama (misal 1 kelompok PKL bareng).
 * 3. Siswa pilih Tempat PKL yang sudah terdaftar (yang belum penuh kuotanya),
 *    ATAU input tempat baru (nama + alamat) -> otomatis dibuatkan record
 *    baru di tabel tempat_pkl.
 * 4. Tempat yang kuotanya sudah penuh otomatis disembunyikan dari pilihan
 *    (lihat TempatPkl::tersedia()), dan divalidasi ulang di server saat
 *    submit supaya tidak ada race condition antara load form & submit.
 * 5. Admin melengkapi guru pembimbing + jadwal lewat tombol "Lengkapi" di
 *    halaman Penempatan PKL (lihat PenempatanPklController::lengkapi()),
 *    yang otomatis mengubah status jadi 'diajukan' sehingga masuk antrian
 *    approval Wali Kelas -> Guru BK -> Kesiswaan -> Kepala Jurusan.
 * 6. Jika admin menghapus pengajuan (mis. salah pilih tempat), siswa bisa
 *    mengajukan ulang karena tidak ada lagi record aktif miliknya.
 */
class PengajuanPklPublicController extends Controller
{
    public function create()
    {
        $kelas = Kelas::with('jurusan')->orderBy('nama_kelas')->get();
        // Hanya tampilkan tempat yang belum penuh kuotanya.
        $tempatPkl = TempatPkl::tersedia();

        return view('Mapping.public.pengajuan', compact('kelas', 'tempatPkl'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
            'tempat_option' => 'required|in:existing,baru',
            'tempat_pkl_id' => 'required_if:tempat_option,existing|nullable|exists:tempat_pkl,id',
            'tempat_baru_nama' => 'required_if:tempat_option,baru|nullable|string|max:255',
            'tempat_baru_alamat' => 'required_if:tempat_option,baru|nullable|string',
            'tempat_baru_bidang' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'siswa_ids.required' => 'Pilih minimal satu nama siswa.',
            'tempat_pkl_id.required_if' => 'Silakan pilih tempat PKL yang sudah terdaftar.',
            'tempat_baru_nama.required_if' => 'Nama tempat PKL baru wajib diisi.',
            'tempat_baru_alamat.required_if' => 'Alamat tempat PKL baru wajib diisi.',
        ]);

        // Validasi ulang di server (jangan percaya hanya pada disabled checkbox di UI):
        // pastikan tidak ada siswa terpilih yang sudah punya pengajuan aktif.
        $siswaTerpilih = Siswa::whereIn('id', $validated['siswa_ids'])->get();
        $sudahAktif = $siswaTerpilih->filter(fn($s) => $s->sudahDitempatkan());

        if ($sudahAktif->isNotEmpty()) {
            throw ValidationException::withMessages([
                'siswa_ids' => 'Beberapa siswa yang dipilih sudah memiliki pengajuan PKL aktif: '
                    . $sudahAktif->pluck('nama')->implode(', ') . '. Silakan hubungi admin bila ingin mengajukan ulang.',
            ]);
        }

        // BARU: cek ulang kuota di server. Tempat yang sudah penuh disembunyikan
        // di form, tapi bisa saja jadi penuh di antara load form & submit
        // (misal ada pengajuan lain yang masuk duluan), jadi tetap divalidasi ulang.
        if ($validated['tempat_option'] === 'existing') {
            $tempat = TempatPkl::findOrFail($validated['tempat_pkl_id']);
            $sisa = $tempat->sisaKuota(); // null = tanpa batas

            if ($sisa !== null && count($validated['siswa_ids']) > $sisa) {
                throw ValidationException::withMessages([
                    'tempat_pkl_id' => $sisa > 0
                        ? "Kuota tempat ini tinggal {$sisa} slot, tidak cukup untuk " . count($validated['siswa_ids']) . " siswa yang dipilih."
                        : 'Kuota tempat ini sudah penuh, silakan pilih tempat lain.',
                ]);
            }
        }

        $namaBerhasil = DB::transaction(function () use ($validated, $siswaTerpilih) {
            if ($validated['tempat_option'] === 'baru') {
                $tempat = TempatPkl::create([
                    'nama_tempat' => $validated['tempat_baru_nama'],
                    'alamat' => $validated['tempat_baru_alamat'],
                    'bidang_usaha' => $validated['tempat_baru_bidang'] ?? null,
                ]);
                $tempatPklId = $tempat->id;
            } else {
                $tempatPklId = $validated['tempat_pkl_id'];
            }

            $nama = [];
            foreach ($siswaTerpilih as $siswa) {
                PenempatanPkl::create([
                    'siswa_id' => $siswa->id,
                    'tempat_pkl_id' => $tempatPklId,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status' => 'draft',
                ]);
                $nama[] = $siswa->nama;
            }

            return $nama;
        });

        return redirect()
            ->route('pkl.pengajuan.create')
            ->with('success', 'Pengajuan tempat PKL untuk ' . implode(', ', $namaBerhasil)
                . ' berhasil dikirim. Admin akan melengkapi guru pembimbing & jadwal, lalu pengajuan akan masuk proses approval.');
    }
}
