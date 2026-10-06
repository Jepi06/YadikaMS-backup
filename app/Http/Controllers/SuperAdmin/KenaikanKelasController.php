<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Imports\KenaikanKelasImport;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class KenaikanKelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('jurusan')
            ->withCount(['siswa' => fn($q) => $q->where('status', 'aktif')])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('admin.kenaikan-kelas.index', compact('kelas'));
    }

    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        return (new KenaikanKelasImport())->downloadTemplate((int) $request->kelas_id);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        // Simpan file sementara
        $path = $request->file('file')->store('kenaikan-kelas-tmp', 'local');

        // Dry run
        $import = (new KenaikanKelasImport())->setDryRun(true);
        Excel::import($import, Storage::disk('local')->path($path));

        // Simpan ke session
        session([
            'kenaikan_tmp_path' => $path,
            'kenaikan_preview'  => $import->getPreview(),
            'kenaikan_errors'   => $import->getErrors(),
            'kenaikan_skipped'  => $import->getSkipped(),
        ]);

        return view('admin.kenaikan-kelas.preview', [
            'preview' => $import->getPreview(),
            'errors'  => $import->getErrors(),
            'skipped' => $import->getSkipped(),
        ]);
    }

    public function eksekusi(Request $request)
    {
        $path = session('kenaikan_tmp_path');

        if (!$path) {
            return redirect()->route('admin.kenaikan-kelas.index')
                ->withErrors(['file' => 'Sesi habis. Ulangi upload file.']);
        }

        $fullPath = Storage::disk('local')->path($path);

        if (!file_exists($fullPath)) {
            session()->forget(['kenaikan_tmp_path', 'kenaikan_preview', 'kenaikan_errors', 'kenaikan_skipped']);
            return redirect()->route('admin.kenaikan-kelas.index')
                ->withErrors(['file' => 'File sementara tidak ditemukan. Ulangi upload.']);
        }

        $import = (new KenaikanKelasImport())->setDryRun(false);
        Excel::import($import, $fullPath);

        // Hapus file & session
        Storage::disk('local')->delete($path);
        session()->forget(['kenaikan_tmp_path', 'kenaikan_preview', 'kenaikan_errors', 'kenaikan_skipped']);

        $msg = "Kenaikan kelas selesai: {$import->getUpdatedCount()} siswa naik kelas, {$import->getLulusCount()} siswa lulus/keluar.";

        return redirect()->route('admin.siswa.index')->with('success', $msg);
    }
}