<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Lms\ModulAjar;
use App\Models\Lms\PengampuMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ModulAjarController extends Controller
{
    private function authorizePengampu(PengampuMapel $pengampuMapel): void
    {
        abort_unless(
            $pengampuMapel->guru_id === Auth::guard('lms')->id(),
            403,
            'Anda bukan pengampu kelas ini.'
        );
    }

    public function index(PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $pengampuMapel->load('mataPelajaran', 'kelas');
        $modulAjar = ModulAjar::where('pengampu_mapel_id', $pengampuMapel->id)->latest()->get();

        return view('lms.guru.modul-ajar', compact('pengampuMapel', 'modulAjar'));
    }

    public function store(Request $request, PengampuMapel $pengampuMapel)
    {
        $this->authorizePengampu($pengampuMapel);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480'], // 20MB, dokumen bisa lebih besar dari materi
        ]);

        ModulAjar::create([
            'pengampu_mapel_id' => $pengampuMapel->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $request->file('file')->store('modul-ajar', 'public'),
        ]);

        return back()->with('status', 'Modul ajar berhasil diunggah.');
    }

    public function destroy(ModulAjar $modulAjar)
    {
        $this->authorizePengampu($modulAjar->pengampuMapel);

        Storage::disk('public')->delete($modulAjar->file_path);
        $modulAjar->delete();

        return back()->with('status', 'Modul ajar dihapus.');
    }
}
