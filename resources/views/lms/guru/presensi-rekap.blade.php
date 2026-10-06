{{-- resources/views/lms/guru/presensi-rekap.blade.php
     Variabel dari controller (nama alternatif dikenali, lihat @php):
       $pengampuMapel  → PengampuMapel
       $rekap          → koleksi per siswa: siswa (model) + hadir/izin/sakit/alpa
                         (atau $presensi = baris PresensiLms mentah, nanti dikelompokkan di sini)
       $totalPertemuan → opsional (kalau tidak ada, dihitung dari data)
       request('dari'), request('sampai') → filter tanggal (YYYY-MM-DD) --}}
@extends('lms.layouts.app')

@section('title', 'Rekap Presensi - LMS Yadika')
@section('breadcrumb', 'Rekap Presensi')

@section('content')
    @php
        use Illuminate\Support\Carbon;

        $batasKehadiran = 75; // minimal % kehadiran (aturan sekolah)

        $mp = $pengampuMapel->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        $kelasModel = $pengampuMapel->kelas ?? null;
        $namaKelas = $kelasModel->nama_kelas ?? ($kelasModel->nama ?? null);
        $semester = $pengampuMapel->semester ?? null;

        // --- normalisasi data rekap ---
        $sumber = $rekap ?? ($rekapSiswa ?? null);
        if ($sumber === null && isset($presensi)) {
            $sumber = collect($presensi)->groupBy('siswa_id')->map(function ($g) {
                $cnt = fn($st) => $g->filter(fn($x) => strtolower((string) $x->status) === $st)->count();
                return [
                    'siswa' => $g->first()->siswa ?? null,
                    'hadir' => $cnt('hadir'), 'izin' => $cnt('izin'), 'sakit' => $cnt('sakit'), 'alpa' => $cnt('alpa'),
                ];
            });
        }

        $baris = collect($sumber ?? [])->map(function ($r) use ($batasKehadiran) {
            $hadir = (int) data_get($r, 'hadir', 0);
            $izin  = (int) data_get($r, 'izin', 0);
            $sakit = (int) data_get($r, 'sakit', 0);
            $alpa  = (int) data_get($r, 'alpa', 0);
            $total = $hadir + $izin + $sakit + $alpa;
            $persen = $total > 0 ? round($hadir / $total * 100, 1) : 0;
            return (object) [
                'nama'  => data_get($r, 'siswa.nama') ?? (data_get($r, 'siswa.name') ?? (data_get($r, 'nama') ?? '-')),
                'nisn'  => data_get($r, 'siswa.nisn') ?? data_get($r, 'nisn'),
                'hadir' => $hadir, 'izin' => $izin, 'sakit' => $sakit, 'alpa' => $alpa,
                'total' => $total, 'persen' => $persen,
                'aman'  => $persen >= $batasKehadiran,
            ];
        })->sortBy(fn($r) => strtolower($r->nama))->values();

        $jumlahSiswa = $baris->count();
        $pertemuan = (int) ($totalPertemuan ?? $baris->max('total') ?? 0);
        $rataRata = $jumlahSiswa > 0 ? round($baris->avg('persen'), 1) : 0;
        $perluPerhatian = $baris->filter(fn($r) => $r->total > 0 && ! $r->aman)->count();
        $paripurna = $baris->filter(fn($r) => $r->hadir > 0 && ($r->izin + $r->sakit + $r->alpa) === 0)->count();
        $persenParipurna = $jumlahSiswa > 0 ? round($paripurna / $jumlahSiswa * 100) : 0;
        $persenPerhatian = $jumlahSiswa > 0 ? round($perluPerhatian / $jumlahSiswa * 100) : 0;

        $inisial = fn($n) => collect(preg_split('/\s+/', trim($n)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        // --- filter tanggal ---
        $dari = request('dari');
        $sampai = request('sampai');
        $urlRekap = fn(array $q = []) => route('lms.guru.presensi.rekap', ['pengampuMapel' => $pengampuMapel] + $q);
    @endphp

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('lms.guru.kelas.index') }}" class="hover:text-blue-600 transition-colors">Kelas Saya</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span>{{ $namaMapel }}@if ($namaKelas) ({{ $namaKelas }})@endif</span>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Rekap Presensi</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rekapitulasi Presensi Siswa</h1>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>{{ $namaMapel }}@if ($namaKelas) • {{ $namaKelas }}@endif</span>
                </div>
            </div>
        </div>
        <a href="{{ route('lms.guru.presensi.index', $pengampuMapel) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold shadow-sm hover:bg-slate-50 transition-all self-start md:self-auto">
            <i class="bi bi-arrow-left"></i><span>Kembali ke Presensi</span>
        </a>
    </div>

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold block">Total Pertemuan</span>
                    <div class="text-3xl font-bold text-slate-900 mt-1">{{ $pertemuan }} <span class="text-base font-semibold text-slate-500">Sesi</span></div>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-calendar-check text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-500">
                <i class="bi bi-journal-text text-blue-600"></i>
                <span>{{ $semester ? 'Semester ' . $semester : 'Periode berjalan' }}</span>
            </div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold block">Rata-rata Kehadiran</span>
                    <div class="text-3xl font-bold text-slate-900 mt-1">{{ $rataRata }}%</div>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-blue-700"><i class="bi bi-pie-chart-fill text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs {{ $rataRata >= $batasKehadiran ? 'text-emerald-700' : 'text-rose-600' }} font-medium">
                <span class="w-1.5 h-1.5 rounded-full {{ $rataRata >= $batasKehadiran ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                <span>{{ $rataRata >= $batasKehadiran ? 'Di atas' : 'Di bawah' }} batas {{ $batasKehadiran }}%</span>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full" style="width: {{ min(100, $rataRata) }}%"></div>
            </div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold block">Perlu Perhatian</span>
                    <div class="text-3xl font-bold {{ $perluPerhatian > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1">{{ $perluPerhatian }} <span class="text-base font-semibold text-slate-500">Siswa</span></div>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600"><i class="bi bi-bell-fill text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-rose-600 font-medium">
                <i class="bi bi-exclamation-triangle"></i><span>Kehadiran kritis (&lt;{{ $batasKehadiran }}%)</span>
            </div>
            <div class="w-full bg-rose-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-rose-500 h-full rounded-full" style="width: {{ $persenPerhatian }}%"></div>
            </div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold block">Presensi Paripurna</span>
                    <div class="text-3xl font-bold text-slate-900 mt-1">{{ $paripurna }} <span class="text-base font-semibold text-slate-500">Siswa</span></div>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i class="bi bi-patch-check-fill text-xl"></i></div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-500">
                <i class="bi bi-check-circle text-emerald-600"></i><span>Tanpa izin, sakit, maupun alpa</span>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $persenParipurna }}%"></div>
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-4">
        <form method="GET" action="{{ route('lms.guru.presensi.rekap', $pengampuMapel) }}" class="flex flex-col lg:flex-row lg:items-end gap-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label for="dari" class="text-xs font-semibold text-slate-500">Dari Tanggal</label>
                    <input id="dari" type="date" name="dari" value="{{ $dari }}"
                        class="h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
                </div>
                <div class="pb-2 text-slate-400 hidden sm:block"><i class="bi bi-arrow-right"></i></div>
                <div class="flex flex-col gap-1">
                    <label for="sampai" class="text-xs font-semibold text-slate-500">Sampai Tanggal</label>
                    <input id="sampai" type="date" name="sampai" value="{{ $sampai }}"
                        class="h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
                </div>
                <button type="submit"
                    class="h-10 px-4 rounded-lg bg-blue-600 text-white text-sm font-semibold flex items-center gap-2 hover:bg-blue-700 shadow-sm transition-all">
                    <i class="bi bi-funnel"></i><span>Tampilkan Data</span>
                </button>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 text-slate-500 text-sm lg:ml-auto self-start lg:self-auto">
                <i class="bi bi-check2-all text-blue-600"></i>
                <span>Tercatat: <strong class="text-slate-900">{{ $pertemuan }} Sesi</strong></span>
            </div>
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 mr-1">Rentang Cepat:</span>
            @php
                $presetAktif = ! $dari && ! $sampai;
                $pill = fn($aktif) => $aktif
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'bg-slate-100 hover:bg-slate-200 text-slate-600';
            @endphp
            <a href="{{ $urlRekap() }}" class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ $pill($presetAktif) }}">Semua Pertemuan</a>
            <a href="{{ $urlRekap(['dari' => now()->subDays(30)->toDateString(), 'sampai' => now()->toDateString()]) }}"
                class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ $pill($dari === now()->subDays(30)->toDateString() && $sampai === now()->toDateString()) }}">30 Hari Terakhir</a>
            <a href="{{ $urlRekap(['dari' => now()->startOfMonth()->toDateString(), 'sampai' => now()->endOfMonth()->toDateString()]) }}"
                class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ $pill($dari === now()->startOfMonth()->toDateString() && $sampai === now()->endOfMonth()->toDateString()) }}">Bulan Ini</a>
        </div>
    </section>

    {{-- TABEL --}}
    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-blue-600"><i class="bi bi-people-fill text-lg"></i></div>
                <div>
                    <h2 class="font-bold text-slate-900">Daftar Presensi & Kehadiran Per Siswa</h2>
                    <p class="text-xs text-slate-500">Total {{ $jumlahSiswa }} siswa{{ $namaKelas ? ' di kelas ' . $namaKelas : '' }}</p>
                </div>
            </div>
            <div class="relative w-full sm:w-64">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input id="cariSiswa" type="text" placeholder="Cari nama atau NISN..."
                    class="w-full h-10 pl-9 pr-3 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
            </div>
        </div>

        <div class="overflow-x-auto w-full custom-scrollbar">
            <table id="tabelRekap" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="py-3.5 px-5">Siswa</th>
                        <th class="py-3.5 px-4 text-center">Hadir</th>
                        <th class="py-3.5 px-4 text-center">Izin</th>
                        <th class="py-3.5 px-4 text-center">Sakit</th>
                        <th class="py-3.5 px-4 text-center">Alpa</th>
                        <th class="py-3.5 px-5 min-w-[200px]">% Kehadiran</th>
                        <th class="py-3.5 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
                    @forelse ($baris as $r)
                        @php $kritis = $r->total > 0 && ! $r->aman; @endphp
                        <tr class="baris-siswa transition-colors {{ $kritis ? 'bg-rose-50/40 hover:bg-rose-50' : 'hover:bg-slate-50' }}">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl font-bold flex items-center justify-center text-sm {{ $kritis ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">{{ $inisial($r->nama) }}</div>
                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-semibold text-slate-900 truncate">{{ $r->nama }}</span>
                                            @if ($kritis)<i class="bi bi-exclamation-circle-fill text-rose-500 text-sm"></i>@endif
                                        </div>
                                        @if ($r->nisn)<span class="text-xs font-mono text-slate-400">NISN: {{ $r->nisn }}</span>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center"><span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">{{ $r->hadir }}</span></td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($r->izin)<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700">{{ $r->izin }}</span>@else<span class="text-slate-400">0</span>@endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($r->sakit)<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700">{{ $r->sakit }}</span>@else<span class="text-slate-400">0</span>@endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($r->alpa)<span class="inline-block px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-700">{{ $r->alpa }}</span>@else<span class="text-slate-400">0</span>@endif
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $kritis ? 'bg-rose-500' : 'bg-blue-600' }}" style="width: {{ min(100, $r->persen) }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold w-14 text-right {{ $kritis ? 'text-rose-600' : 'text-blue-700' }}">{{ rtrim(rtrim(number_format($r->persen, 1), '0'), '.') }}%</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                @if ($r->total === 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">Belum ada data</span>
                                @elseif ($kritis)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Peringatan Absensi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>Memenuhi Batas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="bi bi-inbox text-3xl text-slate-300 block mb-2"></i>
                                Belum ada data presensi untuk rentang ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 bg-slate-50 text-sm text-slate-500">
            Menampilkan <span class="font-semibold text-slate-900">{{ $jumlahSiswa }}</span> siswa
        </div>
    </section>

    {{-- KETENTUAN --}}
    <div class="rounded-xl bg-blue-50 border border-blue-100 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0"><i class="bi bi-info-lg"></i></div>
            <p class="text-sm text-slate-600">
                <strong class="text-slate-900">Ketentuan Kehadiran:</strong>
                Batas minimum persentase kehadiran adalah <span class="text-blue-700 font-bold">{{ $batasKehadiran }}%</span> dari total tatap muka.
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs text-slate-500 font-medium">
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>Aman (≥{{ $batasKehadiran }}%)</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>Perhatian (&lt;{{ $batasKehadiran }}%)</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('cariSiswa')?.addEventListener('input', function (e) {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('#tabelRekap .baris-siswa').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
@endpush