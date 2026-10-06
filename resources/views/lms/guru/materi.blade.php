{{-- resources/views/lms/guru/materi.blade.php --}}
{{--
    Dipanggil dari Guru\MateriController@index, route: lms.guru.materi.index
    Variabel: $pengampuMapel (with mataPelajaran, kelas) dan daftar materi
    ($materi atau $daftarMateri — salah satu saja cukup).
--}}
@extends('lms.layouts.app')

@section('title', 'Materi - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))
@section('breadcrumb', 'Kelola Materi')

@section('content')
    @php
        $daftar = collect($materi ?? ($daftarMateri ?? []));

        // Nilai mode akses (value radio/select) => tampilan
        $modeInfo = [
            'bebas' => [
                'label' => 'Bebas — Langsung Terbuka',
                'badge' => 'bg-emerald-50 text-emerald-800',
                'dot' => 'bg-emerald-500',
            ],
            'berurutan' => [
                'label' => 'Berurutan — Syarat Materi Sebelumnya',
                'badge' => 'bg-blue-50 text-blue-800',
                'dot' => 'bg-blue-500',
            ],
            'manual' => [
                'label' => 'Dibuka Guru — Terkunci Manual',
                'badge' => 'bg-amber-50 text-amber-800',
                'dot' => 'bg-amber-500',
            ],
            'tanggal' => ['label' => 'Terjadwal', 'badge' => 'bg-purple-50 text-purple-800', 'dot' => 'bg-purple-600'],
        ];
        $modeOf = fn($m) => strtolower($m->mode_akses ?? 'bebas');
        $jadwalOf = fn($m) => $m->buka_pada;

        $total = $daftar->count();
        $terjadwal = $daftar->filter(fn($m) => $modeOf($m) === 'tanggal')->count();
        $manual = $daftar->filter(fn($m) => $modeOf($m) === 'manual')->count();
        $lampiran = $daftar->filter(fn($m) => !empty($m->file_path))->count();
    @endphp

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-2">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500">
                <a href="{{ route('lms.guru.kelas.index') }}" class="hover:text-blue-600 flex items-center gap-1">
                    <i class="bi bi-door-open"></i> Kelas Saya
                </a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="font-semibold text-slate-800">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }}</span>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="font-semibold text-blue-600">Kelola Materi</span>
            </nav>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    {{ $pengampuMapel->mataPelajaran->nama ?? '-' }}
                </h1>
                <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                    {{ $pengampuMapel->kelas->nama_kelas ?? '-' }}
                    @if ($pengampuMapel->tahun_ajaran ?? null)
                        · {{ $pengampuMapel->tahun_ajaran }}
                    @endif
                </span>
            </div>
        </div>
        <a href="{{ route('lms.guru.kelas.index') }}"
            class="self-start md:self-auto inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition">
            <i class="bi bi-arrow-left"></i> Kembali ke Kelas
        </a>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([['Total Materi', $total, 'bi-journal-text', 'bg-blue-50 text-blue-600'], ['Akses Terjadwal', $terjadwal, 'bi-clock', 'bg-purple-50 text-purple-600'], ['Dibuka Manual', $manual, 'bi-lock', 'bg-amber-50 text-amber-600'], ['Lampiran File', $lampiran, 'bi-paperclip', 'bg-slate-100 text-slate-600']] as [$label, $nilai, $ikon, $warna])
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $nilai }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl {{ $warna }} flex items-center justify-center text-xl">
                    <i class="bi {{ $ikon }}"></i>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Form tambah materi --}}
    <details class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden"
        {{ $errors->any() ? 'open' : '' }}>
        <summary class="list-none cursor-pointer px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="bi bi-file-earmark-plus"></i>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800">Tambah Materi Pembelajaran Baru</h2>
                    <p class="text-xs text-slate-500">Unggah file, tautan video, atau instruksi untuk siswa</p>
                </div>
            </div>
            <i class="bi bi-chevron-down text-slate-400 transition-transform group-open:rotate-180"></i>
        </summary>

        <form method="POST" action="{{ route('lms.guru.materi.store', $pengampuMapel) }}" enctype="multipart/form-data"
            class="px-6 pb-6 pt-2 space-y-5">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Kiri --}}
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Judul Materi <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required
                            placeholder="Contoh: 05. RESTful API dengan Laravel Sanctum"
                            class="mt-1 w-full h-11 px-4 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Tautan Video / Referensi
                            <span class="text-xs font-normal text-slate-400">(opsional)</span></label>
                        <div class="relative mt-1">
                            <i class="bi bi-link-45deg absolute left-3 top-3 text-slate-400"></i>
                            <input type="url" name="link_url" value="{{ old('link_url') }}"
                                placeholder="https://youtu.be/... atau https://drive.google.com/..."
                                class="w-full h-11 pl-9 pr-4 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Deskripsi & Instruksi</label>
                        <textarea name="deskripsi" rows="4" placeholder="Poin capaian pembelajaran atau instruksi praktikum..."
                            class="mt-1 w-full p-4 rounded-xl bg-white border border-slate-200 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                {{-- Kanan --}}
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-slate-700 flex items-center justify-between">
                            <span>Berkas Lampiran</span>
                            <span class="text-xs font-normal text-slate-400">Maks. 10 MB</span>
                        </label>
                        <div
                            class="relative mt-1 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 hover:bg-slate-100 transition p-5 text-center">
                            <input type="file" name="file" id="inputFile"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="document.getElementById('namaFile').textContent = this.files[0]?.name ?? 'Klik untuk telusuri atau seret berkas ke sini'">
                            <i class="bi bi-cloud-arrow-up text-2xl text-blue-600"></i>
                            <p id="namaFile" class="text-sm text-slate-600 mt-1">Klik untuk telusuri atau seret berkas ke
                                sini</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">Mode Hak Akses Siswa</label>
                        <div class="mt-1 grid grid-cols-2 gap-1.5 p-1 bg-slate-100 rounded-xl">
                            @foreach (['bebas' => 'Bebas', 'berurutan' => 'Berurutan', 'manual' => 'Dibuka Guru', 'tanggal' => 'Terjadwal'] as $val => $lbl)
                                <label class="cursor-pointer">
                                    <input type="radio" name="mode_akses" value="{{ $val }}"
                                        class="peer sr-only" onchange="toggleJadwalBaru()"
                                        {{ old('mode_akses', 'bebas') === $val ? 'checked' : '' }}>
                                    <div
                                        class="px-3 py-2 rounded-lg text-center text-sm font-semibold text-slate-500 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm transition flex items-center justify-center gap-1.5">
                                        <span
                                            class="w-2 h-2 rounded-full {{ $modeInfo[$val]['dot'] }}"></span>{{ $lbl }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div id="boxJadwalBaru" class="{{ old('mode_akses') === 'tanggal' ? '' : 'hidden' }}">
                        <label class="text-sm font-semibold text-slate-700">Tanggal & Waktu Terbuka</label>
                        <input type="datetime-local" name="buka_pada" value="{{ old('buka_pada') }}"
                            class="mt-1 w-full h-11 px-4 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm shadow-blue-500/30 active:scale-95 transition">
                    <i class="bi bi-save"></i> Simpan Materi
                </button>
            </div>
        </form>
    </details>

    {{-- Daftar materi --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Materi Diterbitkan</h2>
            <p class="text-xs text-slate-500">Kelola akses, tautan, dan lampiran tiap materi.</p>
        </div>
        <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-semibold">Semua
            ({{ $total }})</span>
    </div>

    <div class="space-y-4">
        @forelse ($daftar as $i => $m)
            @php
                $mode = $modeOf($m);
                $info = $modeInfo[$mode] ?? $modeInfo['bebas'];
                $jadwal = $jadwalOf($m);
                $terbuka = (bool) $m->dibuka_manual;
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition p-5">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 font-bold text-lg">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="min-w-0 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-slate-900">{{ $m->judul }}</h3>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $info['badge'] }}">
                                    <span class="w-2 h-2 rounded-full {{ $info['dot'] }}"></span>
                                    @if ($mode === 'tanggal' && $jadwal)
                                        Terjadwal · {{ \Carbon\Carbon::parse($jadwal)->translatedFormat('d M Y, H:i') }}
                                    @else
                                        {{ $info['label'] }}
                                    @endif
                                </span>
                            </div>

                            @if ($m->deskripsi ?? null)
                                <p class="text-sm text-slate-600 leading-relaxed max-w-3xl">{{ $m->deskripsi }}</p>
                            @endif

                            <div class="flex flex-wrap items-center gap-2">
                                @if ($m->file_path ?? null)
                                    <a href="{{ route('lms.file.materi', $m) }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-xs font-semibold">
                                        <i class="bi bi-download"></i><span>Buka / Unduh</span>
                                    </a>
                                @endif
                                @if ($m->link_url ?? null)
                                    <a href="{{ $m->link_url }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-sm text-slate-700 transition">
                                        <i class="bi bi-box-arrow-up-right text-rose-500"></i>
                                        <span
                                            class="font-medium truncate max-w-[16rem]">{{ \Illuminate\Support\Str::limit(preg_replace('#^https?://#', '', $m->link_url), 40) }}</span>
                                    </a>
                                @endif
                            </div>

                            {{-- Ubah tautan (PUT lms.guru.materi.link) --}}
                            @if (Route::has('lms.guru.materi.link'))
                                <details class="text-sm">
                                    <summary
                                        class="cursor-pointer text-blue-600 hover:text-blue-700 font-semibold list-none inline-flex items-center gap-1">
                                        <i class="bi bi-pencil"></i> Ubah tautan
                                    </summary>
                                    <form method="POST" action="{{ route('lms.guru.materi.link', $m) }}"
                                        class="mt-2 flex gap-2 max-w-xl">
                                        @csrf @method('PUT')
                                        <input type="url" name="link_url" value="{{ $m->link_url }}"
                                            placeholder="https://..."
                                            class="flex-1 h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                        <button
                                            class="px-3 h-9 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">Simpan</button>
                                    </form>
                                </details>
                            @endif
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex flex-wrap items-center gap-2 shrink-0 self-end lg:self-start">
                        @if ($mode === 'manual')
                            <form method="POST" action="{{ route('lms.guru.materi.toggle', $m) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-white text-xs font-semibold shadow-sm transition {{ $terbuka ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }}">
                                    <i class="bi {{ $terbuka ? 'bi-unlock' : 'bi-lock' }}"></i>
                                    {{ $terbuka ? 'Kunci Lagi' : 'Buka Sekarang' }}
                                </button>
                            </form>
                        @endif

                        {{-- Ubah mode akses (PUT lms.guru.materi.akses) --}}
                        <form method="POST" action="{{ route('lms.guru.materi.akses', $m) }}"
                            class="flex items-center gap-1.5 bg-slate-100 rounded-lg p-1">
                            @csrf @method('PUT')
                            <i class="bi bi-sliders text-slate-500 ml-2"></i>
                            <select name="mode_akses" onchange="aksesBerubah(this)"
                                class="bg-transparent text-sm font-semibold text-slate-700 px-1 py-1 focus:outline-none cursor-pointer">
                                @foreach (['bebas' => 'Bebas', 'berurutan' => 'Berurutan', 'manual' => 'Dibuka Guru', 'tanggal' => 'Terjadwal'] as $val => $lbl)
                                    <option value="{{ $val }}" @selected($mode === $val)>{{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="datetime-local" name="buka_pada"
                                value="{{ $jadwal ? \Carbon\Carbon::parse($jadwal)->format('Y-m-d\TH:i') : '' }}"
                                class="jadwal-input text-xs h-8 px-2 rounded-md border border-slate-200 {{ $mode === 'tanggal' ? '' : 'hidden' }}">
                            <button type="submit"
                                class="btn-simpan-akses px-2.5 h-8 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold {{ $mode === 'tanggal' ? '' : 'hidden' }}">
                                Simpan
                            </button>
                        </form>

                        <form method="POST" action="{{ route('lms.guru.materi.destroy', $m) }}"
                            onsubmit="return confirm('Hapus materi ini? File lampiran ikut terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Hapus materi"
                                class="w-9 h-9 rounded-lg hover:bg-rose-50 text-slate-500 hover:text-rose-600 flex items-center justify-center transition">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-400 text-sm">
                Belum ada materi untuk kelas ini. Klik "Tambah Materi Pembelajaran Baru" di atas.
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        // Form tambah: tampilkan input jadwal hanya untuk mode "terjadwal"
        function toggleJadwalBaru() {
            const mode = document.querySelector('input[name="mode_akses"]:checked')?.value;
            document.getElementById('boxJadwalBaru').classList.toggle('hidden', mode !== 'tanggal');
        }

        // Daftar materi: ganti mode langsung tersimpan, kecuali "terjadwal" (perlu isi tanggal dulu)
        function aksesBerubah(select) {
            const form = select.closest('form');
            const terjadwal = select.value === 'tanggal';
            form.querySelector('.jadwal-input').classList.toggle('hidden', !terjadwal);
            form.querySelector('.btn-simpan-akses').classList.toggle('hidden', !terjadwal);
            if (!terjadwal) form.submit();
        }
    </script>
@endpush
