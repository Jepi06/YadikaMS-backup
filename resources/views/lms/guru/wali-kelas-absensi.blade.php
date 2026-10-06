{{-- resources/views/lms/guru/wali-kelas-absensi.blade.php
     Variabel dari WaliKelasController@absensi:
       $kelasDiwalikan, $kelas, $periodeList, $tahunAjaran, $semester,
       $hariMasukPerBulan  → Collection ['Y-m' => jumlah hari masuk]
       $totalHariMasuk
       $rekap              → array of object {siswa, per_bulan[Y-m] = [hadir, hari_masuk, persen], total_hadir, total_hari_masuk, total_persen}
       $rekapKelasPerBulan → ['Y-m' => [hari_aktif, slot, hadir, izin, sakit, alpa, persen_*]]
       $rekapKelasTotal    → [slot, hadir, izin, sakit, alpa, persen_*]
       $bulanDipilih, $tanggalList (Collection Y-m-d)
       $detailHarian       → [siswa_id][Y-m-d] = ['status' => 'Hadir', 'rincian' => 'Hadir 3, Alpa 1']
     Semua relasi sudah di-load di controller (lazy loading dimatikan). --}}
@extends('lms.layouts.app')

@section('title', 'Rekap Absensi Wali Kelas - LMS Yadika')
@section('breadcrumb', 'Rekap Absensi')

