{{-- resources/views/lms/siswa/tugas-show.blade.php
     Variabel & logika TIDAK diubah dari versi lama:
       $tugas            → Tugas (judul, deskripsi, batas_waktu, file_lampiran, is_kelompok,
                            pengampuMapel.mataPelajaran, pengampuMapel.kelas.siswa,
                            method alasanTidakBisaKumpul())
       $pengumpulanSaya  → pengumpulan milik siswa login (nullable)
       $kelompokSaya     → kelompok milik siswa login untuk tugas ini (nullable, hanya relevan jika is_kelompok)
       $siswa            → siswa login --}}
@extends('lms.layouts.app')

@section('title', $tugas->judul)
@section('breadcrumb', 'Detail Tugas')

@section('content')
    @php
        use Illuminate\Support\Carbon;

        $mp = $tugas->pengampuMapel->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? '-';
        $deadline = Carbon::parse($tugas->batas_waktu);
        $alasanTutup = $tugas->alasanTidakBisaKumpul();
    @endphp

    {{-- BREADCRUMB & HEADER --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-5">
        <div class="space-y-1.5 min-w-0">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.siswa.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('lms.siswa.tugas.index', $tugas->pengampuMapel) }}" class="hover:text-blue-600 transition-colors">{{ $namaMapel }}</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Detail Tugas</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $tugas->judul }}</h1>
            <p class="text-sm text-slate-500 flex items-center gap-1.5">
                <i class="bi bi-journal-bookmark text-blue-600"></i>{{ $namaMapel }}
                @if ($tugas->is_kelompok)
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1 text-indigo-700 font-medium"><i class="bi bi-people-fill"></i>Tugas Kelompok</span>
                @endif
            </p>
        </div>
        <a href="{{ route('lms.siswa.tugas.index', $tugas->pengampuMapel) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition-all self-start md:self-auto">
            <i class="bi bi-arrow-left"></i><span>Kembali ke Daftar Tugas</span>
        </a>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 flex items-start gap-2 mb-4">
            <i class="bi bi-check-circle mt-0.5"></i><span>{{ session('status') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 flex items-start gap-2 mb-4">
            <i class="bi bi-exclamation-circle mt-0.5"></i><span>{{ $errors->first() }}</span>
        </div>
    @endif

    @if ($alasanTutup)
        <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 flex items-start gap-2 mb-5">
            <i class="bi bi-lock-fill mt-0.5"></i><span>{{ $alasanTutup }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- KOLOM KIRI: INFO TUGAS & EVALUASI --}}
        <div class="lg:col-span-7 flex flex-col gap-5">
            {{-- DEADLINE --}}
            <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i class="bi bi-alarm text-xl"></i></div>
                        <div class="flex flex-col">
                            <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Batas Waktu Pengumpulan</span>
                            <span class="font-semibold text-slate-900">{{ $deadline->translatedFormat('l, d F Y, H:i') }} WIB</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold self-start sm:self-center {{ $deadline->isPast() ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700' }}">
                        <i class="bi bi-clock-history"></i><span>{{ $deadline->diffForHumans(['parts' => 1]) }}</span>
                    </span>
                </div>
            </div>

            {{-- INSTRUKSI TUGAS --}}
            <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2 font-semibold text-slate-900">
                    <i class="bi bi-clipboard-fill text-blue-600 text-lg"></i><span>Instruksi Tugas</span>
                </div>
                @if ($tugas->deskripsi)
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $tugas->deskripsi }}</p>
                @else
                    <p class="text-sm text-slate-400 italic">Tidak ada instruksi tambahan dari guru.</p>
                @endif

                @if ($tugas->file_lampiran)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                            <span class="text-sm font-semibold text-slate-800 truncate">Lampiran Tugas</span>
                        </div>
                        <a href="{{ route('lms.file.tugas', $tugas) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-white text-blue-700 hover:bg-blue-50 text-xs font-semibold shadow-sm transition-all shrink-0">
                            <i class="bi bi-download"></i><span>Lihat Lampiran</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- HASIL PENILAIAN --}}
            @if ($pengumpulanSaya?->sudah_dinilai)
                <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm border-l-4 border-l-emerald-500">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="bi bi-patch-check-fill text-lg"></i></div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-slate-900">Hasil Penilaian</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Sudah Dinilai
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-1 px-4 py-2 rounded-xl bg-blue-600 text-white shrink-0">
                            <span class="text-2xl font-bold leading-none">{{ $pengumpulanSaya->nilai }}</span>
                            <span class="text-xs opacity-80">/100</span>
                        </div>
                    </div>
                    @if ($pengumpulanSaya->catatan_guru)
                        <div class="mt-3 p-3 rounded-lg bg-slate-50">
                            <span class="block text-xs font-semibold text-slate-600 mb-1"><i class="bi bi-chat-left-text"></i> Catatan Guru:</span>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $pengumpulanSaya->catatan_guru }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- BERKAS PENGUMPULAN TERAKHIR --}}
            @if ($pengumpulanSaya?->file_jawaban)
                <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-2">
                    <div class="flex items-center gap-2 font-semibold text-slate-900">
                        <i class="bi bi-clock-history text-blue-600 text-lg"></i><span>Berkas Pengumpulan Terakhir</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <a href="{{ route('lms.file.jawaban', $pengumpulanSaya) }}" target="_blank"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline min-w-0">
                            <i class="bi bi-file-earmark-arrow-down shrink-0"></i><span class="truncate">Lihat file yang sudah dikumpulkan</span>
                        </a>
                        <span class="text-xs text-slate-400 shrink-0 ml-2">{{ $pengumpulanSaya->dikumpulkan_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- KOLOM KANAN: KELOMPOK & FORM PENGUMPULAN --}}
        <div class="lg:col-span-5 flex flex-col gap-5">
            @if ($alasanTutup)
                {{-- Form disembunyikan total, pesan sudah ditampilkan di atas --}}

            @elseif ($tugas->is_kelompok && !$kelompokSaya)
                {{-- Belum punya kelompok: form bikin kelompok --}}
                <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 font-semibold text-slate-900">
                        <i class="bi bi-people-fill text-indigo-600 text-lg"></i><span>Bikin Kelompok</span>
                    </div>
                    <p class="text-sm text-slate-500">Ini tugas kelompok. Pilih anggota, kamu otomatis jadi ketua.</p>

                    <form method="POST" action="{{ route('lms.siswa.tugas.kelompok.buat', $tugas) }}" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-600">Nama Kelompok (opsional)</label>
                            <input type="text" name="nama_kelompok"
                                class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-600">Pilih Anggota</label>
                            <div class="rounded-lg border border-slate-200 divide-y divide-slate-100 max-h-56 overflow-y-auto">
                                @foreach ($tugas->pengampuMapel->kelas->siswa as $temanSekelas)
                                    @if ($temanSekelas->id !== $siswa->id)
                                        <label for="a{{ $temanSekelas->id }}" class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-slate-50 cursor-pointer text-sm">
                                            <input type="checkbox" name="anggota[]" value="{{ $temanSekelas->id }}"
                                                id="a{{ $temanSekelas->id }}"
                                                class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/40">
                                            <span class="text-slate-700">{{ $temanSekelas->nama }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 h-11 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                            <i class="bi bi-people"></i><span>Bikin Kelompok</span>
                        </button>
                    </form>
                </div>

            @elseif ($tugas->is_kelompok && $kelompokSaya)
                {{-- Sudah punya kelompok --}}
                <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 font-semibold text-slate-900">
                            <i class="bi bi-people-fill text-indigo-600 text-lg"></i>
                            <span>Kelompok {{ $kelompokSaya->nama_kelompok ?? '' }}</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 space-y-1.5">
                        <p class="text-sm text-slate-700"><span class="font-semibold">Ketua:</span> {{ $kelompokSaya->ketua->nama }}</p>
                        <p class="text-sm text-slate-700"><span class="font-semibold">Anggota:</span> {{ $kelompokSaya->anggota->pluck('nama')->implode(', ') }}</p>
                    </div>
                </div>

                @if ($kelompokSaya->ketua_siswa_id === $siswa->id)
                    {{-- Ketua yang kumpulkan --}}
                    <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 font-semibold text-slate-900">
                            <i class="bi bi-cloud-upload-fill text-blue-600 text-lg"></i>
                            <span>{{ $pengumpulanSaya?->dikumpulkan_at ? 'Kumpulkan Ulang (sebagai Ketua)' : 'Kumpulkan (sebagai Ketua)' }}</span>
                        </div>
                        <form method="POST" action="{{ route('lms.siswa.tugas.kumpul', $tugas) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-600">File Jawaban (opsional)</label>
                                <input type="file" name="file"
                                    class="w-full text-sm rounded-lg bg-slate-100 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-white file:text-blue-700 file:text-xs file:font-semibold file:shadow-sm">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-600">Atau Link Jawaban (opsional)</label>
                                <input type="url" name="link_jawaban" placeholder="https://..."
                                    value="{{ $pengumpulanSaya->link_jawaban ?? '' }}"
                                    class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                                <p class="text-xs text-slate-400">Isi minimal salah satu: file atau link.</p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-600">Catatan (opsional)</label>
                                <textarea name="catatan_siswa" rows="2"
                                    class="w-full p-3 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition resize-none">{{ $pengumpulanSaya->catatan_siswa ?? '' }}</textarea>
                            </div>
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 h-11 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                                <i class="bi bi-upload"></i><span>Kumpulkan buat Kelompok</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="rounded-lg bg-blue-50 border border-blue-100 text-blue-800 text-sm px-4 py-3 flex items-start gap-2">
                        <i class="bi bi-info-circle mt-0.5"></i>
                        <span>Menunggu ketua kelompok ({{ $kelompokSaya->ketua->nama }}) mengumpulkan tugas.</span>
                    </div>
                @endif

            @else
                {{-- Tugas individu biasa --}}
                <div class="bg-white border border-slate-200/70 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 font-semibold text-slate-900">
                        <i class="bi bi-cloud-upload-fill text-blue-600 text-lg"></i>
                        <span>{{ $pengumpulanSaya?->dikumpulkan_at ? 'Kumpulkan Ulang Jawaban' : 'Kumpulkan Jawaban' }}</span>
                    </div>
                    <form method="POST" action="{{ route('lms.siswa.tugas.kumpul', $tugas) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-600">File Jawaban (opsional)</label>
                            <input type="file" name="file"
                                class="w-full text-sm rounded-lg bg-slate-100 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-white file:text-blue-700 file:text-xs file:font-semibold file:shadow-sm">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-600">Atau Link Jawaban (opsional)</label>
                            <input type="url" name="link_jawaban" placeholder="https://..."
                                value="{{ $pengumpulanSaya->link_jawaban ?? '' }}"
                                class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                            <p class="text-xs text-slate-400">Isi minimal salah satu: file atau link.</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-600">Catatan (opsional)</label>
                            <textarea name="catatan_siswa" rows="2"
                                class="w-full p-3 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition resize-none">{{ $pengumpulanSaya->catatan_siswa ?? '' }}</textarea>
                        </div>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 h-11 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                            <i class="bi bi-upload"></i><span>Kumpulkan</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection