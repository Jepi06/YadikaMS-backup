<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\GuruPembimbing;
use Illuminate\Http\Request;

class GuruPembimbingController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruPembimbing::withCount('penempatanPkl');

        if ($request->filled('search')) {
            // PERBAIKAN: sama seperti TempatPklController, orWhere dibungkus closure
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nip', 'like', "%{$request->search}%");
            });
        }

        $guru = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('Mapping.guru.index', compact('guru'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nip'   => 'nullable|string|unique:guru_pembimbing,nip',
            'nama'  => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        GuruPembimbing::create($data);

        return back()->with('success', 'Guru pembimbing berhasil ditambahkan.');
    }

    public function update(Request $request, GuruPembimbing $guru)
    {
        $data = $request->validate([
            'nip'   => 'nullable|string|unique:guru_pembimbing,nip,' . $guru->id,
            'nama'  => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $guru->update($data);

        return back()->with('success', 'Data guru pembimbing berhasil diperbarui.');
    }

    public function destroy(GuruPembimbing $guru)
    {
        $guru->delete();

        return back()->with('success', 'Guru pembimbing berhasil dihapus.');
    }
}
