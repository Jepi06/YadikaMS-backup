<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lms\PengaturanJam;
use App\Support\JamPelajaran;
use Illuminate\Http\Request;

class PengaturanJamController extends Controller
{
    public function edit()
    {
        return view('admin.jadwal.pengaturan-jam', [
            'p'    => PengaturanJam::ambil(),
            'grid' => JamPelajaran::hitung()['semua'],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'menit_jp'        => ['required', 'integer', 'between:10,120'],
            'menit_istirahat' => ['required', 'integer', 'between:0,120'],
        ]);

        $p = PengaturanJam::first() ?? new PengaturanJam();
        $p->fill($data)->save();

        return redirect()->route('admin.jadwal.pengaturan-jam')
            ->with('status', 'Pengaturan disimpan. Jadwal yang sudah ada tidak berubah; import ulang jika ingin memakai durasi baru.');
    }
}