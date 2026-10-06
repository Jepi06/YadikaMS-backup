
@extends('lms.layouts.app')

@section('title', 'Rekap Nilai Wali Kelas - LMS Yadika')
@section('breadcrumb', 'Rekap Nilai')

@section('content')
    @php
        $KKM = 75;

        $namaKelasFn = fn($k) => $k->nama_kelas ?? ($k->nama ?? '-');
        $namaKelas = $namaKelasFn($kelas);
        $namaSiswa = fn($s) => $s->nama ?? ($s->name ?? '-');
        $inisial = fn($n) => collect(preg_split('/\s+/', trim($n)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        // --- Adaptor hasil NilaiAkhirService (ubah di sini bila key berbeda) ---
        $kunciSiswa = function ($row, $key) {
            return data_get($row, 'siswa.id') ?? data_get($row, 'siswa_id') ?? data_get($row, 'id') ?? $key;
        };
        $ambilNilai = function ($row) {
            if ($row === null) return null;
            if (is_numeric($row)) return (float) $row;
            foreach (['nilai_akhir', 'nilai_rapor', 'akhir', 'nilai'] as $k) {
                $v = data_get($row, $k);
                if ($v !== null && is_numeric($v)) return (float) $v;
            }
            return null;
        };

        $daftarTahun = $periodeList->pluck('tahun_ajaran')->push($tahunAjaran)->filter()->unique()->values();
        $daftarSemester = $periodeList->pluck('semester')->push($semester)->filter()->unique()->values();

        $namaMapelFn = function ($pm) {
            $mp = $pm->mataPelajaran ?? null;
            return $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        };
        $namaGuruFn = fn($pm) => $pm->guru->nama ?? ($pm->guru->name ?? '-');

        // Indeks nilai: [pengampu_id][siswa_id] = nilai (float|null)
        $indeks = [];
        foreach ($daftarPengampu as $pm) {
            $indeks[$pm->id] = [];
            foreach (collect($rekapPerMapel[$pm->id] ?? []) as $k => $row) {
                $indeks[$pm->id][$kunciSiswa($row, $k)] = $ambilNilai($row);
            }
        }

        $nMapel = $daftarPengampu->count();
        $siswaUrut = $kelas->siswa->sortBy(fn($s) => strtolower($namaSiswa($s)))->values();

        // Susun baris per siswa
        $baris = $siswaUrut->map(function ($s) use ($daftarPengampu, $indeks, $nMapel) {
            $nilaiPerMapel = [];
            $terisi = [];
            foreach ($daftarPengampu as $pm) {
                $v = $indeks[$pm->id][$s->id] ?? null;
                $nilaiPerMapel[$pm->id] = $v;
                if ($v !== null) $terisi[] = $v;
            }
            return (object) [
                's' => $s,
                'nilai' => $nilaiPerMapel,
                'rata' => count($terisi) ? round(array_sum($terisi) / count($terisi), 1) : null,
                'kosong' => $nMapel - count($terisi),
                'lengkap' => $nMapel > 0 && count($terisi) === $nMapel,
            ];
        });

        $totalSiswa = $baris->count();
        $nLengkap = $baris->where('lengkap', true)->count();
        $nBelum = $totalSiswa - $nLengkap;
        $rataKelas = $baris->whereNotNull('rata')->count() ? round($baris->whereNotNull('rata')->avg('rata'), 1) : null;
        $persenLengkap = $totalSiswa > 0 ? round($nLengkap / $totalSiswa * 100, 1) : 0;
        $guruTerkait = $daftarPengampu->pluck('guru_id')->filter()->unique()->count();

        $paramPeriode = ['kelas_id' => $kelas->id, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester];
    @endphp

    {{-- HEADER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1.5 min-w-0">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span>Wali Kelas</span>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Rekapitulasi Nilai Siswa</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3 pt-0.5">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rekap Nilai — Wali Kelas</h1>
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                    {{ $namaKelas }} • Semester {{ $semester }} {{ $tahunAjaran }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 self-start lg:self-center">
            <a href="{{ route('lms.guru.wali-kelas.absensi', $paramPeriode) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
                <i class="bi bi-calendar-check text-blue-600"></i><span>Lihat Rekap Absensi</span>
            </a>
        </div>
    </section>

    {{-- FILTER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
        <form id="filterNilai" method="GET" action="{{ route('lms.guru.wali-kelas.index') }}"
            class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
        </form>
    </section>

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Siswa</p>
                <p class="text-3xl font-bold text-slate-900">{{ $totalSiswa }}</p>
                <span class="text-xs text-slate-500">{{ $nMapel }} mata pelajaran, {{ $guruTerkait }} guru pengampu</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-blue-600"><i class="bi bi-people-fill text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Rata-rata Rapor Kelas</p>
                <p class="text-3xl font-bold text-blue-700">{{ $rataKelas ?? '-' }} <span class="text-sm font-normal text-slate-500">/ 100</span></p>
                <span class="text-xs text-slate-500">KKM {{ $KKM }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700"><i class="bi bi-graph-up-arrow text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Nilai Lengkap</p>
                <p class="text-3xl font-bold text-slate-900">{{ $nLengkap }} <span class="text-sm font-normal text-slate-500">siswa</span></p>
                <div class="flex items-center gap-2">
                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="bg-blue-600 h-full rounded-full" style="width: {{ $persenLengkap }}%"></div></div>
                    <span class="text-xs font-mono text-slate-500">{{ $persenLengkap }}%</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i class="bi bi-check2-circle text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Perlu Dilengkapi</p>
                <p class="text-3xl font-bold {{ $nBelum > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $nBelum }} <span class="text-sm font-normal text-slate-500">siswa</span></p>
                @if ($nBelum > 0)
                    <span class="text-xs text-rose-600 font-semibold">Ada nilai mapel yang belum masuk</span>
                @else
                    <span class="text-xs text-emerald-600 font-semibold">Semua nilai sudah lengkap</span>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600"><i class="bi bi-exclamation-circle-fill text-2xl"></i></div>
        </div>
    </section>

    {{-- MATRIKS NILAI --}}
    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 rounded-full bg-blue-600"></div>
                <div>
                    <h2 class="font-bold text-slate-900 text-lg">Matriks Nilai Rapor Siswa</h2>
                    <p class="text-sm text-slate-500">Nilai akhir tiap mata pelajaran di {{ $namaKelas }}</p>
                </div>
            </div>
            <div class="relative w-full sm:w-72">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input id="cariSiswa" type="text" placeholder="Cari nama atau NISN siswa..."
                    class="w-full h-10 pl-10 pr-4 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all">
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse" style="min-width: {{ 260 + $nMapel * 150 + 280 }}px">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-5 sticky left-0 z-20 bg-slate-50 min-w-[240px]">Identitas Siswa</th>
                        @foreach ($daftarPengampu as $pm)
                            <th class="py-3.5 px-4 min-w-[150px] text-center">
                                <div class="text-slate-800 text-xs normal-case tracking-normal font-bold">{{ $namaMapelFn($pm) }}</div>
                                <div class="text-[11px] text-slate-400 normal-case tracking-normal font-normal truncate max-w-[140px] mx-auto">{{ $namaGuruFn($pm) }}</div>
                            </th>
                        @endforeach
                        <th class="py-3.5 px-4 min-w-[120px] text-center bg-blue-50 text-blue-700">
                            <div>Rata-rata</div>
                            <div class="text-[11px] normal-case tracking-normal font-normal">Rapor Akhir</div>
                        </th>
                        <th class="py-3.5 px-5 min-w-[150px] text-center">Status Berkas</th>
                    </tr>
                </thead>
                <tbody id="tbodyNilai" class="divide-y divide-slate-100 text-sm">
                    @forelse ($baris as $b)
                        @php $nama = $namaSiswa($b->s); @endphp
                        <tr class="baris-siswa hover:bg-slate-50 transition-colors group {{ ! $b->lengkap ? 'bg-rose-50/20' : '' }}">
                            <td class="py-3.5 px-5 sticky left-0 z-10 bg-white group-hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 font-semibold flex items-center justify-center text-xs shrink-0">{{ $inisial($nama) }}</div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate nama-siswa">{{ $nama }}</div>
                                        @if ($b->s->nisn ?? null)<div class="text-xs font-mono text-slate-400 nisn-siswa">NISN: {{ $b->s->nisn }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            @foreach ($daftarPengampu as $pm)
                                @php $v = $b->nilai[$pm->id]; @endphp
                                <td class="py-3.5 px-4 text-center">
                                    @if ($v === null)
                                        <div class="inline-flex flex-col items-center">
                                            <span class="font-mono font-semibold text-slate-300">–</span>
                                            <span class="text-[10px] text-rose-600 font-semibold">Belum ada nilai</span>
                                        </div>
                                    @else
                                        <span class="font-mono font-semibold {{ $v < $KKM ? 'text-rose-600' : 'text-slate-800' }}">{{ rtrim(rtrim(number_format($v, 1), '0'), '.') }}</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="py-3.5 px-4 text-center bg-blue-50/40">
                                @if ($b->rata === null)
                                    <span class="font-mono text-slate-300">–</span>
                                @else
                                    <span class="font-mono font-bold {{ $b->rata < $KKM ? 'text-rose-600' : 'text-blue-700' }}">{{ number_format($b->rata, 1) }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if ($b->lengkap)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Lengkap
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Belum Lengkap ({{ $b->kosong }})
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $nMapel + 3 }}" class="py-12 text-center text-slate-500">
                                <i class="bi bi-people text-3xl text-slate-300 block mb-2"></i>Belum ada siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                    @if ($nMapel === 0 && $totalSiswa > 0)
                        <tr><td colspan="3" class="py-6 text-center text-xs text-slate-500">Belum ada mata pelajaran terdaftar pada periode ini.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-slate-500">
            <div>Menampilkan <span id="jumlahTampil" class="font-semibold text-slate-900">{{ $totalSiswa }}</span> dari {{ $totalSiswa }} siswa {{ $namaKelas }}</div>
            <div class="text-xs">Nilai di bawah KKM ({{ $KKM }}) ditandai merah.</div>
        </div>
    </section>

    {{-- KETERANGAN --}}
    <section class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0"><i class="bi bi-info-circle text-xl"></i></div>
        <div class="space-y-3">
            <div>
                <h2 class="font-semibold text-slate-900">Catatan & Mekanisme Nilai</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    Nilai akhir tiap mapel dihitung otomatis dari konfigurasi bobot yang diatur masing-masing guru pengampu.
                    Rata-rata rapor adalah rata-rata dari mapel yang nilainya sudah ada.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="flex items-start gap-2.5 p-3 rounded-lg bg-white/70">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Lengkap</div>
                        <div class="text-xs text-slate-500">Semua mapel di kelas ini sudah punya nilai akhir untuk siswa tersebut.</div>
                    </div>
                </div>
                <div class="flex items-start gap-2.5 p-3 rounded-lg bg-white/70">
                    <span class="w-3 h-3 rounded-full bg-rose-500 mt-1 shrink-0"></span>
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Belum Lengkap</div>
                        <div class="text-xs text-slate-500">Angka di dalam kurung menunjukkan jumlah mapel yang nilainya belum masuk. Koordinasikan dengan guru pengampu mapel tersebut.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            // Ganti kelas → reset periode; ganti periode → kirim ulang
            const form = document.getElementById('filterNilai');
            const f = id => document.getElementById(id);
            f('fKelas')?.addEventListener('change', () => {
                ['fTahun', 'fSemester'].forEach(i => f(i) && (f(i).disabled = true));
                form.submit();
            });
            ['fTahun', 'fSemester'].forEach(i => f(i)?.addEventListener('change', () => form.submit()));

            // Cari siswa
            const rows = document.querySelectorAll('#tbodyNilai .baris-siswa');
            f('cariSiswa')?.addEventListener('input', e => {
                const q = e.target.value.toLowerCase().trim();
                let tampil = 0;
                rows.forEach(r => {
                    const nama = (r.querySelector('.nama-siswa')?.textContent || '').toLowerCase();
                    const nisn = (r.querySelector('.nisn-siswa')?.textContent || '').toLowerCase();
                    const cocok = nama.includes(q) || nisn.includes(q);
                    r.classList.toggle('hidden', !cocok);
                    if (cocok) tampil++;
                });
                f('jumlahTampil').textContent = tampil;
            });
        })();
    </script>
@endpush