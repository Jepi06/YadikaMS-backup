<?php

// app/Http/Controllers/SuperAdmin/MataPelajaranController.php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Imports\MataPelajaranImport;
use App\Models\Jurusan;
use App\Models\Lms\MataPelajaran;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $q         = $request->q;
        $jurusanId = $request->jurusan_id;

        $mapel = MataPelajaran::with('jurusan')
            ->when($q, fn($query) => $query->where(fn($sub) => $sub
                ->where('nama', 'like', "%{$q}%")
                ->orWhere('kode', 'like', "%{$q}%")))
            ->when($jurusanId === 'umum', fn($query) => $query->whereNull('jurusan_id'))
            ->when($jurusanId && $jurusanId !== 'umum', fn($query) => $query->where('jurusan_id', $jurusanId))
            ->orderByRaw('jurusan_id IS NULL DESC')
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        $jurusan = Jurusan::orderBy('nama')->get();

        return view('admin.mata-pelajaran.index', compact('mapel', 'jurusan', 'q', 'jurusanId'));
    }

    public function create()
    {
        $jurusan = Jurusan::orderBy('nama')->get();
        return view('admin.mata-pelajaran.create', compact('jurusan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'       => ['required', 'string', 'max:20', 'unique:mata_pelajaran,kode'],
            'nama'       => ['required', 'string', 'max:255'],
            'jurusan_id' => ['nullable', 'exists:jurusan,id'],
        ], [
            'kode.unique' => 'Kode mata pelajaran sudah digunakan.',
        ]);

        MataPelajaran::create($request->only('kode', 'nama', 'jurusan_id'));

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $jurusan = Jurusan::orderBy('nama')->get();
        return view('admin.mata-pelajaran.edit', compact('mataPelajaran', 'jurusan'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'kode'       => ['required', 'string', 'max:20', "unique:mata_pelajaran,kode,{$mataPelajaran->id}"],
            'nama'       => ['required', 'string', 'max:255'],
            'jurusan_id' => ['nullable', 'exists:jurusan,id'],
        ]);

        $mataPelajaran->update($request->only('kode', 'nama', 'jurusan_id'));

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        if ($mataPelajaran->pengampuMapel()->exists()) {
            return back()->withErrors(['error' => 'Mata pelajaran ini masih digunakan di penugasan mengajar.']);
        }

        $mataPelajaran->delete();

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    // ── Import ────────────────────────────────────────────────────────────────

    public function importForm()
    {
        return view('admin.mata-pelajaran.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new MataPelajaranImport();
        Excel::import($import, $request->file('file'));

        $imported = $import->getImportedCount();
        $skipped  = $import->getSkippedRows();
        $errors   = $import->getErrors();

        $msg = "Import selesai: {$imported} mata pelajaran berhasil ditambahkan.";
        if (count($skipped) > 0) {
            $msg .= ' ' . count($skipped) . ' baris dilewati (kode duplikat).';
        }

        $request->session()->put('import_mapel_skipped', $skipped);
        $request->session()->put('import_mapel_errors',  $errors);

        return redirect()->route('admin.mata-pelajaran.index')->with('success', $msg);
    }

    public function downloadTemplate()
    {
        return (new MataPelajaranImport())->downloadTemplate();
    }
}