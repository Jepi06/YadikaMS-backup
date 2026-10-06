<?php
// app/Http/Controllers/Spmb/PengajuanSpmbPublicController.php

namespace App\Http\Controllers\Spmb;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\SPMB\Pendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanSpmbPublicController extends Controller
{
    public function create()
    {
        $jurusanList = Jurusan::orderBy('nama')->get(['id', 'nama', 'kuota']);

        return view('spmb.public.create', compact('jurusanList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'alamat' => ['required', 'string'],
            'nama_orang_tua' => ['required', 'string', 'max:255'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'jurusan_id' => ['required', 'exists:jurusan,id'],
        ]);

        $pendaftar = DB::transaction(function () use ($data) {
            $data['no_pendaftaran'] = $this->generateNoPendaftaran();
            $data['status'] = 'pending';

            return Pendaftar::create($data);
        });

        return redirect()
            ->route('spmb.pengajuan.berhasil', $pendaftar->no_pendaftaran)
            ->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function berhasil(string $noPendaftaran)
    {
        $pendaftar = Pendaftar::with('jurusan:id,nama')
            ->where('no_pendaftaran', $noPendaftaran)
            ->firstOrFail();

        return view('spmb.public.berhasil', compact('pendaftar'));
    }

    /**
     * Format: SPMB-{tahun}-{urutan 4 digit}, contoh: SPMB-2026-0001
     * Pakai lockForUpdate supaya aman dari race condition saat
     * banyak siswa daftar bersamaan.
     */
    private function generateNoPendaftaran(): string
    {
        $tahun = now()->format('Y');
        $prefix = "SPMB-{$tahun}-";

        $terakhir = Pendaftar::where('no_pendaftaran', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('no_pendaftaran')
            ->value('no_pendaftaran');

        $urutan = $terakhir
            ? ((int) substr($terakhir, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}



