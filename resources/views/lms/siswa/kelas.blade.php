{{-- resources/views/lms/siswa/kelas.blade.php --}}
@extends('lms.layouts.app')

@section('title', 'Mata Pelajaran - LMS Yadika')
@section('breadcrumb', 'Mata Pelajaran')

@section('content')
    @php
        $namaKelas = $siswa->kelas->nama_kelas ?? ($siswa->kelas->nama ?? null);
        $warna = ['blue', 'indigo', 'emerald', 'amber', 'rose'];
        // Link fitur lain: otomatis '#' kalau route-nya belum dibuat
        $link = fn($name, $pm) => Route::has($name) ? route($name, $pm) : '#';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 flex-wrap">
                <span>Mata Pelajaran</span>
                @if ($namaKelas)
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">
                        Kelas {{ $namaKelas }}
                    </span>
                @endif
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Buka materi, tugas, dan presensi dari tiap mata pelajaran.</p>
        </div>
        <a href="{{ route('lms.siswa.dashboard') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-600">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($mapelDiKelas as $i => $pm)
            @php $c = $warna[$i % count($warna)]; @endphp
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between group">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-{{ $c }}-600 bg-{{ $c }}-50 px-2 py-0.5 rounded-md">
                            {{ $namaKelas ?? '-' }}
                        </span>
                        <h3 class="font-bold text-slate-900 text-base mt-2 group-hover:text-blue-600 transition-colors">
                            {{ $pm->mataPelajaran->nama ?? ($pm->nama ?? '-') }}
                        </h3>
                        <p class="text-xs text-slate-500 flex items-center gap-2 mt-1">
                            <span class="font-medium text-slate-700 flex items-center gap-1">
                                <i class="bi bi-person-badge text-slate-400"></i>
                                {{ $pm->guru->nama ?? ($pm->guru->user->name ?? '-') }}
                            </span>
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-{{ $c }}-50 text-{{ $c }}-600 flex items-center justify-center text-lg flex-shrink-0 group-hover:bg-{{ $c }}-600 group-hover:text-white transition-colors">
                        <i class="bi bi-book-half"></i>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-1.5">
                    <a href="{{ $link('lms.siswa.materi.index', $pm) }}"
                        class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200/70 transition-all flex items-center justify-center gap-1">
                        <i class="bi bi-journal-text text-slate-400"></i> Materi
                    </a>
                    <a href="{{ $link('lms.siswa.tugas.index', $pm) }}"
                        class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200/70 transition-all flex items-center justify-center gap-1">
                        <i class="bi bi-clipboard-check text-slate-400"></i> Tugas
                    </a>
                    <a href="{{ $link('lms.siswa.presensi.index', $pm) }}"
                        class="col-span-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/30 transition-all flex items-center justify-center gap-1.5">
                        <i class="bi bi-qr-code-scan"></i> Presensi
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-400 text-sm">
                Belum ada mata pelajaran untuk kelas Anda.
            </div>
        @endforelse
    </div>
@endsection