{{-- resources/views/lms/siswa/tugas.blade.php
     Variabel dari Siswa\TugasController@index (TIDAK diubah dari versi lama):
       $pengampuMapel → PengampuMapel (mataPelajaran & guru & kelas sebaiknya sudah di-load)
       $tugas         → koleksi Tugas milik pengampuMapel ini, relasi pengumpulan sudah dimuat
                         setiap $t punya: judul, batas_waktu, sudah_lewat_batas_waktu, is_kelompok,
                         pengumpulan (relasi, ambil milik siswa login via ->pengumpulan->first())
                         setiap $p (pengumpulan) punya: dikumpulkan_at, nilai --}}
@extends('lms.layouts.app')

@section('title', 'Tugas - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))
@section('breadcrumb', 'Daftar Tugas')

@section('content')
    @php
        use Illuminate\Support\Carbon;

        $mp = $pengampuMapel->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? '-'));
        $namaGuru = $pengampuMapel->guru->name ?? ($pengampuMapel->guru->nama ?? null);
        $namaKelas = $pengampuMapel->kelas->nama_kelas ?? ($pengampuMapel->kelas->nama ?? null);
        $semester = $pengampuMapel->semester ?? null;
        $tahunAjaran = $pengampuMapel->tahun_ajaran ?? null;

        // Susun baris + status, tanpa mengubah logika/variabel dari versi lama.
        $baris = $tugas->map(function ($t) {
            $p = $t->pengumpulan->first();
            $status = $p?->dikumpulkan_at
                ? 'submitted'
                : ($t->sudah_lewat_batas_waktu ? 'overdue' : 'pending');
            return (object) ['t' => $t, 'p' => $p, 'status' => $status];
        });

        $total = $baris->count();
        $nSubmitted = $baris->where('status', 'submitted')->count();
        $nOverdue = $baris->where('status', 'overdue')->count();
        $nPending = $baris->where('status', 'pending')->count();
    @endphp

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-3">
        <a href="{{ route('lms.siswa.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard Siswa</a>
        <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
        <span>{{ $namaMapel }}</span>
        <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
        <span class="text-slate-800 font-semibold">Daftar Tugas</span>
    </nav>

    {{-- BANNER MAPEL --}}
    <section class="relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-700 to-blue-500 p-6 text-white shadow-sm mb-5">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                @if ($namaKelas || $semester)
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($namaKelas)<span class="px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold">{{ $namaKelas }}</span>@endif
                        @if ($semester)<span class="px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold">Semester {{ $semester }} {{ $tahunAjaran }}</span>@endif
                    </div>
                @endif
                <h1 class="text-2xl font-bold tracking-tight text-white mt-1">{{ $namaMapel }}</h1>
                @if ($namaGuru)
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center"><i class="bi bi-person-fill text-sm"></i></div>
                        <span class="text-sm text-white/90">{{ $namaGuru }}</span>
                    </div>
                @endif
            </div>
            <a href="{{ route('lms.siswa.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/15 hover:bg-white/25 text-white text-sm font-semibold shadow-sm transition-all self-start md:self-auto">
                <i class="bi bi-arrow-left"></i><span>Kembali ke Dashboard</span>
            </a>
        </div>
    </section>

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Tugas</p>
                <p class="text-3xl font-bold text-slate-900">{{ $total }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-clipboard-check text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Sudah Dikumpulkan</p>
                <p class="text-3xl font-bold text-slate-900">{{ $nSubmitted }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i class="bi bi-check-circle text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Belum Dikumpulkan</p>
                <p class="text-3xl font-bold text-amber-600">{{ $nPending }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600"><i class="bi bi-hourglass-split text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Lewat Batas Waktu</p>
                <p class="text-3xl font-bold text-rose-600">{{ $nOverdue }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600"><i class="bi bi-exclamation-circle text-2xl"></i></div>
        </div>
    </section>

    {{-- DAFTAR TUGAS --}}
    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        {{-- FILTER & SEARCH --}}
        <div class="p-4 bg-slate-50/60 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1 p-1 bg-slate-100 rounded-lg" id="filterTabGroup">
                <button type="button" data-filter="all" class="filter-btn px-3.5 py-1.5 rounded-md text-xs font-semibold bg-white text-blue-700 shadow-sm transition-all">
                    Semua <span class="ml-1 px-1.5 py-0.5 text-[10px] rounded-full bg-blue-50 text-blue-700">{{ $total }}</span>
                </button>
                <button type="button" data-filter="pending" class="filter-btn px-3.5 py-1.5 rounded-md text-xs font-semibold text-slate-500 hover:text-slate-800 transition-all">
                    Belum Dikumpulkan <span class="ml-1 px-1.5 py-0.5 text-[10px] rounded-full bg-slate-200 text-slate-600">{{ $nPending }}</span>
                </button>
                <button type="button" data-filter="submitted" class="filter-btn px-3.5 py-1.5 rounded-md text-xs font-semibold text-slate-500 hover:text-slate-800 transition-all">
                    Sudah Dikumpulkan <span class="ml-1 px-1.5 py-0.5 text-[10px] rounded-full bg-slate-200 text-slate-600">{{ $nSubmitted }}</span>
                </button>
                <button type="button" data-filter="overdue" class="filter-btn px-3.5 py-1.5 rounded-md text-xs font-semibold text-slate-500 hover:text-slate-800 transition-all">
                    Lewat Batas Waktu <span class="ml-1 px-1.5 py-0.5 text-[10px] rounded-full bg-rose-100 text-rose-700">{{ $nOverdue }}</span>
                </button>
            </div>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input id="taskSearch" type="text" placeholder="Cari nama tugas..."
                    class="h-9 pl-8 pr-3 w-full md:w-60 rounded-md bg-white border border-slate-200 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>
        </div>

        {{-- LIST --}}
        <div class="divide-y divide-slate-100" id="tasksList">
            @forelse ($baris as $b)
                @php
                    $t = $b->t;
                    $p = $b->p;
                    $deadline = $t->batas_waktu ? Carbon::parse($t->batas_waktu) : null;
                @endphp
                <div class="task-card p-4 hover:bg-slate-50/60 transition-colors" data-status="{{ $b->status }}">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-11 h-11 rounded-lg flex items-center justify-center shrink-0 mt-0.5
                                {{ $b->status === 'submitted' ? 'bg-emerald-50 text-emerald-600' : ($b->status === 'overdue' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600') }}">
                                <i class="bi {{ $b->status === 'submitted' ? 'bi-check-circle-fill' : ($b->status === 'overdue' ? 'bi-exclamation-circle-fill' : 'bi-hourglass-split') }} text-lg"></i>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="inline-block w-fit px-2 py-0.5 rounded text-[11px] font-semibold mb-1 {{ $t->is_kelompok ?? false ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ ($t->is_kelompok ?? false) ? 'Tugas Kelompok' : 'Tugas Individu' }}
                                </span>
                                <h3 class="task-judul font-semibold text-slate-900 leading-tight truncate">{{ $t->judul }}</h3>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-5 lg:gap-8 shrink-0">
                            @if ($deadline)
                                <div class="flex flex-col">
                                    <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Batas Pengumpulan</span>
                                    <div class="text-sm text-slate-800 mt-0.5 flex items-center gap-1.5">
                                        <i class="bi bi-alarm {{ $b->status === 'overdue' ? 'text-rose-600' : 'text-amber-600' }}"></i>
                                        <span>{{ $deadline->translatedFormat('d M Y, H:i') }} WIB</span>
                                    </div>
                                    @if ($p?->dikumpulkan_at)
                                        <span class="text-[11px] text-emerald-600 font-medium">Dikirim: {{ Carbon::parse($p->dikumpulkan_at)->translatedFormat('d M Y, H:i') }}</span>
                                    @elseif ($b->status === 'overdue')
                                        <span class="text-[11px] text-rose-600 font-medium">{{ $deadline->diffForHumans(['parts' => 1]) }}</span>
                                    @else
                                        <span class="text-[11px] text-amber-600 font-medium">{{ $deadline->diffForHumans(['parts' => 1]) }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="flex flex-col items-start min-w-[140px]">
                                <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold mb-1">Status</span>
                                @if ($b->status === 'submitted')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Sudah Dikumpulkan
                                    </span>
                                @elseif ($b->status === 'overdue')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Lewat Batas Waktu
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Belum Dikumpulkan
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-col items-center justify-center min-w-[70px]">
                                <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Nilai</span>
                                <span class="font-mono font-bold text-lg mt-0.5 {{ $p?->nilai !== null ? 'text-slate-900' : 'text-slate-300' }}">{{ $p?->nilai ?? '-' }}</span>
                            </div>

                            <div>
                                <a href="{{ route('lms.siswa.tugas.show', $t) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm
                                        {{ $b->status === 'pending' ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                                    <span>{{ $b->status === 'pending' ? 'Kumpulkan Tugas' : 'Lihat Detail' }}</span>
                                    <i class="bi {{ $b->status === 'pending' ? 'bi-upload' : 'bi-eye' }}"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-500">
                    <i class="bi bi-clipboard-x text-3xl text-slate-300 block mb-2"></i>Belum ada tugas untuk mata pelajaran ini.
                </div>
            @endforelse
        </div>

        <div id="kosongFilter" class="hidden p-8 text-center text-slate-500">
            <p class="font-semibold text-slate-800">Tidak ada tugas yang cocok</p>
            <p class="text-sm">Ubah kata kunci atau filter status.</p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const taskCards = document.querySelectorAll('.task-card');
            const searchInput = document.getElementById('taskSearch');
            const kosong = document.getElementById('kosongFilter');
            let currentFilter = 'all';

            function terapkan() {
                const query = (searchInput.value || '').toLowerCase().trim();
                let tampil = 0;
                taskCards.forEach(card => {
                    const status = card.dataset.status;
                    const judul = (card.querySelector('.task-judul')?.textContent || '').toLowerCase();
                    const cocok = (currentFilter === 'all' || status === currentFilter) && judul.includes(query);
                    card.style.display = cocok ? '' : 'none';
                    if (cocok) tampil++;
                });
                kosong?.classList.toggle('hidden', !(taskCards.length > 0 && tampil === 0));
            }

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-white', 'text-blue-700', 'shadow-sm');
                        b.classList.add('text-slate-500');
                    });
                    btn.classList.add('bg-white', 'text-blue-700', 'shadow-sm');
                    btn.classList.remove('text-slate-500');
                    currentFilter = btn.dataset.filter;
                    terapkan();
                });
            });

            searchInput?.addEventListener('input', terapkan);
        })();
    </script>
@endpush