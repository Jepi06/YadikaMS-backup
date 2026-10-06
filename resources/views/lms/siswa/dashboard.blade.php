{{-- resources/views/lms/siswa/dashboard.blade.php --}}
@extends('lms.layouts.app')

@section('title', 'Dashboard Siswa - LMS Yadika')
@section('breadcrumb', 'Dashboard Siswa')

@section('content')
    @php
        $namaSiswa = $siswa->nama ?? ($siswa->name ?? (auth('lms')->user()->name ?? 'Siswa'));
        $namaKelas = $siswa->kelas->nama_kelas ?? ($siswa->kelas->nama ?? null);
        $nisn = $siswa->nisn ?? null;
        $totalMapel = $mapelDiKelas->count();
        $ta = $mapelDiKelas->first()->tahun_ajaran ?? null;
        $smt = $mapelDiKelas->first()->semester ?? null;
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-blue-700 p-6 lg:p-8 text-white shadow-xl">
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/4 -bottom-20 w-64 h-64 rounded-full bg-indigo-400/20 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($namaKelas)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>{{ $namaKelas }}
                        </span>
                    @endif
                    @if ($nisn)
                        <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-mono">NISN: {{ $nisn }}</span>
                    @endif
                    @if ($ta)
                        <span class="px-3 py-1 rounded-full bg-blue-500/30 text-blue-100 text-xs font-semibold">
                            Semester {{ $smt }} {{ $ta }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Halo, {{ $namaSiswa }} 👋</h1>
                <p class="text-sm text-slate-200/90 leading-relaxed">
                    Selamat datang di Portal Siswa SMK Yadika Soreang. Pastikan selalu memindai QR presensi sebelum sesi pembelajaran dimulai dan periksa tugas tepat waktu.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <a href="{{ route('lms.siswa.presensi.kamera') }}"
                    class="group inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-lg shadow-blue-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="bi bi-qr-code-scan text-lg"></i><span>Presensi Sekarang</span>
                </a>
                <a href="{{ route('lms.siswa.presensi.riwayat') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md text-white text-sm font-semibold transition-colors">
                    <i class="bi bi-clock-history"></i><span>Riwayat Presensi</span>
                </a>
            </div>
        </div>
    </section>

    {{-- STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 hover:shadow-md transition-shadow flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Mata Pelajaran</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900">{{ $totalMapel }}</span>
                    <span class="text-xs text-slate-500">Mapel Aktif</span>
                </div>
                @if ($ta)
                    <span class="inline-block mt-2 text-[11px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Semester {{ $smt }}</span>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="bi bi-journal-text text-2xl"></i>
            </div>
        </div>

        @php $adaTugas = $tugasBelumDikumpulkan > 0; @endphp
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 hover:shadow-md transition-shadow flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Tugas Tertunda</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold {{ $adaTugas ? 'text-rose-600' : 'text-emerald-600' }}">{{ $tugasBelumDikumpulkan }}</span>
                    <span class="text-xs {{ $adaTugas ? 'text-rose-500' : 'text-emerald-600' }}">{{ $adaTugas ? 'Perlu Respons' : 'Semua Beres' }}</span>
                </div>
                <span class="inline-flex items-center gap-1 mt-2 text-[11px] font-medium px-2 py-0.5 rounded {{ $adaTugas ? 'text-rose-600 bg-rose-50' : 'text-emerald-700 bg-emerald-50' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $adaTugas ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                    {{ $adaTugas ? 'Segera dikumpulkan' : 'Tidak ada tugas aktif' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $adaTugas ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                <i class="bi {{ $adaTugas ? 'bi-clipboard-x' : 'bi-clipboard-check' }} text-2xl"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 hover:shadow-md transition-shadow flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Kelas Saya</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900">{{ $namaKelas ?? '-' }}</span>
                </div>
                @if ($ta)
                    <span class="inline-block mt-2 text-[11px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded">T.A {{ $ta }}</span>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <i class="bi bi-door-open text-2xl"></i>
            </div>
        </div>
    </section>

    {{-- INSTRUKSI --}}
    <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100">
        <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
            <i class="bi bi-info-lg text-lg"></i>
        </div>
        <p class="text-sm text-slate-600">
            <span class="font-semibold text-slate-900">Instruksi Presensi:</span>
            Klik <span class="font-semibold text-blue-600">Presensi Sekarang</span> untuk membuka kamera dan memindai kode QR dari layar guru di kelas.
            Materi dan tugas dibuka lewat tombol di tiap mata pelajaran di bawah.
        </p>
    </div>

    {{-- DAFTAR MAPEL (maks. 5, selengkapnya di halaman kelas) --}}
    @include('lms.siswa._tabel-mapel', [
        'mapel'      => $mapelDiKelas->take(5),
        'total'      => $totalMapel,
        'linkSemua'  => true,
    ])
@endsection