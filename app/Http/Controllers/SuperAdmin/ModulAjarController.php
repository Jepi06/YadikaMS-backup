<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lms\ModulAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModulAjarController extends Controller
{
    /** Arsip modul ajar dari SEMUA guru — buat admin/kepala sekolah. */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $modulAjar = ModulAjar::with(['pengampuMapel.guru', 'pengampuMapel.mataPelajaran', 'pengampuMapel.kelas'])
            ->when($q, fn($query) => $query->where('judul', 'like', "%{$q}%")
                ->orWhereHas('pengampuMapel.guru', fn($g) => $g->where('name', 'like', "%{$q}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.modul-ajar.index', compact('modulAjar', 'q'));
    }

    /**
     * Serve file modul ajar khusus buat Panel Super Admin. Dibikin
     * terpisah dari Lms\FileController karena panel ini guard-agnostic
     * (middleware-nya 'super.admin', bukan 'auth.lms') — admin/kepsek
     * bisa aja lagi login lewat PKL atau SPMB pas buka arsip ini.
     */
    public function file(ModulAjar $modulAjar)
    {
        return Storage::disk('public')->response($modulAjar->file_path);
    }
}
