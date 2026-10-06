<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\TempatPkl;
use Illuminate\Http\Request;

class TempatPklController extends Controller
{
    public function index(Request $request)
    {
        $query = TempatPkl::withCount('penempatanPkl');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_tempat', 'like', "%{$request->search}%")
                    ->orWhere('bidang_usaha', 'like', "%{$request->search}%");
            });
        }

        $tempat = $query->orderBy('nama_tempat')->paginate(15)->withQueryString();

        return view('Mapping.tempat.index', compact('tempat'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_tempat' => 'required|string|max:255',
            'alamat' => 'required|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'nama_kontak' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'kuota_maksimal' => 'nullable|integer|min:1', // kosong = tidak dibatasi
        ]);

        TempatPkl::create($data);

        return back()->with('success', 'Tempat PKL berhasil ditambahkan.');
    }

    public function update(Request $request, TempatPkl $tempat)
    {
        $data = $request->validate([
            'nama_tempat' => 'required|string|max:255',
            'alamat' => 'required|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'nama_kontak' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'kuota_maksimal' => 'nullable|integer|min:1',
        ]);

        // Cegah admin set kuota lebih kecil dari jumlah yang sudah terisi
        // (misal sudah 7 orang aktif, tidak boleh diset jadi 3).
        if (
            $data['kuota_maksimal'] !== null &&
            $data['kuota_maksimal'] < $tempat->jumlahTerisi()
        ) {
            return back()->with(
                'error',
                "Kuota tidak bisa diset lebih kecil dari jumlah siswa yang sudah ditempatkan ({$tempat->jumlahTerisi()} orang)."
            );
        }

        $tempat->update($data);

        return back()->with('success', 'Data tempat PKL berhasil diperbarui.');
    }

    public function destroy(TempatPkl $tempat)
    {
        $tempat->delete();

        return back()->with('success', 'Tempat PKL berhasil dihapus.');
    }
}
