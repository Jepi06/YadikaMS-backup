{{-- resources/views/lms/guru/tugas.blade.php
     Variabel dari controller:
       $pengampuMapel → PengampuMapel (route model binding), relasi mataPelajaran & kelas (siswa_count) sudah dimuat
       $tugas         → koleksi Tugas milik pengampuMapel ini, relasi pengumpulan sudah dimuat --}}
@extends('lms.layouts.app')

@section('title', 'Kelola Tugas - LMS Yadika')
@section('breadcrumb', 'Tugas')

@section('content')
    @php
        use Illuminate\Support\Carbon;

        $daftar = collect($tugas ?? [])
            ->sortByDesc(fn($t) => $t->batas_waktu)
            ->values();

        $mp = $pengampuMapel->mataPelajaran ?? null;
        $namaMapel = $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        $kelas = $pengampuMapel->kelas ?? null;
        $namaKelas = $kelas->nama_kelas ?? ($kelas->nama ?? null);
        $semester = $pengampuMapel->semester ?? null;
        $tahunAjaran = $pengampuMapel->tahun_ajaran ?? null;
        $jumlahSiswa = (int) ($kelas->siswa_count ?? 0);

        // --- helper per tugas ---
        $isKelompok = fn($t) => (bool) $t->is_kelompok;
        $deadline = fn($t) => $t->batas_waktu ? Carbon::parse($t->batas_waktu) : null;
        $ditutupManual = fn($t) => (bool) $t->ditutup_manual;
        $statusTugas = function ($t) use ($deadline, $ditutupManual) {
            if ($ditutupManual($t)) return 'tertutup';
            $d = $deadline($t);
            if ($d && $d->isPast()) return 'berakhir';
            return 'terbuka';
        };
        $terkumpul = fn($t) => $t->pengumpulan->whereNotNull('dikumpulkan_at')->count();

        // --- ringkasan ---
        $total = $daftar->count();
        $totalTerbuka = $daftar->filter(fn($t) => $statusTugas($t) === 'terbuka')->count();
        $totalKelompok = $daftar->filter(fn($t) => $isKelompok($t))->count();
        $totalIndividu = $total - $totalKelompok;
        $sumTerkumpul = $daftar->sum(fn($t) => $terkumpul($t));
        $persenRata = ($total > 0 && $jumlahSiswa > 0)
            ? min(100, round($sumTerkumpul / ($total * $jumlahSiswa) * 100))
            : 0;
        $defaultDeadline = old('batas_waktu', now()->addWeek()->setTime(23, 59)->format('Y-m-d\TH:i'));
    @endphp

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.guru.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('lms.guru.kelas.index') }}" class="hover:text-blue-600 transition-colors">
                    Kelas {{ $namaKelas ?? 'Saya' }}
                </a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Tugas Siswa</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3 pt-0.5">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Tugas & Pengumpulan</h1>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">{{ $namaMapel }}</span>
                    @if ($namaKelas || $semester)
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                            {{ $namaKelas }}@if ($namaKelas && $semester) • @endif{{ $semester }} {{ $tahunAjaran }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 self-start md:self-auto">
            <a href="{{ route('lms.guru.kelas.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition-colors">
                <i class="bi bi-arrow-left"></i>Kembali ke Kelas
            </a>
            <button type="button" onclick="document.getElementById('formBuatTugas').scrollIntoView({behavior: 'smooth'})"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                <i class="bi bi-plus-circle"></i>Buat Tugas Baru
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 flex items-start gap-2">
            <i class="bi bi-check-circle mt-0.5"></i><span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Tugas</div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900">{{ $total }}</span>
                    <span class="text-xs text-slate-500">tugas</span>
                </div>
                <div class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $totalTerbuka }} masih terbuka
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-journal-check text-2xl"></i></div>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Tugas Individu</div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900">{{ $totalIndividu }}</span>
                    <span class="text-xs text-slate-500">mandiri</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600"><i class="bi bi-person-fill text-2xl"></i></div>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Tugas Kelompok</div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900">{{ $totalKelompok }}</span>
                    <span class="text-xs text-slate-500">kolaboratif</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600"><i class="bi bi-people-fill text-2xl"></i></div>
        </div>

        <div class="p-4 rounded-xl bg-white border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Rata-rata Pengumpulan</div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-slate-900">{{ $persenRata }}%</span>
                    <span class="text-xs text-slate-500">{{ $jumlahSiswa }} siswa</span>
                </div>
                <div class="w-28 h-1.5 bg-slate-100 rounded-full overflow-hidden mt-1">
                    <div class="bg-blue-600 h-full rounded-full" style="width: {{ $persenRata }}%"></div>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i class="bi bi-patch-check-fill text-2xl"></i></div>
        </div>
    </section>

    {{-- FORM BUAT TUGAS --}}
    <section id="formBuatTugas" class="p-6 rounded-xl bg-white border border-slate-200/70 shadow-sm space-y-6">
        <div class="flex items-center gap-3 pb-1">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-pencil-square text-xl"></i></div>
            <div>
                <h2 class="font-bold text-slate-900 text-lg">Buat Penugasan Baru</h2>
                <p class="text-sm text-slate-500">Tugas akan muncul untuk siswa {{ $namaKelas ?? 'kelas ini' }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-start gap-2"><i class="bi bi-exclamation-circle mt-0.5"></i><span>{{ $error }}</span></div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('lms.guru.tugas.store', $pengampuMapel) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div class="lg:col-span-2 space-y-1.5">
                    <label for="judul" class="block text-sm font-semibold text-slate-800">Judul Tugas <span class="text-rose-500">*</span></label>
                    <input id="judul" name="judul" type="text" required value="{{ old('judul') }}"
                        placeholder="Contoh: Praktik 04 - REST API Laravel & Autentikasi"
                        class="w-full h-10 px-3.5 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40 transition">
                </div>
                <div class="space-y-1.5">
                    <label for="batas_waktu" class="block text-sm font-semibold text-slate-800">Batas Waktu <span class="text-rose-500">*</span></label>
                    <input id="batas_waktu" name="batas_waktu" type="datetime-local" required value="{{ $defaultDeadline }}"
                        class="w-full h-10 px-3.5 rounded-lg bg-slate-100 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40 transition">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="deskripsi" class="block text-sm font-semibold text-slate-800">Petunjuk Pengerjaan</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    placeholder="Tuliskan instruksi, format pengumpulan, tautan referensi..."
                    class="w-full p-3.5 rounded-lg bg-slate-100 text-sm placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/40 transition resize-y">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-800">Lampiran <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <label for="lampiran" class="p-4 rounded-xl bg-slate-100 hover:bg-slate-200/70 flex flex-col items-center justify-center text-center cursor-pointer transition group">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-600 mb-2 group-hover:scale-105 transition-transform">
                            <i class="bi bi-cloud-arrow-up text-xl"></i>
                        </div>
                        <p id="lampiranLabel" class="text-sm font-semibold text-slate-800">Pilih berkas lampiran</p>
                        <p class="text-xs text-slate-500 mt-0.5">PDF, ZIP, DOCX, dll. (maks. 10 MB)</p>
                    </label>
                    <input id="lampiran" name="lampiran" type="file" class="sr-only">
                </div>

                <div class="p-4 rounded-xl bg-slate-100">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-0.5">
                            <span class="text-sm font-semibold text-slate-800 flex items-center gap-1.5">
                                <i class="bi bi-diagram-3 text-blue-600"></i>Tugas Kelompok
                            </span>
                            <p class="text-xs text-slate-500">
                                Siswa membentuk kelompok sendiri di portal, lalu berkas dikumpulkan per kelompok.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
                            <input type="checkbox" name="is_kelompok" value="1" class="sr-only peer" @checked(old('is_kelompok'))>
                            <div class="w-11 h-6 bg-slate-300 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500/40 peer-checked:bg-blue-600 transition-colors
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                    <i class="bi bi-rocket-takeoff"></i>Simpan & Terbitkan Tugas
                </button>
            </div>
        </form>
    </section>

    {{-- DAFTAR TUGAS --}}
    <section class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-slate-900 text-lg">Daftar Tugas & Pengumpulan</h2>
                <p class="text-sm text-slate-500">Pantau progres, atur akses, dan beri penilaian</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input id="cariTugas" type="text" placeholder="Cari nama tugas..."
                        class="h-9 pl-8 pr-3 w-44 sm:w-56 rounded-lg bg-white border border-slate-200 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                </div>
                <select id="filterTugas"
                    class="h-9 px-3 rounded-lg bg-white border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer">
                    <option value="semua">Semua Status</option>
                    <option value="terbuka">Masih Terbuka</option>
                    <option value="tertutup">Sudah Ditutup / Berakhir</option>
                    <option value="kelompok">Tugas Kelompok</option>
                </select>
            </div>
        </div>

        <div id="daftarTugas" class="space-y-3">
            @forelse ($daftar as $t)
                @php
                    $status = $statusTugas($t);
                    $kelompok = $isKelompok($t);
                    $d = $deadline($t);
                    $jmlKumpul = $terkumpul($t);
                    $persen = $jumlahSiswa > 0 ? min(100, round($jmlKumpul / $jumlahSiswa * 100)) : 0;
                    $belum = max(0, $jumlahSiswa - $jmlKumpul);
                    $rataNilai = $t->pengumpulan->whereNotNull('nilai')->avg('nilai');
                    $lampiran = $t->file_lampiran;
                    $grup = ($status === 'terbuka' ? 'terbuka' : 'tertutup') . ($kelompok ? ' kelompok' : '');
                    $panelId = 'panel-tugas-' . $t->id;
                @endphp

                <div class="tugas-card rounded-xl bg-white border border-slate-200/70 shadow-sm overflow-hidden transition-all {{ $status !== 'terbuka' ? 'opacity-90 hover:opacity-100' : '' }}"
                    data-grup="{{ $grup }}">
                    <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        {{-- Kiri --}}
                        <div class="space-y-2 min-w-0 max-w-xl">
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($status === 'terbuka')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Terbuka
                                    </span>
                                @elseif ($status === 'berakhir')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Batas waktu lewat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Ditutup
                                    </span>
                                @endif

                                @if ($kelompok)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                                        <i class="bi bi-people-fill"></i>Kelompok
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                                        <i class="bi bi-person-fill"></i>Individu
                                    </span>
                                @endif

                                @if ($lampiran)
                                    <a href="{{ route('lms.file.tugas', $t) }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100">
                                        <i class="bi bi-paperclip"></i>Lampiran
                                    </a>
                                @endif
                            </div>

                            <div>
                                <h3 class="judul-tugas font-semibold text-slate-900 truncate">{{ $t->judul }}</h3>
                                @if ($d)
                                    <p class="text-sm text-slate-500 flex flex-wrap items-center gap-1.5 mt-0.5">
                                        <i class="bi bi-clock {{ $status === 'terbuka' ? 'text-sky-600' : 'text-slate-400' }}"></i>
                                        Deadline: <strong class="text-slate-800">{{ $d->translatedFormat('l, d M Y • H:i') }} WIB</strong>
                                        @if ($status === 'terbuka')
                                            <span class="text-rose-600 font-medium">({{ $d->diffForHumans(['parts' => 1]) }})</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Tengah: progres --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 shrink-0">
                            <div class="space-y-1 text-left sm:text-right">
                                <div class="text-xs text-slate-500">Terkumpul {{ $jmlKumpul }} dari {{ $jumlahSiswa }} siswa</div>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 h-2 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $persen >= 100 ? 'bg-emerald-600' : 'bg-blue-600' }}" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold {{ $persen >= 100 ? 'text-emerald-700' : 'text-slate-900' }}">{{ $persen }}%</span>
                                </div>
                                <div class="text-xs text-slate-500">
                                    @if ($rataNilai !== null)
                                        Rata-rata nilai: {{ number_format($rataNilai, 1) }}
                                    @elseif ($belum > 0)
                                        <span class="text-sky-700">{{ $belum }} siswa belum mengumpulkan</span>
                                    @else
                                        Semua sudah mengumpulkan
                                    @endif
                                </div>
                            </div>

                            {{-- Aksi --}}
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="togglePanel('{{ $panelId }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 text-blue-600 text-sm font-semibold hover:bg-slate-200 transition-colors">
                                    <i class="bi bi-sliders"></i><span>Atur</span>
                                    <i class="bi bi-chevron-down text-xs transition-transform" id="icon-{{ $panelId }}"></i>
                                </button>
                                <a href="{{ route('lms.guru.tugas.kumpulan', $t) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                                    <i class="bi bi-inbox"></i><span>Lihat & Nilai</span>
                                </a>
                                <form method="POST" action="{{ route('lms.guru.tugas.destroy', $t) }}"
                                    onsubmit="return confirm('Hapus tugas ini? Pengumpulan siswa juga akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Tugas"
                                        class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                        <i class="bi bi-trash3 text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Panel pengaturan cepat --}}
                    <div id="{{ $panelId }}" class="hidden p-5 bg-slate-50 border-t border-slate-200/70 space-y-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                            <i class="bi bi-sliders text-blue-600"></i>Pengaturan Cepat & Kontrol Akses
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Perbarui deadline --}}
                            <form method="POST" action="{{ route('lms.guru.tugas.update', $t) }}" class="space-y-1.5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="judul" value="{{ $t->judul }}">
                                <input type="hidden" name="deskripsi" value="{{ $t->deskripsi }}">
                                <input type="hidden" name="mode_buka" value="{{ $t->mode_buka ?? 'bebas' }}">
                                @if ($t->mulai_pada)
                                    <input type="hidden" name="mulai_pada" value="{{ Carbon::parse($t->mulai_pada)->format('Y-m-d\TH:i') }}">
                                @endif
                                <label class="text-xs font-semibold text-slate-500">Perbarui Batas Waktu</label>
                                <div class="flex items-center gap-2">
                                    <input type="datetime-local" name="batas_waktu" required
                                        value="{{ $d?->format('Y-m-d\TH:i') }}"
                                        class="flex-1 h-9 px-3 rounded-lg bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                    <button type="submit"
                                        class="h-9 px-3.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors">
                                        Simpan
                                    </button>
                                </div>
                            </form>

                            {{-- Akses --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-500">Akses Pengumpulan</label>
                                <div class="flex items-center gap-2">
                                    @if (($t->mode_buka ?? null) === 'manual')
                                        <form method="POST" action="{{ route('lms.guru.tugas.toggle-buka', $t) }}" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full h-9 px-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition-colors flex items-center justify-center gap-1">
                                                <i class="bi {{ $t->dibuka_manual ? 'bi-lock' : 'bi-unlock' }}"></i>{{ $t->dibuka_manual ? 'Kunci Lagi' : 'Buka Tugas' }}
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('lms.guru.tugas.toggle-tutup', $t) }}" class="flex-1"
                                        onsubmit="return confirm('{{ $t->ditutup_manual ? 'Batalkan penutupan paksa tugas ini?' : 'Tutup pengumpulan tugas ini sekarang?' }}')">
                                        @csrf
                                        <button type="submit"
                                            class="w-full h-9 px-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-colors flex items-center justify-center gap-1">
                                            <i class="bi {{ $t->ditutup_manual ? 'bi-arrow-counterclockwise' : 'bi-slash-circle' }}"></i>{{ $t->ditutup_manual ? 'Batalkan Penutupan' : 'Tutup Sekarang' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if ($t->deskripsi)
                            <p class="text-sm text-slate-500 border-t border-slate-200 pt-3">{{ $t->deskripsi }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-xl bg-white border border-dashed border-slate-300 text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-slate-400">
                        <i class="bi bi-clipboard-plus text-xl"></i>
                    </div>
                    <p class="font-semibold text-slate-800">Belum ada tugas</p>
                    <p class="text-sm text-slate-500">Buat tugas pertama lewat formulir di atas.</p>
                </div>
            @endforelse
        </div>

        <div id="kosongFilter" class="hidden p-8 rounded-xl bg-white border border-slate-200/70 text-center space-y-1">
            <p class="font-semibold text-slate-800">Tidak ada tugas yang cocok</p>
            <p class="text-sm text-slate-500">Ubah kata kunci atau filter status.</p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function togglePanel(id) {
            const panel = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            if (!panel) return;
            const sembunyi = panel.classList.toggle('hidden');
            icon?.classList.toggle('rotate-180', !sembunyi);
        }

        document.getElementById('lampiran')?.addEventListener('change', function () {
            const f = this.files && this.files[0];
            document.getElementById('lampiranLabel').textContent = f
                ? f.name + ' (' + (f.size / (1024 * 1024)).toFixed(2) + ' MB)'
                : 'Pilih berkas lampiran';
        });

        function terapkanFilter() {
            const q = (document.getElementById('cariTugas').value || '').toLowerCase().trim();
            const f = document.getElementById('filterTugas').value;
            const kartu = document.querySelectorAll('#daftarTugas .tugas-card');
            let tampil = 0;
            kartu.forEach(k => {
                const judul = (k.querySelector('.judul-tugas')?.textContent || '').toLowerCase();
                const grup = (k.dataset.grup || '').split(' ');
                const cocok = judul.includes(q) && (f === 'semua' || grup.includes(f));
                k.style.display = cocok ? '' : 'none';
                if (cocok) tampil++;
            });
            document.getElementById('kosongFilter').classList.toggle('hidden', !(kartu.length > 0 && tampil === 0));
        }
        document.getElementById('cariTugas')?.addEventListener('input', terapkanFilter);
        document.getElementById('filterTugas')?.addEventListener('change', terapkanFilter);
    </script>
@endpush