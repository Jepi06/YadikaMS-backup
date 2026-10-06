{{-- resources/views/lms/guru/dashboard.blade.php --}}
{{--
    Variabel yang diharapkan dari controller:
    $pengampuMapel  : collection PengampuMapel (with mataPelajaran, kelas.siswa)
    $totalKelas, $tugasBelumDinilai, $totalSiswa, $rataKehadiran (opsional, ada default)
    $tahunAjaran, $semester, $kelasWali (opsional, dipakai layout)
--}}
@extends('lms.layouts.app')

@section('title', 'Dashboard Guru')
@section('breadcrumb', 'Dashboard Guru')

@section('content')
    @php
        $namaGuru = Auth::guard('lms')->user()->nama ?? (Auth::guard('lms')->user()->name ?? 'Guru');
        $totalKelas = $totalKelas ?? $pengampuMapel->count();
        $totalSiswa = $totalSiswa ?? $pengampuMapel->sum(fn($p) => $p->kelas->siswa->count() ?? 0);
        $tugasBelumDinilai = $tugasBelumDinilai ?? 0;
        $rataKehadiran = $rataKehadiran ?? null;
        $warna = ['blue', 'indigo', 'emerald', 'amber', 'rose'];
    @endphp

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 p-6 sm:p-8 text-white shadow-xl shadow-blue-900/10">
        <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute right-20 -bottom-16 w-52 h-52 rounded-full bg-indigo-500/20 blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                @isset($tahunAjaran)
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-semibold text-blue-100 mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Tahun Ajaran Aktif {{ $tahunAjaran }}
                    </div>
                @endisset
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Halo, {{ $namaGuru }} <span class="inline-block">👋</span>
                </h1>
                <p class="mt-2 text-sm sm:text-base text-blue-100/90 max-w-xl">
                    Selamat datang kembali di portal pembelajaran Yadika. Berikut ikhtisar kelas dan agenda penilaian Anda.
                </p>
            </div>

            <a href="{{ Route::has('lms.guru.kelas') ? route('lms.guru.kelas') : '#kelas-diampu' }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white text-blue-800 font-bold text-sm shadow-lg shadow-black/10 hover:bg-blue-50 transition-all active:scale-95">
                <i class="bi bi-qr-code-scan"></i>
                <span>Buka QR Presensi</span>
            </a>
        </div>
    </div>

    {{-- Kartu statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center text-xl">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Aktif</span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $totalKelas }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5">Total Kelas Diampu</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-xl">
                    <i class="bi bi-clipboard-x-fill"></i>
                </div>
                @if ($tugasBelumDinilai > 0)
                    <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">Perlu Review</span>
                @endif
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-rose-600 tracking-tight">{{ $tugasBelumDinilai }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5">Tugas Belum Dinilai</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Semua Rombel</span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $totalSiswa }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5">Siswa Terdaftar</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-xl">
                    <i class="bi bi-clock-history"></i>
                </div>
                <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Hari Ini</span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $rataKehadiran !== null ? $rataKehadiran . '%' : '-' }}</div>
                <div class="text-xs font-medium text-slate-500 mt-0.5">Rata-rata Kehadiran</div>
            </div>
        </div>
    </div>

    {{-- Tabel kelas diampu --}}
    <div id="kelas-diampu" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="bi bi-door-open text-lg"></i>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-lg">Kelas yang Sedang Diampu</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar kelas pembelajaran dan kontrol presensi siswa.</p>
                </div>
            </div>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-xs text-slate-400"></i>
                <input id="cariKelas" type="text" placeholder="Cari mata pelajaran / kelas..."
                    class="pl-8 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-700 w-48 sm:w-64">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tabelKelas">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Mata Pelajaran</th>
                        <th class="py-3.5 px-6">Kelas</th>
                        <th class="py-3.5 px-6">Jumlah Siswa</th>
                        <th class="py-3.5 px-6">Tahun Ajaran</th>
                        <th class="py-3.5 px-6">Semester</th>
                        <th class="py-3.5 px-6 text-right">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($pengampuMapel as $i => $pm)
                        @php $c = $warna[$i % count($warna)]; @endphp
                        <tr class="hover:bg-blue-50/40 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-{{ $c }}-100/70 text-{{ $c }}-700 flex items-center justify-center text-xs flex-shrink-0">
                                        <i class="bi bi-book"></i>
                                    </div>
                                    <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors">
                                        {{ $pm->mataPelajaran->nama ?? '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                                    <i class="bi bi-mortarboard text-slate-400"></i> {{ $pm->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <i class="bi bi-people-fill text-[11px]"></i> {{ $pm->kelas->siswa->count() ?? 0 }} Siswa
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 text-xs font-medium">{{ $pm->tahun_ajaran ?? ($tahunAjaran ?? '-') }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $pm->semester ?? ($semester ?? '-') }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('lms.guru.presensi.index', $pm) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm shadow-blue-500/20 active:scale-95 transition-all">
                                    <i class="bi bi-qr-code-scan"></i>
                                    <span>Presensi</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 text-sm">Belum ada kelas yang Anda ampu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span>Menampilkan {{ $pengampuMapel->count() }} kelas aktif semester ini</span>
            <a href="{{ Route::has('lms.guru.kelas') ? route('lms.guru.kelas') : '#' }}"
                class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                Lihat Kelas Saya <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    {{-- Banner petunjuk --}}
    <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/70 p-5 flex items-start gap-4 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-600/20">
            <i class="bi bi-info-circle-fill text-lg"></i>
        </div>
        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
            <div class="font-bold text-slate-900 mb-0.5">Petunjuk Guru Pengampu:</div>
            Klik tombol <strong class="text-blue-700 bg-blue-100/60 px-1.5 py-0.5 rounded">Presensi</strong> di tiap baris kelas
            untuk membuka sesi QR Code atau mengisi kehadiran siswa secara manual.
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Filter tabel kelas (client-side)
        document.getElementById('cariKelas')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tabelKelas tbody tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
@endpush