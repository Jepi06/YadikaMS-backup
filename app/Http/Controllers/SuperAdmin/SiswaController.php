<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Imports\SiswaImport;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = Siswa::with(['kelas.jurusan', 'kelas.waliKelas'])
            ->search($request->q)
            ->filterKelas($request->kelas_id)
            ->filterJurusan($request->jurusan_id)
            ->filterTingkat($request->tingkat)
            ->latest();

        $siswa   = $query->paginate(20)->withQueryString();
        $kelas   = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $jurusan = Jurusan::orderBy('nama')->get();

        return view('admin.siswa.index', compact('siswa', 'kelas', 'jurusan'));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        $kelas = Kelas::with(['jurusan', 'waliKelas'])  // tambah waliKelas
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('admin.siswa.create', compact('kelas'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Login siswa memakai NIS sebagai "email" (sama seperti import)
        if (User::where('email', $data['nis'])->exists()) {
            return back()->withInput()->withErrors([
                'nis' => 'Sudah ada akun dengan NIS ini.',
            ]);
        }

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'           => $data['nama'],
                'email'          => $data['nis'],
                'password'       => Hash::make('password'),
                'is_active'      => true,
                'is_super_admin' => false,
            ]);

            $roleSiswaId = Role::whereHas('module', fn($m) => $m->where('kode', 'lms'))
                ->where('kode', 'siswa')
                ->value('id');

            if ($roleSiswaId) {
                $user->roles()->attach($roleSiswaId, ['assigned_at' => now()]);
            }

            Siswa::create($data + ['user_id' => $user->id]);
        });

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', "Siswa {$data['nama']} ditambahkan. Login: NIS {$data['nis']}, password default: password.");
    }
    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Siswa $siswa): View
    {
        $siswa->load(['kelas.jurusan', 'kelas.waliKelas', 'user']);
        return view('admin.siswa.show', compact('siswa'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::with(['jurusan', 'waliKelas'])  // tambah waliKelas
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($siswa, $data) {
            $siswa->update($data);

            if ($siswa->user) {
                $siswa->user->update([
                    'name'  => $data['nama'] ?? $siswa->nama,
                    'email' => $data['nis'] ?? $siswa->nis,
                ]);
            }
        });

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
    // ── Reset Password ───────────────────────────────────────────────────────

    public function resetPassword(Siswa $siswa): RedirectResponse
    {
        $user = $siswa->user;

        if (! $user) {
            return back()->withErrors([
                'siswa' => "Siswa {$siswa->nama} belum punya akun login, tidak ada password untuk direset.",
            ]);
        }

        $user->update([
            'password' => Hash::make('password'),
        ]);

        return back()->with('success', "Password akun {$siswa->nama} berhasil direset ke default: \"password\".");
    }
    // ── Import Excel ──────────────────────────────────────────────────────────

    public function importForm()
    {
        $kelas = \App\Models\Kelas::with(['jurusan', 'waliKelas'])
            ->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('admin.siswa.import', compact('kelas'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'file'     => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        $import = new SiswaImport();
        $import->kelasId = (int) $request->kelas_id;

        Excel::import($import, $request->file('file'));

        $msg = "Import selesai: {$import->getImportedCount()} siswa berhasil dimasukkan.";
        $request->session()->put('import_skipped', $import->getSkippedRows());
        $request->session()->put('import_errors',  $import->getErrors());

        return redirect()->route('admin.siswa.index')->with('success', $msg);
    }
    // ── Download Template ─────────────────────────────────────────────────────

    public function downloadTemplate()
    {
        $import = new SiswaImport();
        return $import->downloadTemplate();
    }
}
