<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lms\JadwalPelajaran;
use App\Models\Lms\PengampuMapel;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $hari = $request->query('hari');
        $q = $request->query('q');

        $jadwal = JadwalPelajaran::with(['pengampuMapel.guru', 'pengampuMapel.mataPelajaran', 'pengampuMapel.kelas'])
            ->when($hari, fn ($qr) => $qr->where('hari', $hari))
            ->when($q, fn ($qr) => $qr->whereHas('pengampuMapel', fn ($p) => $p
                ->whereHas('guru', fn ($g) => $g->where('name', 'like', "%{$q}%"))
                ->orWhereHas('kelas', fn ($k) => $k->where('nama_kelas', 'like', "%{$q}%"))))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(25)
            ->withQueryString();

        return view('admin.jadwal.index', compact('jadwal', 'hari', 'q'));
    }

    public function create()
    {
        return view('admin.jadwal.form', [
            'jadwal'   => null,
            'pengampu' => $this->daftarPengampu(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($pesan = $this->bentrok($data)) {
            return back()->withInput()->withErrors(['jam_mulai' => $pesan]);
        }

        JadwalPelajaran::create($data);

        return redirect()->route('admin.jadwal.index')->with('status', 'Jadwal ditambahkan.');
    }

    public function edit(JadwalPelajaran $jadwal)
    {
        return view('admin.jadwal.form', [
            'jadwal'   => $jadwal,
            'pengampu' => $this->daftarPengampu(),
        ]);
    }

    public function update(Request $request, JadwalPelajaran $jadwal)
    {
        $data = $this->validated($request);

        if ($pesan = $this->bentrok($data, $jadwal->id)) {
            return back()->withInput()->withErrors(['jam_mulai' => $pesan]);
        }

        $jadwal->update($data);

        return redirect()->route('admin.jadwal.index')->with('status', 'Jadwal diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwal)
    {
        $jadwal->delete();

        return back()->with('status', 'Jadwal dihapus.');
    }

    // ── helper ─────────────────────────────────────────────

    private function validated(Request $request): array
    {
        return $request->validate([
            'pengampu_mapel_id' => ['required', 'exists:pengampu_mapel,id'],
            'hari'              => ['required', 'integer', 'between:1,6'],
            'jam_mulai'         => ['required', 'date_format:H:i'],
            'jam_selesai'       => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'ruangan'           => ['nullable', 'string', 'max:50'],
        ]);
    }

    /** Penugasan mengajar periode aktif, untuk dropdown. */
    private function daftarPengampu()
    {
        return PengampuMapel::with(['guru', 'mataPelajaran', 'kelas'])
            ->where('tahun_ajaran', TahunAjaran::sekarang())
            ->where('semester', TahunAjaran::semesterSekarang())
            ->get()
            ->sortBy(fn ($p) => ($p->guru->name ?? '') . ($p->kelas->nama_kelas ?? ''))
            ->values();
    }

    /** Cek bentrok jam: guru yang sama ATAU kelas yang sama di hari & jam beririsan. */
    private function bentrok(array $d, ?int $ignoreId = null): ?string
    {
        $p = PengampuMapel::findOrFail($d['pengampu_mapel_id']);

        $irisan = fn ($q) => $q->where('hari', $d['hari'])
            ->where('jam_mulai', '<', $d['jam_selesai'])
            ->where('jam_selesai', '>', $d['jam_mulai'])
            ->when($ignoreId, fn ($x) => $x->where('id', '!=', $ignoreId));

        $guruBentrok = $irisan(JadwalPelajaran::query())
            ->whereHas('pengampuMapel', fn ($x) => $x->where('guru_id', $p->guru_id))
            ->exists();

        if ($guruBentrok) {
            return 'Guru ini sudah punya jadwal lain di hari dan jam yang beririsan.';
        }

        $kelasBentrok = $irisan(JadwalPelajaran::query())
            ->whereHas('pengampuMapel', fn ($x) => $x->where('kelas_id', $p->kelas_id))
            ->exists();

        if ($kelasBentrok) {
            return 'Kelas ini sudah punya pelajaran lain di hari dan jam yang beririsan.';
        }

        return null;
    }
}