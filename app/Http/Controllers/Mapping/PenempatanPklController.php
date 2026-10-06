<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\{PenempatanPkl, TempatPkl, GuruPembimbing};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Siswa;
use App\Models\Kelas;
class PenempatanPklController extends Controller
{
    public function index(Request $request)
    {
        $query = PenempatanPkl::with(['siswa.kelas.jurusan', 'tempatPkl', 'guruPembimbing']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tempat_pkl_id')) {
            $query->where('tempat_pkl_id', $request->tempat_pkl_id);
        }
        if ($request->filled('search')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('nis', 'like', "%{$request->search}%");
            });
        }

        $penempatan = $query->latest()->paginate(15)->withQueryString();
        $kelas = Kelas::with('jurusan')->orderBy('nama_kelas')->get();

        // Dipisah jadi dua:
        // - $tempatPkl: SEMUA tempat, dipakai untuk dropdown FILTER tabel (tempat
        //   yang sudah penuh tetap harus bisa difilter, karena datanya sudah ada
        //   di tabel penempatan).
        // - $tempatPklTersedia: hanya yang BELUM penuh, dipakai di form
        //   "Tambah Penempatan" supaya admin tidak menempatkan siswa ke tempat
        //   yang kuotanya sudah habis.
        $tempatPkl = TempatPkl::orderBy('nama_tempat')->get();
        $tempatPklTersedia = TempatPkl::tersedia();

        $guruPembimbing = GuruPembimbing::orderBy('nama')->get();

        return view('Mapping.penempatan.index', compact(
            'penempatan',
            'kelas',
            'tempatPkl',
            'tempatPklTersedia',
            'guruPembimbing'
        ));
    }

    /**
     * Simpan penempatan untuk beberapa siswa sekaligus (bulk) — dipakai ADMIN.
     * Untuk pengajuan mandiri oleh siswa, lihat SiswaPklController::store().
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
            'tempat_pkl_id' => 'required|exists:tempat_pkl,id',
            'guru_pembimbing_id' => 'required|exists:guru_pembimbing,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tahun_ajaran' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        // BARU: validasi kuota sebelum insert bulk.
        $tempat = TempatPkl::findOrFail($request->tempat_pkl_id);
        $sisa = $tempat->sisaKuota(); // null = tanpa batas

        if ($sisa !== null && count($request->siswa_ids) > $sisa) {
            return back()->with('error', $sisa > 0
                ? "Kuota tempat PKL ini tinggal {$sisa} slot, tidak cukup untuk " . count($request->siswa_ids) . " siswa yang dipilih."
                : 'Kuota tempat PKL ini sudah penuh.');
        }

        DB::transaction(function () use ($request) {
            foreach ($request->siswa_ids as $siswaId) {
                PenempatanPkl::create([
                    'siswa_id' => $siswaId,
                    'tempat_pkl_id' => $request->tempat_pkl_id,
                    'guru_pembimbing_id' => $request->guru_pembimbing_id,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'tahun_ajaran' => $request->tahun_ajaran,
                    'keterangan' => $request->keterangan,
                    'status' => 'draft',
                ]);
            }
        });

        return back()->with('success', 'Penempatan PKL berhasil disimpan untuk ' . count($request->siswa_ids) . ' siswa.');
    }

    public function show(PenempatanPkl $penempatan)
    {
        $penempatan->load([
            'siswa.kelas.jurusan',
            'tempatPkl',
            'guruPembimbing',
            'approverWaliKelas',
            'approverGuruBk',
            'approverKesiswaan',
            'approverKepalaJurusan',
        ]);

        return view('Mapping.penempatan.show', compact('penempatan'));
    }

    public function update(Request $request, PenempatanPkl $penempatan)
    {
        if ($penempatan->status !== 'draft') {
            return back()->with('error', 'Penempatan yang sudah diajukan tidak dapat diubah.');
        }

        $data = $request->validate([
            'tempat_pkl_id' => 'required|exists:tempat_pkl,id',
            'guru_pembimbing_id' => 'required|exists:guru_pembimbing,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tahun_ajaran' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        $penempatan->update($data);

        return back()->with('success', 'Data penempatan berhasil diperbarui.');
    }

    public function destroy(PenempatanPkl $penempatan)
    {
        if ($penempatan->status === 'approved') {
            return back()->with('error', 'Penempatan yang sudah approved tidak dapat dihapus.');
        }

        $penempatan->delete();

        return back()->with('success', 'Data penempatan berhasil dihapus.');
    }

    /**
     * Ubah status penempatan menjadi "diajukan" untuk memulai proses approval
     */
    public function ajukan(PenempatanPkl $penempatan)
    {
        if ($penempatan->status !== 'draft') {
            return back()->with('error', 'Hanya penempatan berstatus draft yang bisa diajukan.');
        }

        $penempatan->update(['status' => 'diajukan']);

        return back()->with('success', 'Penempatan berhasil diajukan untuk proses approval.');
    }

    /**
     * Lengkapi data penempatan yang berasal dari pengajuan PUBLIK siswa
     * (guru pembimbing & jadwal belum diisi saat siswa mengajukan mandiri
     * lewat PengajuanPklPublicController). Tempat PKL & siswa TIDAK diubah
     * di sini karena itu sudah ditentukan siswa sendiri saat mengajukan.
     *
     * Begitu dilengkapi, status otomatis berubah draft -> diajukan supaya
     * langsung masuk antrian approval Wali Kelas tanpa perlu klik "Ajukan"
     * terpisah.
     */
    public function lengkapi(Request $request, PenempatanPkl $penempatan)
    {
        if ($penempatan->status !== 'draft') {
            return back()->with('error', 'Data ini sudah diproses dan tidak bisa dilengkapi ulang.');
        }

        $data = $request->validate([
            'guru_pembimbing_id' => 'required|exists:guru_pembimbing,id',
            'tahun_ajaran' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $data['status'] = 'diajukan';

        $penempatan->update($data);

        return back()->with('success', 'Data berhasil dilengkapi dan otomatis diajukan untuk proses approval.');
    }

    /**
     * Cetak/unduh Surat Rekomendasi PKL dalam bentuk PDF.
     * Hanya bisa dicetak jika seluruh 4 approver sudah menyetujui (status = approved),
     * supaya surat resmi tidak keluar sebelum proses approval selesai.
     *
     * Butuh package: composer require barryvdh/laravel-dompdf
     */
    public function cetakSuratRekomendasi(PenempatanPkl $penempatan)
    {
        if ($penempatan->status !== 'approved') {
            return back()->with('error', 'Surat rekomendasi hanya bisa dicetak untuk penempatan yang sudah disetujui penuh (approved).');
        }

        $penempatan->load([
            'siswa.kelas.jurusan',
            'tempatPkl',
            'guruPembimbing',
            'approverKepalaJurusan',
        ]);

        $nomorSurat = sprintf(
            '%03d/PKL-REKOM/%s/%s',
            $penempatan->id,
            'SMK-YS',
            now()->format('m/Y')
        );

        $pdf = Pdf::loadView('Mapping.penempatan.surat_rekomendasi', [
            'penempatan' => $penempatan,
            'nomorSurat' => $nomorSurat,
            'tanggalCetak' => now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'portrait');

        $namaFile = 'Surat-Rekomendasi-PKL-' . str($penempatan->siswa->nama)->slug() . '.pdf';

        return $pdf->download($namaFile);
    }
}
