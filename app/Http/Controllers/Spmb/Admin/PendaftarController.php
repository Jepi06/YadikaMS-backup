<?php

namespace App\Http\Controllers\Spmb\Admin;

use App\Exports\PendaftarExport;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\SPMB\Pendaftar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PendaftarController extends Controller
{
    /**
     * Dashboard ringkas admin: total pendaftar, total diterima,
     * dan rekap per jurusan untuk ditampilkan di halaman awal panel.
     */
    public function dashboard()
    {
        $totalPendaftar = Pendaftar::count();
        $totalDiterima = Pendaftar::diterima()->count();
        $totalPending = Pendaftar::pending()->count();
        $totalDitolak = Pendaftar::ditolak()->count();
        $jurusanList = Jurusan::withCount('pendaftar')->get();

        return view('spmb.admin.dashboard', compact(
            'totalPendaftar',
            'totalDiterima',
            'totalPending',
            'totalDitolak',
            'jurusanList'
        ));
    }

    /**
     * Daftar SELURUH pendaftar (semua jurusan, diterima maupun belum).
     */
    public function index(Request $request)
    {
        $query = Pendaftar::with('jurusan:id,nama,kode')->latest('id');
        $this->applyFilters($query, $request);

        $pendaftar = $query->paginate(15)->withQueryString();
        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('spmb.admin.pendaftar.index', [
            'pendaftar' => $pendaftar,
            'jurusanList' => $jurusanList,
            'jurusanAktif' => null,
            'judulHalaman' => 'Semua Pendaftar',
        ]);
    }

    /**
     * Daftar pendaftar terfilter per jurusan (dipanggil dari sidebar).
     */
    public function perJurusan(Request $request, Jurusan $jurusan)
    {
        $query = Pendaftar::with('jurusan:id,nama,kode')
            ->where('jurusan_id', $jurusan->id)
            ->latest('id');
        $this->applyFilters($query, $request);

        $pendaftar = $query->paginate(15)->withQueryString();
        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('spmb.admin.pendaftar.index', [
            'pendaftar' => $pendaftar,
            'jurusanList' => $jurusanList,
            'jurusanAktif' => $jurusan,
            'judulHalaman' => 'Pendaftar - ' . $jurusan->nama,
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('cari')) {
            $keyword = $request->query('cari');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                    ->orWhere('no_pendaftaran', 'like', "%{$keyword}%")
                    ->orWhere('no_hp', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->query('lunas') === '1') {
            $query->where('is_lunas', true);
        } elseif ($request->query('lunas') === '0') {
            $query->where('is_lunas', false);
        }
    }

    public function create()
    {
        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('spmb.admin.pendaftar.create', compact('jurusanList'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['created_by'] = Auth::guard('spmb')->id();

        $pendaftar = Pendaftar::create($data);

        return redirect()
            ->route('spmb.admin.pendaftar.show', $pendaftar)
            ->with('success', 'Data pendaftar berhasil ditambahkan.');
    }

    public function show(Pendaftar $pendaftar)
    {
        $pendaftar->load(['jurusan', 'processedBy', 'lunasBy']);

        return view('spmb.admin.pendaftar.show', compact('pendaftar'));
    }

    public function edit(Pendaftar $pendaftar)
    {
        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('spmb.admin.pendaftar.edit', compact('pendaftar', 'jurusanList'));
    }

    public function update(Request $request, Pendaftar $pendaftar)
    {
        $data = $this->validatedData($request, $pendaftar->id);
        $pendaftar->update($data);

        return redirect()
            ->route('spmb.admin.pendaftar.show', $pendaftar)
            ->with('success', 'Data pendaftar berhasil diperbarui.');
    }

    public function destroy(Pendaftar $pendaftar)
    {
        $pendaftar->delete();

        return back()->with('success', 'Data pendaftar berhasil dihapus.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'nama_orang_tua' => ['nullable', 'string', 'max:255'],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'catatan_admin' => ['nullable', 'string'],
        ]);
    }

    /**
     * Tambah / ubah nominal pembayaran melalui popup.
     * Jika nominal mencapai batas (>= Rp 2.000.000), status otomatis
     * berubah menjadi "diterima" dan data masuk ke rekap diterima.
     */
    public function updateNominal(Request $request, Pendaftar $pendaftar)
    {
        if ($pendaftar->is_lunas) {
            return back()->with('error', 'Nominal tidak bisa diubah karena pembayaran sudah dinyatakan LUNAS.');
        }

        $request->validate([
            'nominal_pembayaran' => ['required', 'numeric', 'min:0'],
        ]);

        $pendaftar->nominal_pembayaran = $request->input('nominal_pembayaran');
        $pendaftar->applyAutoAcceptRule();
        $pendaftar->save();

        $pesan = $pendaftar->status === Pendaftar::STATUS_DITERIMA
            ? 'Nominal berhasil disimpan. Nominal telah mencapai batas minimal, status otomatis menjadi DITERIMA.'
            : 'Nominal berhasil disimpan.';

        return back()->with('success', $pesan);
    }

    /**
     * Tombol "Lunas" — penanda administrasi manual oleh admin (ceklis hijau).
     * Terpisah dari status penerimaan; dipakai untuk menandai bahwa
     * pembayaran sudah final dan tidak dapat diedit lagi.
     */
    public function markLunas(Pendaftar $pendaftar)
    {
        if ($pendaftar->belumAdaNominal()) {
            return back()->with('error', 'Tidak bisa menandai lunas, nominal pembayaran belum diisi.');
        }

        $pendaftar->tandaiLunas();
        $pendaftar->save();

        return back()->with('success', 'Pembayaran berhasil ditandai LUNAS.');
    }

    /**
     * Batalkan penanda lunas (jika terjadi kesalahan input admin).
     */
    public function unmarkLunas(Pendaftar $pendaftar)
    {
        $pendaftar->batalkanLunas();
        $pendaftar->save();

        return back()->with('success', 'Penanda LUNAS dibatalkan, nominal dapat diedit kembali.');
    }

    /**
     * Override status manual oleh admin (mis. menolak pendaftar).
     */
    public function updateStatus(Request $request, Pendaftar $pendaftar)
    {
        $request->validate([
            'status' => ['required', 'in:pending,diterima,ditolak'],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $pendaftar->status = $request->input('status');
        $pendaftar->catatan_admin = $request->input('catatan_admin');
        $pendaftar->processed_by = Auth::guard('spmb')->id();
        $pendaftar->processed_at = now();
        $pendaftar->save();

        return back()->with('success', 'Status pendaftar berhasil diperbarui.');
    }

    /**
     * Export Excel: SELURUH data peserta didik baru (semua kolom).
     */
    public function exportExcel()
    {
        $namaFile = 'data-pendaftar-spmb-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new PendaftarExport(), $namaFile);
    }

    /**
     * Export PDF: rekap JUMLAH yang diterima per jurusan (tanpa data pribadi siswa).
     */
    public function exportPdf()
    {
        $rekap = Jurusan::query()
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'kuota'])
            ->map(function (Jurusan $jurusan) {
                return [
                    'kode' => $jurusan->kode,
                    'nama' => $jurusan->nama,
                    'kuota' => $jurusan->kuota,
                    'diterima' => $jurusan->total_diterima,
                    'sisa_kuota' => $jurusan->sisa_kuota,
                ];
            });

        $totalDiterima = $rekap->sum('diterima');

        $pdf = Pdf::loadView('spmb.admin.pendaftar.rekap_pdf', [
            'rekap' => $rekap,
            'totalDiterima' => $totalDiterima,
            'tanggalCetak' => now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('rekap-penerimaan-spmb-' . now()->format('Y-m-d') . '.pdf');
    }
    public function exportExcelPerJurusan(Jurusan $jurusan)
    {
        $slug = str($jurusan->nama)->slug();
        $namaFile = "data-pendaftar-spmb-{$slug}-" . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new PendaftarExport($jurusan->id), $namaFile);
    }

}