@section('content')
    @php
        $C = \Illuminate\Support\Carbon::class;

        $namaKelasFn = fn($k) => $k->nama_kelas ?? ($k->nama ?? '-');
        $namaKelas = $namaKelasFn($kelas);
        $namaSiswa = fn($s) => $s->nama ?? ($s->name ?? '-');
        $inisial = fn($n) => collect(preg_split('/\s+/', trim($n)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        $bulanPanjang = fn($ym) => $C::createFromFormat('Y-m-d', $ym . '-01')->translatedFormat('F Y');
        $bulanPendek = fn($ym) => $C::createFromFormat('Y-m-d', $ym . '-01')->translatedFormat('M Y');

        $daftarTahun = $periodeList->pluck('tahun_ajaran')->push($tahunAjaran)->filter()->unique()->values();
        $daftarSemester = $periodeList->pluck('semester')->push($semester)->filter()->unique()->values();

        $statusStyle = [
            'Hadir' => ['H', 'bg-emerald-100 text-emerald-800'],
            'Izin' => ['I', 'bg-blue-100 text-blue-800'],
            'Sakit' => ['S', 'bg-amber-100 text-amber-800'],
            'Alpa' => ['A', 'bg-rose-100 text-rose-800'],
        ];

        $warnaPersen = fn($p) => $p >= 90 ? 'text-emerald-700' : ($p >= 75 ? 'text-amber-700' : 'text-rose-700');
        $labelPersen = function ($p) {
            if ($p >= 100) return ['Sempurna', 'bg-emerald-100 text-emerald-800'];
            if ($p >= 90) return ['Aman', 'bg-emerald-100 text-emerald-800'];
            if ($p >= 75) return ['Waspada', 'bg-amber-100 text-amber-800'];
            return ['Kritis', 'bg-rose-100 text-rose-800'];
        };

        $ringkasHarian = function ($siswaId) use ($detailHarian, $tanggalList) {
            $c = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];
            foreach ($tanggalList as $t) {
                $st = $detailHarian[$siswaId][$t]['status'] ?? null;
                if ($st && isset($c[$st])) $c[$st]++;
            }
            return $c;
        };

        $rekapUrut = collect($rekap)->sortBy(fn($r) => strtolower($namaSiswa($r->siswa)))->values();
        $jumlahSiswa = $rekapUrut->count();
        $adaData = $hariMasukPerBulan->isNotEmpty();

        $nKritis = $rekapUrut->filter(fn($r) => $r->total_hari_masuk > 0 && $r->total_persen < 75)->count();
        $nSempurna = $rekapUrut->filter(fn($r) => $r->total_hari_masuk > 0 && $r->total_persen >= 100)->count();
        $persenSempurna = $jumlahSiswa > 0 ? round($nSempurna / $jumlahSiswa * 100, 1) : 0;
        $persenHadirKelas = $rekapKelasTotal['persen_hadir'] ?? 0;
        $hariBulanDipilih = $bulanDipilih ? ($hariMasukPerBulan[$bulanDipilih] ?? 0) : 0;

        $paramPeriode = ['kelas_id' => $kelas->id, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester];
    @endphp

    {{-- HEADER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1.5 min-w-0">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('lms.guru.wali-kelas.index', $paramPeriode) }}" class="hover:text-blue-600 transition-colors">Wali Kelas</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold">{{ $namaKelas }}</span>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Rekap Absensi</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3 pt-0.5">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rekap Absensi — Wali Kelas</h1>
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                    {{ $namaKelas }} • Semester {{ $semester }} {{ $tahunAjaran }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 self-start lg:self-center">
            <a href="{{ route('lms.guru.wali-kelas.index', $paramPeriode) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                <i class="bi bi-award"></i><span>Lihat Rekap Nilai</span>
            </a>
        </div>
    </section>

    {{-- FILTER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
        <form id="filterAbsensi" method="GET" action="{{ route('lms.guru.wali-kelas.absensi') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="space-y-1.5">
                <label for="fKelas" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i class="bi bi-people text-blue-600"></i>Rombongan Belajar (Kelas)</label>
                <select id="fKelas" name="kelas_id"
                    class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    @foreach ($kelasDiwalikan as $k)
                        <option value="{{ $k->id }}" @selected($k->id === $kelas->id)>{{ $namaKelasFn($k) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label for="fTahun" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i class="bi bi-calendar3 text-blue-600"></i>Tahun Ajaran</label>
                <select id="fTahun" name="tahun_ajaran"
                    class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    @foreach ($daftarTahun as $th)
                        <option value="{{ $th }}" @selected($th == $tahunAjaran)>{{ $th }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label for="fSemester" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i class="bi bi-hourglass-split text-blue-600"></i>Semester</label>
                <select id="fSemester" name="semester"
                    class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    @foreach ($daftarSemester as $sm)
                        <option value="{{ $sm }}" @selected($sm == $semester)>{{ $sm }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label for="fBulan" class="text-xs font-semibold text-slate-600 flex items-center gap-1.5"><i class="bi bi-calendar-event text-blue-600"></i>Bulan (Detail Harian)</label>
                <select id="fBulan" name="bulan" @disabled(! $adaData)
                    class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 disabled:opacity-60">
                    @forelse ($hariMasukPerBulan as $ym => $hari)
                        <option value="{{ $ym }}" @selected($ym === $bulanDipilih)>{{ $bulanPanjang($ym) }} ({{ $hari }} hari masuk)</option>
                    @empty
                        <option value="">Belum ada data</option>
                    @endforelse
                </select>
            </div>
        </form>
    </section>

    @if (! $adaData)
        <section class="p-10 rounded-xl bg-white border border-dashed border-slate-300 text-center space-y-2">
            <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-slate-400"><i class="bi bi-calendar-x text-xl"></i></div>
            <p class="font-semibold text-slate-800">Belum ada data presensi</p>
            <p class="text-sm text-slate-500">Belum ada presensi tercatat untuk {{ $namaKelas }} pada semester {{ $semester }} {{ $tahunAjaran }}.</p>
        </section>
    @else
        {{-- KPI --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Rata-rata Kehadiran</p>
                    <p class="text-3xl font-bold {{ $warnaPersen($persenHadirKelas) }}">{{ $persenHadirKelas }}%</p>
                    <span class="text-xs text-slate-500">Target minimal 90%</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i class="bi bi-patch-check-fill text-2xl"></i></div>
            </div>
            <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Hari Masuk</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $totalHariMasuk }} <span class="text-sm font-normal text-slate-500">hari</span></p>
                    <span class="text-xs text-slate-500">{{ $jumlahSiswa }} siswa aktif</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-calendar-check text-2xl"></i></div>
            </div>
            <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Kehadiran Kritis (&lt;75%)</p>
                    <p class="text-3xl font-bold {{ $nKritis > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $nKritis }} <span class="text-sm font-normal text-slate-500">siswa</span></p>
                    @if ($nKritis > 0)
                        <span class="text-xs text-rose-600 font-semibold">Perlu tindak lanjut wali kelas</span>
                    @else
                        <span class="text-xs text-emerald-600 font-semibold">Tidak ada siswa kritis</span>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600"><i class="bi bi-exclamation-triangle-fill text-2xl"></i></div>
            </div>
            <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Presensi Sempurna (100%)</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $nSempurna }} <span class="text-sm font-normal text-slate-500">siswa</span></p>
                    <span class="text-xs text-blue-700 font-semibold">{{ $persenSempurna }}% dari kelas</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-blue-700"><i class="bi bi-trophy-fill text-2xl"></i></div>
            </div>
        </section>

        {{-- DETAIL HARIAN --}}
        <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-5 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="bi bi-calendar3-week text-xl"></i></div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg">Detail Harian — {{ $bulanDipilih ? $bulanPanjang($bulanDipilih) : '-' }}</h2>
                        <p class="text-sm text-slate-500">Status presensi harian seluruh siswa {{ $namaKelas }} ({{ $hariBulanDipilih }} hari masuk)</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                    <span class="text-slate-500">Legenda:</span>
                    <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">H Hadir</span>
                    <span class="px-2 py-1 rounded bg-blue-50 text-blue-800 border border-blue-200">I Izin</span>
                    <span class="px-2 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200">S Sakit</span>
                    <span class="px-2 py-1 rounded bg-rose-50 text-rose-800 border border-rose-200">A Alpa</span>
                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">– Tidak ada data</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 custom-scrollbar">
                <table class="w-full border-collapse text-left text-sm" style="min-width: {{ 300 + $tanggalList->count() * 40 + 160 }}px">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-[11px] font-semibold border-b border-slate-200">
                            <th class="sticky left-0 z-20 bg-slate-50 px-4 py-3 min-w-[220px] border-r border-slate-200 text-xs uppercase tracking-wider">Nama Siswa</th>
                            @foreach ($tanggalList as $tgl)
                                @php $d = $C::parse($tgl); @endphp
                                <th class="px-1 py-2 text-center border-r border-slate-100">
                                    <div class="text-slate-400 font-normal">{{ $d->translatedFormat('D') }}</div>
                                    <div class="text-slate-800 font-bold">{{ $d->format('d') }}</div>
                                </th>
                            @endforeach
                            <th class="px-2 py-2 text-center bg-emerald-50 text-emerald-800 border-l border-emerald-100">H</th>
                            <th class="px-2 py-2 text-center bg-blue-50 text-blue-800">I</th>
                            <th class="px-2 py-2 text-center bg-amber-50 text-amber-800">S</th>
                            <th class="px-2 py-2 text-center bg-rose-50 text-rose-800">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rekapUrut as $r)
                            @php
                                $sid = $r->siswa->id;
                                $nama = $namaSiswa($r->siswa);
                                $sum = $ringkasHarian($sid);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors {{ $sum['Alpa'] > 0 ? 'bg-rose-50/30' : '' }}">
                                <td class="sticky left-0 z-10 bg-white px-4 py-2.5 border-r border-slate-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold text-[11px] flex items-center justify-center shrink-0">{{ $inisial($nama) }}</div>
                                        <span class="font-medium text-slate-900 truncate">{{ $nama }}</span>
                                    </div>
                                </td>
                                @foreach ($tanggalList as $tgl)
                                    @php
                                        $cell = $detailHarian[$sid][$tgl] ?? null;
                                        $st = $cell['status'] ?? null;
                                        [$huruf, $kls] = $statusStyle[$st] ?? [null, null];
                                    @endphp
                                    <td class="p-1 text-center">
                                        @if ($huruf)
                                            <span title="{{ $cell['rincian'] ?? $st }}" class="inline-block w-6 py-0.5 rounded text-xs font-bold {{ $kls }}">{{ $huruf }}</span>
                                        @else
                                            <span class="inline-block w-6 py-0.5 rounded text-xs text-slate-300">–</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-2 py-2 text-center font-semibold bg-emerald-50/50 text-emerald-800 border-l border-emerald-100">{{ $sum['Hadir'] }}</td>
                                <td class="px-2 py-2 text-center font-semibold bg-blue-50/50 text-blue-800">{{ $sum['Izin'] }}</td>
                                <td class="px-2 py-2 text-center font-semibold bg-amber-50/50 text-amber-800">{{ $sum['Sakit'] }}</td>
                                <td class="px-2 py-2 text-center font-semibold {{ $sum['Alpa'] > 0 ? 'bg-rose-100 text-rose-700 font-bold' : 'bg-rose-50/50 text-rose-800' }}">{{ $sum['Alpa'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100">
                <i class="bi bi-info-circle text-blue-700 mt-0.5"></i>
                <p class="text-sm text-slate-500 leading-relaxed">
                    <span class="font-semibold text-slate-900">Aturan absensi harian:</span> jika dalam satu hari siswa punya status berbeda di beberapa mapel,
                    status terberat yang ditampilkan (<span class="text-rose-700 font-semibold">Alpa</span> &gt; <span class="text-amber-700 font-semibold">Sakit</span> &gt;
                    <span class="text-blue-700 font-semibold">Izin</span> &gt; <span class="text-emerald-700 font-semibold">Hadir</span>). Arahkan kursor ke sel untuk melihat rincian per mapel.
                </p>
            </div>
        </section>

        {{-- REKAP PERSENTASE PER SISWA --}}
        <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-5 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="bi bi-bar-chart-line text-xl"></i></div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg">Rekap Persentase Per Siswa (Semester {{ $semester }})</h2>
                        <p class="text-sm text-slate-500">Tingkat kehadiran per bulan dan kumulatif semester</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                    <span class="text-slate-500">Status:</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Aman (≥90%)</span>
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800">Waspada (75–89%)</span>
                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800">Kritis (&lt;75%)</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 custom-scrollbar">
                <table class="w-full border-collapse text-left text-sm" style="min-width: {{ 260 + $hariMasukPerBulan->count() * 120 + 260 }}px">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                            <th class="sticky left-0 z-20 bg-slate-50 px-4 py-3 min-w-[240px] border-r border-slate-200 text-xs uppercase tracking-wider">Identitas Siswa</th>
                            @foreach ($hariMasukPerBulan as $ym => $hari)
                                <th class="px-3 py-3 text-center border-r border-slate-100">
                                    <div class="font-bold text-slate-800">{{ $bulanPendek($ym) }}</div>
                                    <div class="text-[11px] text-slate-400 font-normal">{{ $hari }} hari masuk</div>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center bg-blue-50 border-l border-blue-200 text-blue-700">
                                <div class="font-bold">Total Semester</div>
                                <div class="text-[11px] font-normal">{{ $totalHariMasuk }} hari masuk</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rekapUrut as $r)
                            @php
                                $nama = $namaSiswa($r->siswa);
                                [$lbl, $lblKls] = $r->total_hari_masuk > 0 ? $labelPersen($r->total_persen) : ['-', 'bg-slate-100 text-slate-500'];
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors {{ $r->total_hari_masuk > 0 && $r->total_persen < 75 ? 'bg-rose-50/30' : '' }}">
                                <td class="sticky left-0 z-10 bg-white px-4 py-3 border-r border-slate-200">
                                    <div class="font-semibold text-slate-900">{{ $nama }}</div>
                                    @if ($r->siswa->nisn ?? null)<div class="text-xs font-mono text-slate-400">NISN: {{ $r->siswa->nisn }}</div>@endif
                                </td>
                                @foreach ($hariMasukPerBulan as $ym => $hari)
                                    @php $pb = $r->per_bulan[$ym] ?? ['hadir' => 0, 'hari_masuk' => $hari, 'persen' => 0]; @endphp
                                    <td class="px-3 py-3 text-center border-r border-slate-100">
                                        <div class="font-bold {{ $warnaPersen($pb['persen']) }}">{{ $pb['persen'] }}%</div>
                                        <div class="text-xs font-mono text-slate-400">{{ $pb['hadir'] }}/{{ $pb['hari_masuk'] }}</div>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 bg-blue-50/50 border-l border-blue-200">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="font-bold text-base {{ $warnaPersen($r->total_persen) }}">{{ $r->total_persen }}%</div>
                                            <div class="text-xs font-mono text-slate-400">{{ $r->total_hadir }}/{{ $r->total_hari_masuk }} hari</div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $lblKls }}">{{ $lbl }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- REKAP AGREGAT KELAS --}}
        <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="bi bi-clipboard-data text-xl"></i></div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg">Rekap Agregat Kelas ({{ $namaKelas }})</h2>
                        <p class="text-sm text-slate-500">Total frekuensi status presensi seluruh siswa per bulan</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-blue-700 text-xs font-semibold">
                    <i class="bi bi-people"></i>Jumlah Siswa: {{ $jumlahSiswa }}
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 custom-scrollbar">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="px-4 py-3 min-w-[220px]">Periode Bulan</th>
                            <th class="px-4 py-3 text-center">Hadir (H)</th>
                            <th class="px-4 py-3 text-center">Izin (I)</th>
                            <th class="px-4 py-3 text-center">Sakit (S)</th>
                            <th class="px-4 py-3 text-center">Alpa (A)</th>
                            <th class="px-4 py-3 text-right">Tingkat Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rekapKelasPerBulan as $ym => $rk)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $bulanPanjang($ym) }}@if ($ym === $bulanDipilih) <span class="text-xs font-normal text-blue-600">(dipilih)</span>@endif</div>
                                    <div class="text-xs font-mono text-slate-400">{{ $rk['hari_aktif'] }} hari × {{ $jumlahSiswa }} siswa = {{ number_format($rk['slot']) }} slot</div>
                                </td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-emerald-800">{{ number_format($rk['hadir']) }}</span> <span class="text-xs text-slate-400">({{ $rk['persen_hadir'] }}%)</span></td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-blue-800">{{ number_format($rk['izin']) }}</span> <span class="text-xs text-slate-400">({{ $rk['persen_izin'] }}%)</span></td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-amber-800">{{ number_format($rk['sakit']) }}</span> <span class="text-xs text-slate-400">({{ $rk['persen_sakit'] }}%)</span></td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-rose-800">{{ number_format($rk['alpa']) }}</span> <span class="text-xs text-slate-400">({{ $rk['persen_alpa'] }}%)</span></td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold bg-slate-50 border border-slate-200 {{ $warnaPersen($rk['persen_hadir']) }}">{{ $rk['persen_hadir'] }}%</span>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-blue-600 text-white">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-base">Keseluruhan Semester</div>
                                <div class="text-xs font-mono text-blue-100">{{ $totalHariMasuk }} hari × {{ $jumlahSiswa }} siswa = {{ number_format($rekapKelasTotal['slot']) }} slot</div>
                            </td>
                            <td class="px-4 py-3.5 text-center"><div class="font-bold text-base">{{ number_format($rekapKelasTotal['hadir']) }}</div><div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_hadir'] }}%)</div></td>
                            <td class="px-4 py-3.5 text-center"><div class="font-bold text-base">{{ number_format($rekapKelasTotal['izin']) }}</div><div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_izin'] }}%)</div></td>
                            <td class="px-4 py-3.5 text-center"><div class="font-bold text-base">{{ number_format($rekapKelasTotal['sakit']) }}</div><div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_sakit'] }}%)</div></td>
                            <td class="px-4 py-3.5 text-center"><div class="font-bold text-base text-rose-200">{{ number_format($rekapKelasTotal['alpa']) }}</div><div class="text-xs text-blue-100">({{ $rekapKelasTotal['persen_alpa'] }}%)</div></td>
                            <td class="px-4 py-3.5 text-right"><span class="inline-block px-3 py-1 rounded-md text-sm font-bold bg-white text-blue-700 shadow-sm">{{ $rekapKelasTotal['persen_hadir'] }}%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-slate-50 border border-slate-200/70 rounded-xl p-4 flex items-start gap-3">
            <i class="bi bi-calculator text-slate-400 mt-0.5"></i>
            <p class="text-sm text-slate-500 leading-relaxed">
                <span class="font-semibold text-slate-900">Catatan perhitungan:</span>
                persentase per siswa = <span class="font-mono text-slate-800">hari hadir ÷ hari masuk</span> (hari masuk = tanggal unik yang punya presensi di mapel mana pun).
                Persentase kelas = <span class="font-mono text-slate-800">total kejadian status ÷ (hari masuk × jumlah siswa)</span>.
                Data diambil dari presensi seluruh mapel di kelas ini pada periode terpilih.
            </p>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('filterAbsensi');
            if (!form) return;
            const f = id => document.getElementById(id);

            // Ganti kelas → reset periode & bulan (periodenya bisa beda per kelas)
            f('fKelas')?.addEventListener('change', () => {
                ['fTahun', 'fSemester', 'fBulan'].forEach(i => f(i) && (f(i).disabled = true));
                form.submit();
            });
            // Ganti periode → reset bulan
            ['fTahun', 'fSemester'].forEach(i => f(i)?.addEventListener('change', () => {
                f('fBulan') && (f('fBulan').disabled = true);
                form.submit();
            }));
            f('fBulan')?.addEventListener('change', () => form.submit());
        })();
    </script>
@endpush