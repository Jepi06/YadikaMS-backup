
@extends('lms.layouts.app')

@section('title', 'Modul Ajar - LMS Yadika')
@section('breadcrumb', 'Modul Ajar')

@section('content')
    @php
        // Terima beberapa kemungkinan nama variabel dari controller.
        $daftar = collect($modulAjar ?? ($modulAjarList ?? ($modul ?? ($daftarModul ?? ($pengampuMapel->modulAjar ?? [])))))
            ->sortByDesc('created_at')
            ->values();

        $mp = $pengampuMapel->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        $kelas = $pengampuMapel->kelas ?? null;
        $namaKelas = $kelas->nama_kelas ?? ($kelas->nama ?? null);
        $semester = $pengampuMapel->semester ?? null;
        $tahunAjaran = $pengampuMapel->tahun_ajaran ?? null;

        $ekstensi = fn($m) => strtolower(pathinfo((string) $m->file_path, PATHINFO_EXTENSION));
        $total = $daftar->count();
        $totalPdf = $daftar->filter(fn($m) => $ekstensi($m) === 'pdf')->count();
        $totalDoc = $daftar->filter(fn($m) => in_array($ekstensi($m), ['doc', 'docx']))->count();
        $terakhir = $daftar->first()?->created_at;
    @endphp

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.kelas.index') }}" class="hover:text-blue-600 transition-colors inline-flex items-center gap-1.5">
                    <i class="bi bi-door-open"></i><span>Kelas Saya</span>
                </a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                @if ($namaKelas)
                    <span>{{ $namaKelas }}</span>
                    <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                @endif
                <span class="text-slate-800 font-semibold">Modul Ajar</span>
            </nav>

            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $namaMapel }}</h1>
                @if ($namaKelas || $semester)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        {{ $namaKelas }}@if ($namaKelas && $semester) • @endif @if ($semester)Semester {{ $semester }}@endif @if ($tahunAjaran)· {{ $tahunAjaran }}@endif
                    </span>
                @endif
            </div>
            <p class="text-sm text-slate-500">Arsip RPP / modul ajar resmi untuk mata pelajaran ini.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('lms.guru.kelas.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm hover:bg-slate-50 transition-all text-sm font-semibold">
                <i class="bi bi-arrow-left"></i><span>Kembali ke Kelas</span>
            </a>
            <a href="{{ route('lms.guru.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition-all text-sm font-semibold">
                <i class="bi bi-grid-fill"></i><span>Dashboard</span>
            </a>
        </div>
    </div>

    {{-- BANNER PRIVASI --}}
    <div class="relative overflow-hidden rounded-xl bg-slate-900 text-slate-200 shadow-md p-5 sm:p-6">
        <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                <i class="bi bi-shield-lock-fill text-2xl text-sky-300"></i>
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="font-bold text-white tracking-tight">Arsip Guru (Tidak Tampil ke Siswa)</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-sky-500/20 text-sky-200">Privat • Non-Siswa</span>
                </div>
                <p class="text-sm text-slate-300 leading-relaxed max-w-3xl">
                    Dokumen di sini hanya bisa dilihat oleh Anda dan Panel Super Admin (admin/kepala sekolah).
                    Untuk bahan belajar yang harus dibaca siswa, unggah lewat menu <span class="font-semibold text-white">Materi</span>, bukan di sini.
                </p>
            </div>
        </div>
    </div>

    {{-- STATISTIK --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Modul</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="bi bi-file-earmark-text-fill text-lg"></i></div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $total }}</span>
                <span class="text-xs text-slate-500">dokumen</span>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Berkas PDF</span>
                <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><i class="bi bi-filetype-pdf text-lg"></i></div>
            </div>
            <div class="mt-3 text-3xl font-bold text-slate-900">{{ $totalPdf }}</div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Berkas Word</span>
                <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center"><i class="bi bi-filetype-docx text-lg"></i></div>
            </div>
            <div class="mt-3 text-3xl font-bold text-slate-900">{{ $totalDoc }}</div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Terakhir Unggah</span>
                <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center"><i class="bi bi-clock-history text-lg"></i></div>
            </div>
            <div class="mt-3 text-base font-bold text-slate-900">
                {{ $terakhir ? $terakhir->translatedFormat('d M Y') : '-' }}
            </div>
        </div>
    </section>

    {{-- FORM + DAFTAR --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- FORM UNGGAH --}}
        <div class="lg:col-span-5 w-full">
            <div class="rounded-xl bg-white border border-slate-200/70 shadow-sm p-6 lg:sticky lg:top-24">
                <div class="flex items-center gap-3 pb-5">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <i class="bi bi-cloud-arrow-up-fill text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900">Unggah Modul Ajar Baru</h2>
                        <p class="text-xs text-slate-500">Tambahkan dokumen ke arsip mapel ini</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-2"><i class="bi bi-exclamation-circle mt-0.5"></i><span>{{ $error }}</span></div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('lms.guru.modul-ajar.store', $pengampuMapel) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="judul" class="block text-sm font-semibold text-slate-800">
                            Judul Modul Ajar <span class="text-rose-500">*</span>
                        </label>
                        <input id="judul" name="judul" type="text" required value="{{ old('judul') }}"
                            placeholder="Contoh: RPP Bab 3 - RESTful API & Routing"
                            class="w-full px-3.5 py-2.5 rounded-lg bg-slate-100 text-slate-800 placeholder:text-slate-400 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-800">
                            Berkas (PDF / DOC / DOCX) <span class="text-rose-500">*</span>
                        </label>
                        <label for="fileModul" id="dropzone"
                            class="group cursor-pointer rounded-lg bg-slate-100 hover:bg-slate-200/70 p-6 flex flex-col items-center justify-center text-center transition">
                            <div class="w-12 h-12 rounded-full bg-white text-blue-600 flex items-center justify-center mb-3 shadow-sm group-hover:scale-110 transition-transform">
                                <i class="bi bi-file-earmark-arrow-up text-2xl"></i>
                            </div>
                            <p id="fileLabel" class="text-sm font-semibold text-slate-800 mb-1">Pilih berkas dari perangkat</p>
                            <p class="text-xs text-slate-500">Format: PDF, DOC, DOCX</p>
                        </label>
                        <input id="fileModul" name="file" type="file" required accept=".pdf,.doc,.docx" class="sr-only">
                    </div>

                    <div class="space-y-1.5">
                        <label for="deskripsi" class="block text-sm font-semibold text-slate-800">
                            Deskripsi <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            placeholder="Catatan capaian pembelajaran, ATP, atau petunjuk asesmen..."
                            class="w-full px-3.5 py-2.5 rounded-lg bg-slate-100 text-slate-800 placeholder:text-slate-400 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40 transition resize-none">{{ old('deskripsi') }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 rounded-lg bg-blue-600 text-white font-semibold text-sm shadow-md hover:bg-blue-700 transition-all flex items-center justify-center gap-2 active:scale-[0.99]">
                        <i class="bi bi-cloud-arrow-up"></i><span>Unggah ke Arsip</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- DAFTAR ARSIP --}}
        <div class="lg:col-span-7 w-full space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                        <i class="bi bi-archive-fill text-blue-600"></i><span>Arsip Modul Ajar</span>
                    </h2>
                    <p class="text-sm text-slate-500">{{ $total }} dokumen untuk mapel ini</p>
                </div>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input id="cariModul" type="text" placeholder="Cari arsip..."
                        class="w-full sm:w-56 pl-8 pr-3 py-2 rounded-lg bg-white border border-slate-200 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition">
                </div>
            </div>

            <div id="daftarModul" class="space-y-3">
                @forelse ($daftar as $i => $m)
                    @php
                        $ext = $ekstensi($m);
                        $isPdf = $ext === 'pdf';
                        $label = $ext ? strtoupper($ext) : 'FILE';
                    @endphp
                    <div class="modul-card group rounded-xl bg-white border border-slate-200/70 p-5 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-4 min-w-0">
                            <span class="text-xl font-bold text-slate-300 tabular-nums shrink-0 pt-0.5">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>

                            <div class="w-12 h-12 rounded-xl flex flex-col items-center justify-center shrink-0 {{ $isPdf ? 'bg-rose-50 text-rose-600' : 'bg-sky-50 text-sky-600' }}">
                                <i class="bi {{ $isPdf ? 'bi-filetype-pdf' : 'bi-filetype-docx' }} text-xl"></i>
                                <span class="text-[9px] font-bold tracking-wider text-slate-500">{{ $label }}</span>
                            </div>

                            <div class="min-w-0 space-y-1">
                                <h3 class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">{{ $m->judul }}</h3>
                                @if ($m->deskripsi)
                                    <p class="text-sm text-slate-500 line-clamp-2">{{ $m->deskripsi }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                                    <span class="flex items-center gap-1.5">
                                        <i class="bi bi-calendar-event"></i>
                                        <span>{{ $m->created_at?->translatedFormat('d M Y, H:i') }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                            <a href="{{ route('lms.file.modul-ajar', $m) }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-xs font-semibold">
                                <i class="bi bi-download"></i><span>Buka / Unduh</span>
                            </a>
                            <form method="POST" action="{{ route('lms.guru.modul-ajar.destroy', $m) }}"
                                onsubmit="return confirm('Hapus modul ajar ini dari arsip?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus"
                                    class="p-2 rounded-lg bg-slate-100 text-rose-600 hover:bg-rose-600 hover:text-white transition-all">
                                    <i class="bi bi-trash3 text-base"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-10 rounded-xl bg-white border border-dashed border-slate-300 text-center space-y-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-slate-400">
                            <i class="bi bi-folder2-open text-xl"></i>
                        </div>
                        <p class="font-semibold text-slate-800">Belum ada modul ajar</p>
                        <p class="text-sm text-slate-500">Unggah dokumen pertama lewat formulir di samping.</p>
                    </div>
                @endforelse
            </div>

            <div id="kosongCari" class="hidden p-8 rounded-xl bg-white border border-slate-200/70 text-center space-y-2">
                <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-slate-400">
                    <i class="bi bi-search text-xl"></i>
                </div>
                <p class="font-semibold text-slate-800">Tidak ada arsip yang cocok</p>
                <p class="text-sm text-slate-500">Periksa kembali kata kunci pencarian Anda.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Tampilkan nama berkas yang dipilih
        const inputFile = document.getElementById('fileModul');
        inputFile?.addEventListener('change', function () {
            const f = this.files && this.files[0];
            document.getElementById('fileLabel').textContent = f
                ? f.name + ' (' + (f.size / (1024 * 1024)).toFixed(2) + ' MB)'
                : 'Pilih berkas dari perangkat';
        });

        // Cari arsip di sisi klien
        document.getElementById('cariModul')?.addEventListener('input', function (e) {
            const q = e.target.value.toLowerCase().trim();
            let tampil = 0;
            document.querySelectorAll('#daftarModul .modul-card').forEach(card => {
                const cocok = card.textContent.toLowerCase().includes(q);
                card.style.display = cocok ? '' : 'none';
                if (cocok) tampil++;
            });
            const adaKartu = document.querySelectorAll('#daftarModul .modul-card').length > 0;
            document.getElementById('kosongCari').classList.toggle('hidden', !(adaKartu && tampil === 0));
        });
    </script>
@endpush