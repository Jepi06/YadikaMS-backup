{{-- resources/views/lms/siswa/materi.blade.php
     Variabel dari Siswa\MateriController@index:
       $pengampuMapel → PengampuMapel (mataPelajaran & guru HARUS sudah di-load, lazy loading dimatikan)
       $materi        → koleksi Materi terurut (kolom: judul, deskripsi, file_path, link_url)
       $selesaiIds    → array id materi yang sudah ditandai selesai oleh siswa
       $statusAkses   → [materi_id => null (terbuka) | string alasan terkunci] --}}
@extends('lms.layouts.app')

@section('title', 'Materi - LMS Yadika')
@section('breadcrumb', 'Materi')

@section('content')
    @php
        $mp = $pengampuMapel->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        $namaGuru = $pengampuMapel->guru->name ?? ($pengampuMapel->guru->nama ?? null);
        $semester = $pengampuMapel->semester ?? null;
        $tahunAjaran = $pengampuMapel->tahun_ajaran ?? null;

        $total = $materi->count();
        $nSelesai = $materi->filter(fn($m) => in_array($m->id, $selesaiIds))->count();
        $nTerkunci = $materi
            ->filter(fn($m) => !in_array($m->id, $selesaiIds) && ($statusAkses[$m->id] ?? null))
            ->count();
        $nTersedia = $total - $nSelesai - $nTerkunci;
        $persen = $total > 0 ? round(($nSelesai / $total) * 100, 1) : 0;

        // Materi aktif = materi terbuka pertama yang belum selesai
        $aktifId = $materi->first(fn($m) => !in_array($m->id, $selesaiIds) && !($statusAkses[$m->id] ?? null))?->id;
    @endphp

    {{-- HEADER --}}
    <section class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5 min-w-0">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.siswa.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('lms.siswa.kelas.index') }}" class="hover:text-blue-600 transition-colors">Kelas Saya</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span>{{ $namaMapel }}</span>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Materi Pembelajaran</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $namaMapel }}</h1>
            <p class="text-sm text-slate-500 flex flex-wrap items-center gap-x-2">
                @if ($namaGuru)
                    <span class="inline-flex items-center gap-1 font-semibold text-slate-700"><i
                            class="bi bi-person-check"></i>{{ $namaGuru }}</span>
                @endif
                @if ($semester)
                    <span>• Semester {{ $semester }} {{ $tahunAjaran }}</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 self-start md:self-auto">
            <a href="{{ route('lms.siswa.tugas.index', $pengampuMapel) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition-colors">
                <i class="bi bi-journal-check"></i><span>Tugas Mapel Ini</span>
            </a>
            <a href="{{ route('lms.siswa.kelas.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition-colors">
                <i class="bi bi-arrow-left"></i><span>Kembali</span>
            </a>
        </div>
    </section>

    @if (session('status'))
        <div
            class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 flex items-start gap-2">
            <i class="bi bi-check-circle mt-0.5"></i><span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Materi</p>
                <p class="text-3xl font-bold text-slate-900">{{ $total }}</p>
                <span class="text-xs text-slate-500">materi pada mapel ini</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i
                    class="bi bi-layers-fill text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Tuntas Dipelajari</p>
                <p class="text-3xl font-bold text-slate-900">{{ $nSelesai }} <span
                        class="text-sm font-normal text-slate-500">/ {{ $total }}</span></p>
                <div class="flex items-center gap-2">
                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full" style="width: {{ $persen }}%"></div>
                    </div>
                    <span class="text-xs font-mono text-slate-500">{{ $persen }}%</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i
                    class="bi bi-check2-circle text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Siap Dipelajari</p>
                <p class="text-3xl font-bold text-blue-700">{{ $nTersedia }}</p>
                <span class="text-xs text-slate-500">terbuka, belum ditandai selesai</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700"><i
                    class="bi bi-play-circle-fill text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Terkunci</p>
                <p class="text-3xl font-bold text-slate-500">{{ $nTerkunci }}</p>
                <span class="text-xs text-slate-500">menunggu syarat terpenuhi</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500"><i
                    class="bi bi-lock-fill text-2xl"></i></div>
        </div>
    </section>

    {{-- DAFTAR MATERI --}}
    <section class="space-y-3">
        <h2 class="font-bold text-slate-900 text-lg">Daftar Modul Belajar</h2>

        @forelse ($materi as $i => $m)
            @php
                $judul = $m->judul ?? ($m->nama ?? 'Materi ' . ($i + 1));
                $deskripsi = $m->deskripsi ?? null;
                // NOTE: nama kolom disamakan dengan versi lama (file_path, link_url)
                $file = $m->file_path ?? null;
                $link = $m->link_url ?? null;
                $alasan = $statusAkses[$m->id] ?? null;
                $selesai = in_array($m->id, $selesaiIds);
                $aktif = $m->id === $aktifId;
                $terkunci = !$selesai && $alasan;
            @endphp

            @if ($terkunci)
                <div class="rounded-xl bg-slate-50 border border-slate-200/70 p-5 space-y-3 opacity-90">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div
                                class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                <i class="bi bi-lock-fill text-lg"></i></div>
                            <div class="min-w-0">
                                <span
                                    class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-500">MATERI
                                    {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} • TERKUNCI</span>
                                <h3 class="font-semibold text-slate-500 mt-1">{{ $judul }}</h3>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold shrink-0"><i
                                class="bi bi-lock"></i>Terkunci</span>
                    </div>
                    <div class="flex items-start gap-2.5 p-3 rounded-lg bg-amber-50 text-amber-900 text-sm">
                        <i class="bi bi-hourglass-split text-amber-600 mt-0.5"></i>
                        <span>{{ $alasan }}</span>
                    </div>
                </div>
            @else
                <div
                    class="relative rounded-xl bg-white border border-slate-200/70 shadow-sm p-5 space-y-4 overflow-hidden {{ $aktif ? 'ring-1 ring-blue-200' : '' }}">
                    @if ($aktif)
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-600"></div>
                    @endif
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div
                                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $selesai ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                <i class="bi {{ $selesai ? 'bi-check-circle-fill' : 'bi-play-circle-fill' }} text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <span
                                    class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded {{ $aktif ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    MATERI {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}@if ($aktif)
                                        • AKTIF
                                    @endif
                                </span>
                                <h3 class="font-bold text-slate-900 mt-1">{{ $judul }}</h3>
                                @if ($deskripsi)
                                    <p class="text-sm text-slate-500 mt-1 max-w-3xl">{{ $deskripsi }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($selesai)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Selesai dipelajari
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold shrink-0">
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-blue-600 {{ $aktif ? 'animate-pulse' : '' }}"></span>{{ $aktif ? 'Sedang Dipelajari' : 'Siap Dipelajari' }}
                            </span>
                        @endif
                    </div>

                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50 rounded-lg p-3.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold flex items-center gap-1"><i
                                    class="bi bi-paperclip"></i>Lampiran:</span>
                            @if ($file)
                                <a href="{{ route('lms.file.materi', $m) }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white text-blue-700 hover:bg-blue-600 hover:text-white text-xs font-semibold shadow-sm transition-all">
                                    <i class="bi bi-download"></i><span>Buka / Unduh</span>
                                </a>
                            @endif
                            @if ($link)
                                <a href="{{ $link }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white text-slate-700 hover:bg-slate-100 text-xs font-semibold shadow-sm transition-colors max-w-xs">
                                    <i class="bi bi-box-arrow-up-right"></i><span
                                        class="truncate">{{ preg_replace('#^https?://#', '', $link) }}</span>
                                </a>
                            @endif
                            @if (!$file && !$link)
                                <span class="text-xs italic text-slate-400">Tidak ada berkas atau tautan.</span>
                            @endif
                        </div>

                        @unless ($selesai)
                            <form method="POST" action="{{ route('lms.siswa.materi.selesai', $m) }}" class="shrink-0">
                                @csrf
                                <button type="submit"
                                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                                    <i class="bi bi-check2-all"></i><span>Tandai Selesai Belajar</span>
                                </button>
                            </form>
                        @endunless
                    </div>
                </div>
            @endif
            @empty
                <div class="p-10 rounded-xl bg-white border border-dashed border-slate-300 text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-slate-400"><i
                            class="bi bi-folder2-open text-xl"></i></div>
                    <p class="font-semibold text-slate-800">Belum ada materi</p>
                    <p class="text-sm text-slate-500">Guru belum mengunggah materi untuk mata pelajaran ini.</p>
                </div>
            @endforelse
        </section>
    @endsection
