
@extends('lms.layouts.app')

@section('title', 'Presensi - LMS Yadika')
@section('breadcrumb', 'Presensi')

@section('content')
    @php
        $tgl = \Illuminate\Support\Carbon::parse($tanggal ?? now()->toDateString());
        $isToday = $isHariIni ?? $tgl->isToday();

        $mp = $pengampuMapel->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        $kelasModel = $pengampuMapel->kelas ?? null;
        $namaKelas = $kelasModel->nama_kelas ?? ($kelasModel->nama ?? null);
        $semester = $pengampuMapel->semester ?? null;

        $roster = collect($kelasModel?->siswa ?? [])
            ->sortBy(fn($s) => strtolower($s->nama ?? ($s->name ?? '')))
            ->values();

        $presensiMap = collect($presensiSiswa ?? [])->keyBy('siswa_id');

        $normStatus = function ($s) {
            $s = ucfirst(strtolower((string) $s));
            return in_array($s, ['Hadir', 'Izin', 'Sakit', 'Alpa']) ? $s : 'Alpa';
        };
        $statusSiswa = fn($siswa) => isset($presensiMap[$siswa->id]) ? $normStatus($presensiMap[$siswa->id]->status) : 'Alpa';

        $hitung = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];
        foreach ($roster as $s) { $hitung[$statusSiswa($s)]++; }
        $totalSiswa = $roster->count();
        $persenHadir = $totalSiswa > 0 ? round($hitung['Hadir'] / $totalSiswa * 100) : 0;
        $belumHadir = $totalSiswa - $hitung['Hadir'];

        $aktif = $isToday && ($sesi ?? null) && $sesi->masih_aktif;

        $rowCls = ['Hadir' => '', 'Izin' => 'bg-blue-50/60', 'Sakit' => 'bg-amber-50/70', 'Alpa' => 'bg-rose-50/70'];
        $selCls = [
            'Hadir' => 'bg-emerald-50 text-emerald-800',
            'Izin'  => 'bg-blue-100 text-blue-900',
            'Sakit' => 'bg-amber-100 text-amber-900',
            'Alpa'  => 'bg-rose-100 text-rose-900',
        ];
        $inisial = fn($n) => collect(preg_split('/\s+/', trim($n)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        $urlTanggal = fn($d) => route('lms.guru.presensi.index', ['pengampuMapel' => $pengampuMapel, 'tanggal' => $d->toDateString()]);
    @endphp

    @if (session('status'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 flex items-start gap-2">
            <i class="bi bi-check-circle mt-0.5"></i><span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- HEADER --}}
    <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.kelas.index') }}" class="hover:text-blue-600 transition-colors">Kelas Saya</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                @if ($namaKelas)
                    <span>{{ $namaKelas }}</span>
                    <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                @endif
                <span class="text-slate-800 font-semibold">Presensi & QR Dinamis</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Presensi Siswa — {{ $namaMapel }}</h1>
                @if ($namaKelas)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold">
                        {{ $namaKelas }}@if ($semester) • Semester {{ $semester }}@endif
                    </span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('lms.guru.kelas.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-semibold shadow-sm hover:bg-slate-50 transition-all">
                <i class="bi bi-arrow-left"></i><span>Kembali ke Kelas</span>
            </a>
            <a href="{{ route('lms.guru.presensi.rekap', $pengampuMapel) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold shadow-md hover:bg-blue-700 transition-all">
                <i class="bi bi-bar-chart-line"></i><span>Rekap Presensi</span>
            </a>
        </div>
    </section>

    {{-- NAVIGATOR TANGGAL --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center bg-slate-100 rounded-lg p-1">
                <a href="{{ $urlTanggal($tgl->copy()->subDay()) }}" aria-label="Tanggal sebelumnya"
                    class="p-1.5 rounded text-slate-500 hover:text-slate-900 hover:bg-white transition-colors"><i class="bi bi-chevron-left"></i></a>
                <a href="{{ $urlTanggal($tgl->copy()->addDay()) }}" aria-label="Tanggal berikutnya"
                    class="p-1.5 rounded text-slate-500 hover:text-slate-900 hover:bg-white transition-colors"><i class="bi bi-chevron-right"></i></a>
            </div>
            <form method="GET" action="{{ route('lms.guru.presensi.index', $pengampuMapel) }}" class="flex items-center gap-3">
                <div class="relative flex items-center">
                    <i class="bi bi-calendar3 absolute left-3 text-blue-600 pointer-events-none"></i>
                    <input type="date" name="tanggal" value="{{ $tgl->toDateString() }}" onchange="this.form.submit()"
                        class="pl-9 pr-3 py-1.5 rounded-lg bg-slate-100 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer">
                </div>
            </form>
            <div class="hidden sm:flex items-center gap-2">
                <span class="font-bold text-slate-800">{{ $tgl->translatedFormat('l, d F Y') }}</span>
                @if ($isToday)
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[11px] font-bold">Hari Ini</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3 self-end md:self-auto">
            @if ($belumHadir > 0)
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500"></i>
                    <span><strong>{{ $belumHadir }} siswa</strong> belum / tidak hadir</span>
                </div>
            @endif
            @unless ($isToday)
                <a href="{{ $urlTanggal(now()) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-colors">Ke Hari Ini</a>
            @endunless
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- KIRI: QR + RINGKASAN --}}
        <div class="lg:col-span-4 flex flex-col gap-6">
            @if ($isToday)
                <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-qr-code-scan text-lg"></i></div>
                            <div>
                                <h2 class="font-bold text-slate-900 leading-snug">Presensi QR Dinamis</h2>
                                <p class="text-xs text-slate-500">{{ $aktif ? 'Siswa bisa memindai sekarang' : 'Sesi belum dibuka' }}</p>
                            </div>
                        </div>
                        @if ($aktif)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>Sesi Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>Ditutup
                            </span>
                        @endif
                    </div>

                    @if ($aktif)
                        <div class="bg-slate-100 rounded-xl p-4 flex flex-col items-center text-center">
                            <div class="bg-white p-3.5 rounded-lg shadow-sm w-full max-w-[240px] aspect-square flex items-center justify-center">
                                <div id="qrBox" class="w-full" aria-label="QR presensi"></div>
                            </div>
                            <p id="qrError" class="hidden mt-2 text-xs text-rose-600 whitespace-pre-line break-all"></p>
                            <div class="w-full mt-3 space-y-1.5">
                                <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                                    <span class="flex items-center gap-1"><i class="bi bi-arrow-repeat text-blue-600"></i>Token refresh</span>
                                    <span class="font-mono font-semibold text-blue-600" id="countdownText">--</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full transition-all duration-1000 ease-linear" id="countdownBar" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-lg p-3.5 flex gap-3 text-slate-500">
                            <i class="bi bi-display text-blue-600 text-xl shrink-0"></i>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-slate-800">Tampilkan di Layar Proyektor</p>
                                <p class="text-xs leading-relaxed">Siswa memindai lewat kamera di akun LMS masing-masing. Screenshot tidak berlaku karena kode QR berganti otomatis.</p>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl bg-slate-50 border border-dashed border-slate-300 p-8 text-center space-y-2">
                            <div class="w-12 h-12 rounded-full bg-white mx-auto flex items-center justify-center text-slate-400"><i class="bi bi-qr-code text-2xl"></i></div>
                            <p class="text-sm font-semibold text-slate-800">QR belum ditampilkan</p>
                            <p class="text-xs text-slate-500">Buka sesi presensi agar siswa bisa memindai QR dan otomatis berstatus Hadir.</p>
                        </div>
                    @endif

                    <div class="flex flex-col gap-2">
                        @if ($aktif)
                            <form method="POST" action="{{ route('lms.guru.presensi.tutup', $pengampuMapel) }}"
                                onsubmit="return confirm('Tutup sesi presensi sekarang? Siswa tidak bisa scan QR lagi.')">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-lg bg-rose-50 text-rose-700 text-sm font-semibold flex items-center justify-center gap-2 hover:bg-rose-600 hover:text-white transition-colors shadow-sm">
                                    <i class="bi bi-stop-circle"></i><span>Tutup Sesi Presensi Sekarang</span>
                                </button>
                            </form>
                            <button type="button" onclick="segarkanQr()"
                                class="w-full py-2 px-3 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold flex items-center justify-center gap-1.5 hover:bg-slate-200 transition-colors">
                                <i class="bi bi-arrow-clockwise"></i><span>Segarkan QR Manual</span>
                            </button>
                        @else
                            <form method="POST" action="{{ route('lms.guru.presensi.buka', $pengampuMapel) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-lg bg-blue-600 text-white text-sm font-semibold flex items-center justify-center gap-2 hover:bg-blue-700 transition-colors shadow-sm">
                                    <i class="bi bi-play-circle"></i><span>{{ ($sesi ?? null) ? 'Buka Kembali Sesi Presensi' : 'Buka Sesi Presensi' }}</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm flex gap-3 text-slate-500">
                    <i class="bi bi-clock-history text-blue-600 text-xl shrink-0"></i>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-slate-800">Melihat data tanggal lain</p>
                        <p class="text-xs leading-relaxed">QR hanya tersedia untuk hari ini. Anda masih bisa mengoreksi status kehadiran secara manual di sebelah kanan.</p>
                    </div>
                </div>
            @endif

            {{-- Ringkasan --}}
            <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 flex items-center gap-2"><i class="bi bi-graph-up text-slate-500"></i>Ringkasan Kehadiran</h3>
                <div class="flex items-center gap-4 py-2">
                    <div class="relative w-16 h-16 shrink-0 flex items-center justify-center">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="#e2e8f0" stroke-width="3.5"></circle>
                            <circle id="donutHadir" cx="18" cy="18" r="15.915" fill="none" stroke="#10b981" stroke-width="3.8"
                                stroke-linecap="round" stroke-dasharray="{{ $persenHadir }} 100"></circle>
                        </svg>
                        <span id="persenHadir" class="absolute font-bold text-slate-900 text-sm">{{ $persenHadir }}%</span>
                    </div>
                    <div class="space-y-0.5">
                        <div class="font-bold text-slate-900"><span id="ringHadir">{{ $hitung['Hadir'] }}</span> dari {{ $totalSiswa }} Hadir</div>
                        <div class="text-xs text-slate-500">{{ $tgl->translatedFormat('d F Y') }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-slate-50 p-2.5 rounded-lg">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Mata Pelajaran</span>
                        <span class="font-semibold text-slate-800">{{ $namaMapel }}</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-lg">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Kelas</span>
                        <span class="font-semibold text-slate-800">{{ $namaKelas ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: ROSTER --}}
        <div class="lg:col-span-8">
            <form method="POST" action="{{ route('lms.guru.presensi.manual', $pengampuMapel) }}"
                class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-4">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tgl->toDateString() }}">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-blue-600"><i class="bi bi-calendar2-check text-lg"></i></div>
                        <div>
                            <h2 class="font-bold text-slate-900">Presensi Manual & Daftar Siswa</h2>
                            <p class="text-xs text-slate-500">Siswa yang scan QR otomatis berstatus <strong>Hadir</strong>.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg self-start sm:self-auto text-xs">
                        <span class="px-2.5 py-1 rounded bg-white text-emerald-700 font-bold shadow-sm">Hadir: <span id="cHadir">{{ $hitung['Hadir'] }}</span></span>
                        <span class="px-2 py-1 rounded text-blue-700 font-semibold">Izin: <span id="cIzin">{{ $hitung['Izin'] }}</span></span>
                        <span class="px-2 py-1 rounded text-amber-700 font-semibold">Sakit: <span id="cSakit">{{ $hitung['Sakit'] }}</span></span>
                        <span class="px-2 py-1 rounded text-rose-700 font-semibold">Alpa: <span id="cAlpa">{{ $hitung['Alpa'] }}</span></span>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-2"><i class="bi bi-exclamation-circle mt-0.5"></i><span>{{ $error }}</span></div>
                        @endforeach
                    </div>
                @endif

                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="rosterSearch" type="text" placeholder="Cari nama siswa atau NISN..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-100 rounded-lg text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40 transition-all">
                </div>

                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                                <th class="py-3 px-3.5 rounded-l-lg">Siswa</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Keterangan</th>
                                <th class="py-3 px-3.5 rounded-r-lg text-center">Sumber</th>
                            </tr>
                        </thead>
                        <tbody id="rosterBody" class="divide-y divide-slate-100">
                            @forelse ($roster as $s)
                                @php
                                    $nm = $s->nama ?? ($s->name ?? '-');
                                    $row = $presensiMap[$s->id] ?? null;
                                    $st = $statusSiswa($s);
                                    $src = strtolower((string) ($row->sumber ?? ($row->metode ?? '')));
                                    $viaQr = str_contains($src, 'qr') || str_contains($src, 'scan') || str_contains($src, 'barcode');
                                @endphp
                                <tr class="roster-row hover:bg-slate-50 transition-colors {{ $rowCls[$st] }}">
                                    <td class="py-3 px-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">{{ $inisial($nm) }}</div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-slate-900 truncate">{{ $nm }}</div>
                                                @if ($s->nisn ?? null)
                                                    <div class="text-xs font-mono text-slate-400 truncate">NISN: {{ $s->nisn }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <select name="status[{{ $s->id }}]"
                                            class="status-select py-1.5 px-3 rounded-lg text-xs font-semibold border-0 focus:ring-2 focus:ring-blue-500/40 cursor-pointer {{ $selCls[$st] }}">
                                            @foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $opt)
                                                <option value="{{ $opt }}" @selected($st === $opt)>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="text" name="keterangan[{{ $s->id }}]" value="{{ $row->keterangan ?? '' }}" placeholder="Catatan opsional..."
                                            class="w-full px-2.5 py-1 text-sm rounded bg-transparent hover:bg-slate-100 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:outline-none text-slate-800 placeholder:text-slate-400">
                                    </td>
                                    <td class="py-3 px-3.5 text-center">
                                        @if (! $row)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500"><i class="bi bi-dash-circle"></i>Belum ada</span>
                                        @elseif ($viaQr)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700"><i class="bi bi-qr-code-scan"></i>QR Code</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600"><i class="bi bi-pencil"></i>Manual</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-slate-500">
                                        <i class="bi bi-people text-3xl text-slate-300 block mb-2"></i>Belum ada siswa di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-slate-500 flex items-center gap-2">
                        <i class="bi bi-info-circle text-blue-600"></i>
                        Siswa tanpa catatan presensi ditampilkan sebagai Alpa sampai Anda menyimpan.
                    </p>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold shadow-md hover:bg-blue-700 transition-all">
                        <i class="bi bi-check-circle"></i><span>Simpan Perubahan Presensi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($isToday && $aktif)
        {{-- Pakai file lokal yang sama dengan versi lama yang sudah terbukti jalan --}}
        <script src="{{ asset('js/qrcode.min.js') }}"></script>
        <style>#qrBox img, #qrBox canvas { max-width: 100%; height: auto; margin: 0 auto; }</style>
    @endif
    <script>
        const TOTAL_SISWA = {{ $totalSiswa }};
        const KELAS_ROW = { Hadir: '', Izin: 'bg-blue-50/60', Sakit: 'bg-amber-50/70', Alpa: 'bg-rose-50/70' };
        const KELAS_SEL = {
            Hadir: 'bg-emerald-50 text-emerald-800', Izin: 'bg-blue-100 text-blue-900',
            Sakit: 'bg-amber-100 text-amber-900', Alpa: 'bg-rose-100 text-rose-900'
        };

        function hitungUlang() {
            const c = { Hadir: 0, Izin: 0, Sakit: 0, Alpa: 0 };
            document.querySelectorAll('.status-select').forEach(s => c[s.value]++);
            document.getElementById('cHadir').textContent = c.Hadir;
            document.getElementById('cIzin').textContent = c.Izin;
            document.getElementById('cSakit').textContent = c.Sakit;
            document.getElementById('cAlpa').textContent = c.Alpa;
            const p = TOTAL_SISWA > 0 ? Math.round(c.Hadir / TOTAL_SISWA * 100) : 0;
            document.getElementById('persenHadir').textContent = p + '%';
            document.getElementById('ringHadir').textContent = c.Hadir;
            document.getElementById('donutHadir').setAttribute('stroke-dasharray', p + ' 100');
        }

        document.querySelectorAll('.status-select').forEach(sel => {
            sel.addEventListener('change', e => {
                const row = e.target.closest('tr');
                Object.values(KELAS_ROW).forEach(k => k && k.split(' ').forEach(x => row.classList.remove(x)));
                Object.values(KELAS_SEL).forEach(k => k.split(' ').forEach(x => e.target.classList.remove(x)));
                const v = e.target.value;
                if (KELAS_ROW[v]) KELAS_ROW[v].split(' ').forEach(x => row.classList.add(x));
                KELAS_SEL[v].split(' ').forEach(x => e.target.classList.add(x));
                hitungUlang();
            });
        });

        document.getElementById('rosterSearch')?.addEventListener('input', e => {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('#rosterBody .roster-row').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        @if ($isToday && $aktif)
        // ── QR dinamis ─────────────────────────────────────────────
        // Endpoint qr() mengembalikan JSON {aktif, url, sisa}; QR digambar di browser (qrcodejs).
        // Path relatif (absolute=false) supaya selalu satu origin dengan halaman yang dibuka.
        const QR_ENDPOINT = @json(route('lms.guru.presensi.qr', $pengampuMapel, false));
        const qrBox = document.getElementById('qrBox');
        const qrError = document.getElementById('qrError');
        const teks = document.getElementById('countdownText');
        const bar = document.getElementById('countdownBar');
        let durasi = 30, sisa = durasi, memuat = false, qr = null;

        function tampilkanError(pesan) {
            if (!qrError) return;
            qrError.textContent = pesan || '';
            qrError.classList.toggle('hidden', !pesan);
        }

        function gambarQr(url) {
            if (typeof QRCode === 'undefined') {
                tampilkanError('Library QR gagal dimuat (cek file public/js/qrcode.min.js). Coba refresh halaman.');
                return;
            }
            if (!url) { tampilkanError('URL QR kosong.'); return; }
            try {
                if (!qr) {
                    qr = new QRCode(qrBox, {
                        text: url,
                        width: 220, height: 220,
                        correctLevel: QRCode.CorrectLevel.M
                    });
                } else {
                    qr.clear();
                    qr.makeCode(url);
                }
                tampilkanError('');
            } catch (e) {
                tampilkanError('Gagal membuat QR: ' + e.message);
            }
        }

        function tampilkanSisa() {
            teks.textContent = Math.max(sisa, 0) + 's';
            bar.style.width = Math.max(0, Math.min(100, sisa / durasi * 100)) + '%';
        }

        async function segarkanQr() {
            if (memuat) return;
            memuat = true;
            try {
                const res = await fetch(QR_ENDPOINT, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const d = await res.json();
                if (!d.aktif) { location.reload(); return; }   // sesi sudah ditutup/kedaluwarsa
                gambarQr(d.url);
                sisa = Number(d.sisa) || durasi;
                durasi = Math.max(durasi, sisa);
                tampilkanSisa();
            } catch (e) {
                sisa = 5; // gagal jaringan: coba lagi sebentar lagi
            } finally {
                memuat = false;
            }
        }

        // QR awal dari server, langsung tampil tanpa menunggu fetch pertama
        gambarQr(@json($scanUrl ?? ''));

        setInterval(() => {
            sisa--;
            if (sisa <= 0) { segarkanQr(); }
            tampilkanSisa();
        }, 1000);

        segarkanQr(); // sinkronkan hitung mundur dengan server saat halaman dibuka
        @endif
    </script>
@endpush