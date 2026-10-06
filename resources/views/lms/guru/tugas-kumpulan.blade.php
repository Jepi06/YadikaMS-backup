{{-- resources/views/lms/guru/tugas-kumpulan.blade.php
       $tugas       → Tugas (punya pengampuMapel, judul, batas_waktu, deskripsi)
       $pengumpulan → koleksi pengumpulan tugas ini (siswa_id, dikumpulkan_at, nilai, catatan_guru, file_jawaban, link_jawaban, catatan_siswa)
       $pengampuMapel (opsional) → default: $tugas->pengampuMapel --}}
@extends('lms.layouts.app')

@section('title', 'Penilaian Tugas - LMS Yadika')
@section('breadcrumb', 'Penilaian Tugas')

@section('content')
    @php
        use Illuminate\Support\Carbon;

        $batasKKM = 75; // Standar ketuntasan minimal

        $pm = $pengampuMapel ?? ($tugas->pengampuMapel ?? null);
        $mp = $pm->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        $kelasModel = $pm->kelas ?? null;
        $namaKelas = $kelasModel->nama_kelas ?? ($kelasModel->nama ?? null);
        $semester = $pm->semester ?? null;
        $tahunAjaran = $pm->tahun_ajaran ?? null;
        $deadline = $tugas->batas_waktu ? Carbon::parse($tugas->batas_waktu) : null;

        $roster = collect($siswaList ?? ($kelasModel?->siswa ?? []))
            ->sortBy(fn($s) => strtolower($s->nama ?? ($s->name ?? '')))
            ->values();

        $map = collect($pengumpulan ?? ($tugas->pengumpulan ?? []))->keyBy('siswa_id');

        $inisial = fn($n) => collect(preg_split('/\s+/', trim($n)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        // Susun baris per siswa + status
        $baris = $roster->map(function ($s) use ($map) {
            $p = $map[$s->id] ?? null;
            $dikumpul = $p && $p->dikumpulkan_at;
            $nilai = $p->nilai ?? null;
            $status = ! $dikumpul ? 'unsubmitted' : ($nilai !== null ? 'graded' : 'pending');
            return (object) [
                's' => $s,
                'p' => $dikumpul ? $p : null,
                'nama' => $s->nama ?? ($s->name ?? '-'),
                'nisn' => $s->nisn ?? null,
                'status' => $status,
                'nilai' => $nilai,
            ];
        });

        $totalSiswa = $baris->count();
        $nTerkumpul = $baris->where('status', '!=', 'unsubmitted')->count();
        $nPending = $baris->where('status', 'pending')->count();
        $nGraded = $baris->where('status', 'graded')->count();
        $nBelum = $baris->where('status', 'unsubmitted')->count();
        $rataNilai = $nGraded > 0 ? round($baris->where('status', 'graded')->avg('nilai'), 1) : null;
        $persenKumpul = $totalSiswa > 0 ? round($nTerkumpul / $totalSiswa * 100, 1) : 0;

        $urlDaftar = $pm ? route('lms.guru.tugas.index', $pm) : route('lms.guru.kelas.index');
    @endphp

    {{-- HEADER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1.5 min-w-0">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.kelas.index') }}" class="hover:text-blue-600 transition-colors">Kelas Saya</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ $urlDaftar }}" class="hover:text-blue-600 transition-colors">{{ $namaMapel }}@if ($namaKelas) ({{ $namaKelas }})@endif</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-blue-700 font-semibold">Penilaian Tugas</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $tugas->judul }}</h1>
            <div class="flex flex-wrap items-center gap-2 pt-1">
                @if ($namaKelas || $semester)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                        <i class="bi bi-mortarboard"></i>
                        {{ $namaKelas }}@if ($namaKelas && $semester) • @endif @if ($semester)Semester {{ $semester }}@endif {{ $tahunAjaran }}
                    </span>
                @endif
                @if ($deadline)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $deadline->isPast() ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700' }}">
                        <i class="bi bi-stopwatch"></i>
                        Batas: {{ $deadline->translatedFormat('d M Y, H:i') }} WIB
                    </span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 self-start lg:self-center">
            <a href="{{ $urlDaftar }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all text-sm font-semibold shadow-sm">
                <i class="bi bi-arrow-left"></i><span>Kembali ke Daftar Tugas</span>
            </a>
        </div>
    </section>

    @if ($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-start gap-2"><i class="bi bi-exclamation-circle mt-0.5"></i><span>{{ $error }}</span></div>
            @endforeach
        </div>
    @endif

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Siswa</p>
                <p class="text-3xl font-bold text-slate-900">{{ $totalSiswa }}</p>
                <span class="inline-flex items-center gap-1 text-xs text-slate-500"><span class="w-2 h-2 rounded-full bg-blue-600"></span>Siswa terdaftar di kelas</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-blue-600"><i class="bi bi-people-fill text-2xl"></i></div>
        </div>

        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Terkumpul</p>
                <p class="text-3xl font-bold text-slate-900">{{ $nTerkumpul }} <span class="text-sm font-normal text-slate-500">/ {{ $totalSiswa }}</span></p>
                <span class="inline-flex items-center gap-1 text-xs text-sky-700 font-semibold"><i class="bi bi-check-circle"></i>{{ $persenKumpul }}% sudah mengumpulkan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center text-sky-700"><i class="bi bi-journal-check text-2xl"></i></div>
        </div>

        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Perlu Diperiksa</p>
                <p class="text-3xl font-bold text-slate-900">{{ $nPending }}</p>
                @if ($nPending > 0)
                    <span class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>Menunggu penilaian
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-semibold"><i class="bi bi-check2-all"></i>Semua sudah dinilai</span>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600"><i class="bi bi-hourglass-split text-2xl"></i></div>
        </div>

        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Rata-rata Nilai</p>
                <p class="text-3xl font-bold text-blue-700">{{ $rataNilai ?? '-' }} <span class="text-sm font-normal text-slate-500">/ 100</span></p>
                <span class="inline-flex items-center gap-1 text-xs text-slate-500 font-medium"><i class="bi bi-graph-up-arrow text-blue-600"></i>KKM {{ $batasKKM }} ({{ $nGraded }} dinilai)</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-blue-700"><i class="bi bi-award-fill text-2xl"></i></div>
        </div>
    </section>

    {{-- FILTER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-lg">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input id="cariSiswa" type="text" placeholder="Cari nama siswa atau NISN..."
                    class="w-full h-10 pl-10 pr-4 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all">
            </div>
            <div id="filterGroup" class="flex flex-wrap items-center gap-1.5">
                @php
                    $filters = [
                        'all' => 'Semua (' . $totalSiswa . ')',
                        'pending' => 'Menunggu Dinilai (' . $nPending . ')',
                        'graded' => 'Sudah Dinilai (' . $nGraded . ')',
                        'unsubmitted' => 'Belum Kumpul (' . $nBelum . ')',
                    ];
                @endphp
                @foreach ($filters as $key => $label)
                    <button type="button" data-filter="{{ $key }}"
                        class="filter-btn px-3.5 py-1.5 rounded-lg text-xs transition-all {{ $key === 'all' ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'bg-slate-100 text-slate-600 hover:text-slate-900 font-medium' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TABEL PENILAIAN --}}
    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4">Siswa & NISN</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Submisi & Berkas</th>
                        <th class="py-3.5 px-4 w-36">Nilai (0-100)</th>
                        <th class="py-3.5 px-4 min-w-[240px]">Catatan untuk Siswa</th>
                        <th class="py-3.5 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyNilai" class="divide-y divide-slate-100 text-sm">
                    @forelse ($baris as $b)
                        @php
                            $p = $b->p;
                            $formId = 'nilai-' . $b->s->id;
                            // NOTE: nama kolom disamakan dengan model PengumpulanTugas (lihat tugas.blade.php lama & route lms.file.jawaban)
                            $fileJawaban = $p?->file_jawaban;
                            $linkJawaban = $p?->link_jawaban;
                            $catatanSiswa = $p?->catatan_siswa;
                            $feedback = $p?->catatan_guru ?? '';
                            $terlambat = $p && $deadline && Carbon::parse($p->dikumpulkan_at)->gt($deadline);
                            $dibawahKKM = $b->nilai !== null && $b->nilai < $batasKKM;
                        @endphp
                        <tr class="baris-nilai hover:bg-slate-50 transition-colors {{ $b->status === 'unsubmitted' ? 'opacity-75' : '' }} {{ $b->status === 'pending' ? 'bg-sky-50/30' : '' }}"
                            data-status="{{ $b->status }}">
                            <td class="py-4 px-4 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 font-semibold flex items-center justify-center text-xs">{{ $inisial($b->nama) }}</div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 nama-siswa">{{ $b->nama }}</div>
                                        @if ($b->nisn)<div class="text-xs font-mono text-slate-400 nisn-siswa">NISN: {{ $b->nisn }}</div>@endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4 align-top">
                                @if ($b->status === 'graded')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Sudah Dinilai
                                    </span>
                                @elseif ($b->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>Menunggu Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Belum Kumpul
                                    </span>
                                @endif
                                @if ($terlambat)
                                    <div class="mt-1.5"><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-[11px] font-semibold"><i class="bi bi-clock-history"></i>Terlambat</span></div>
                                @endif
                            </td>

                            <td class="py-4 px-4 align-top">
                                @if ($p)
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap gap-2">
                                            @if ($fileJawaban)
                                                <a href="{{ route('lms.file.jawaban', $p) }}" target="_blank" rel="noopener"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-100 text-blue-700 hover:bg-slate-200 transition-colors text-xs font-mono">
                                                    <i class="bi bi-file-earmark-arrow-down"></i><span>{{ basename($fileJawaban) }}</span>
                                                </a>
                                            @endif
                                            @if ($linkJawaban)
                                                <a href="{{ $linkJawaban }}" target="_blank" rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors text-xs font-mono max-w-[260px]">
                                                    <i class="bi bi-link-45deg"></i><span class="truncate">{{ preg_replace('#^https?://#', '', $linkJawaban) }}</span>
                                                </a>
                                            @endif
                                            @if (! $fileJawaban && ! $linkJawaban)
                                                <span class="text-xs italic text-slate-400">Tidak ada berkas atau tautan.</span>
                                            @endif
                                        </div>
                                        @if ($catatanSiswa)
                                            <div class="text-slate-500 text-xs bg-slate-50 rounded-lg p-2 max-w-md">
                                                <span class="font-semibold text-slate-800">Catatan Siswa:</span> {{ $catatanSiswa }}
                                            </div>
                                        @endif
                                        <div class="text-[11px] text-slate-400">Dikumpulkan {{ Carbon::parse($p->dikumpulkan_at)->translatedFormat('d M Y, H:i') }}</div>
                                    </div>
                                @else
                                    <div class="py-2 italic text-slate-400 text-xs">Belum ada berkas atau tautan submisi.</div>
                                @endif
                            </td>

                            <td class="py-4 px-4 align-top">
                                <div class="relative flex items-center">
                                    <input form="{{ $formId }}" name="nilai" type="number" min="0" max="100" step="0.01"
                                        value="{{ $b->nilai }}" placeholder="{{ $p ? 'Nilai...' : '-' }}"
                                        {{ $p ? '' : 'disabled' }}
                                        class="w-full h-10 px-3 pr-12 rounded-lg text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500/40 focus:outline-none
                                            {{ $p ? 'bg-slate-100 text-slate-900 focus:bg-white' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}
                                            {{ $dibawahKKM ? 'text-rose-600' : '' }}">
                                    <span class="absolute right-3 text-slate-400 text-xs font-mono">/100</span>
                                </div>
                                @if ($dibawahKKM)
                                    <div class="text-[11px] text-rose-600 font-semibold mt-1">Di bawah KKM ({{ $batasKKM }})</div>
                                @endif
                            </td>

                            <td class="py-4 px-4 align-top">
                                @if ($p)
                                    <textarea form="{{ $formId }}" name="catatan_guru" rows="2"
                                        placeholder="Beri catatan atau umpan balik..."
                                        class="w-full p-2.5 rounded-lg bg-slate-100 text-slate-800 placeholder:text-slate-400 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/40 focus:outline-none resize-none">{{ $feedback }}</textarea>
                                @else
                                    <div class="py-2.5 text-xs italic text-slate-400">Menunggu siswa mengumpulkan tugas.</div>
                                @endif
                            </td>

                            <td class="py-4 px-4 align-top text-center">
                                @if ($p)
                                    <form id="{{ $formId }}" method="POST" action="{{ route('lms.guru.tugas.kumpulan.nilai', $p) }}">
                                        @csrf
                                    </form>
                                    <button type="submit" form="{{ $formId }}"
                                        title="{{ $b->status === 'graded' ? 'Perbarui Nilai' : 'Simpan Penilaian' }}"
                                        class="w-10 h-10 rounded-lg bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-all shadow-sm mx-auto">
                                        <i class="bi bi-check-lg text-xl"></i>
                                    </button>
                                @else
                                    <button type="button" disabled title="Tugas belum dikumpulkan"
                                        class="w-10 h-10 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center cursor-not-allowed mx-auto">
                                        <i class="bi bi-slash-circle text-lg"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="bi bi-people text-3xl text-slate-300 block mb-2"></i>Belum ada siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-slate-500">
            <div>Menampilkan <span id="jumlahTampil" class="font-semibold text-slate-900">{{ $totalSiswa }}</span> dari {{ $totalSiswa }} siswa{{ $namaKelas ? ' kelas ' . $namaKelas : '' }}</div>
            <div class="text-xs">Nilai tersimpan per siswa lewat tombol <i class="bi bi-check-lg text-blue-600"></i> di tiap baris.</div>
        </div>
    </section>

    {{-- KKM --}}
    <section class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0"><i class="bi bi-shield-check text-xl"></i></div>
        <div>
            <h2 class="font-semibold text-slate-900">Standar Ketuntasan Minimal (KKM)</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                KKM penilaian tugas ini adalah <span class="font-semibold text-blue-700">{{ $batasKKM }}</span>.
                Nilai di bawah angka tersebut ditandai merah. Nilai yang disimpan ikut dihitung di rekap nilai kelas.
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const rows = document.querySelectorAll('#tbodyNilai .baris-nilai');
        const btns = document.querySelectorAll('.filter-btn');
        let filterAktif = 'all', kata = '';

        const AKTIF = ['bg-blue-600', 'text-white', 'font-semibold', 'shadow-sm'];
        const NONAKTIF = ['bg-slate-100', 'text-slate-600', 'hover:text-slate-900', 'font-medium'];

        function terapkan() {
            let tampil = 0;
            rows.forEach(r => {
                const nama = (r.querySelector('.nama-siswa')?.textContent || '').toLowerCase();
                const nisn = (r.querySelector('.nisn-siswa')?.textContent || '').toLowerCase();
                const cocok = (filterAktif === 'all' || r.dataset.status === filterAktif)
                    && (nama.includes(kata) || nisn.includes(kata));
                r.classList.toggle('hidden', !cocok);
                if (cocok) tampil++;
            });
            document.getElementById('jumlahTampil').textContent = tampil;
        }

        document.getElementById('cariSiswa')?.addEventListener('input', e => {
            kata = e.target.value.toLowerCase().trim();
            terapkan();
        });

        btns.forEach(b => b.addEventListener('click', () => {
            btns.forEach(x => { x.classList.remove(...AKTIF); x.classList.add(...NONAKTIF); });
            b.classList.remove(...NONAKTIF); b.classList.add(...AKTIF);
            filterAktif = b.dataset.filter;
            terapkan();
        }));
    </script>
@endpush