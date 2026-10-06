{{-- resources/views/lms/siswa/presensi-riwayat.blade.php
     Variabel dari Siswa\PresensiController@riwayat:
       $riwayat     → paginator PresensiLms (pengampuMapel.mataPelajaran & pengampuMapel.guru sudah di-load)
       $hitung      → ['Hadir' => n, 'Izin' => n, ...] untuk SELURUH riwayat siswa
       $totalQr     → jumlah presensi via QR
       $daftarMapel → PengampuMapel kelas siswa (mataPelajaran di-load) untuk filter --}}
@extends('lms.layouts.app')

@section('title', 'Riwayat Presensi - LMS Yadika')
@section('breadcrumb', 'Riwayat Presensi')

@section('content')
    @php
        $C = \Illuminate\Support\Carbon::class;

        $nHadir = (int) ($hitung['Hadir'] ?? 0);
        $nIzin = (int) ($hitung['Izin'] ?? 0);
        $nSakit = (int) ($hitung['Sakit'] ?? 0);
        $nAlpa = (int) ($hitung['Alpa'] ?? 0);
        $total = $nHadir + $nIzin + $nSakit + $nAlpa;
        $persen = $total > 0 ? round($nHadir / $total * 100, 1) : 0;
        $persenQr = $total > 0 ? round($totalQr / $total * 100, 1) : 0;

        $namaMapelFn = function ($pm) {
            $mp = $pm->mataPelajaran ?? null;
            return $mp->nama ?? ($mp->nama_mapel ?? ($mp->nama_mata_pelajaran ?? 'Mata Pelajaran'));
        };

        $statusAktif = request('status');
        $mapelAktif = request('mapel');

        $badge = [
            'Hadir' => 'bg-emerald-50 text-emerald-700',
            'Izin' => 'bg-sky-50 text-sky-700',
            'Sakit' => 'bg-amber-50 text-amber-700',
            'Alpa' => 'bg-rose-50 text-rose-700',
        ];
        $dot = [
            'Hadir' => 'bg-emerald-500', 'Izin' => 'bg-sky-500', 'Sakit' => 'bg-amber-500', 'Alpa' => 'bg-rose-500',
        ];

        $chip = fn($key) => route('lms.siswa.presensi.riwayat', array_filter(['status' => $key, 'mapel' => $mapelAktif]));
        $kelasChip = fn($aktif) => $aktif
            ? 'bg-blue-600 text-white font-semibold shadow-sm'
            : 'bg-slate-100 text-slate-600 hover:text-slate-900 font-medium';
    @endphp

    {{-- HEADER --}}
    <section class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.siswa.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Riwayat Presensi</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Presensi Siswa</h1>
            <p class="text-sm text-slate-500">Catatan kehadiran seluruh mata pelajaran Anda.</p>
        </div>
        <a href="{{ route('lms.siswa.presensi.kamera') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all self-start lg:self-auto">
            <i class="bi bi-qr-code-scan"></i><span>Presensi Sekarang</span>
        </a>
    </section>

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Persentase Hadir</p>
                <p class="text-3xl font-bold {{ $persen >= 85 ? 'text-emerald-700' : ($persen >= 75 ? 'text-amber-700' : 'text-rose-600') }}">{{ $persen }}%</p>
                <span class="text-xs text-slate-500">dari {{ $total }} pencatatan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i class="bi bi-patch-check-fill text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Total Tercatat</p>
                <p class="text-3xl font-bold text-slate-900">{{ $total }} <span class="text-sm font-normal text-slate-500">sesi</span></p>
                <span class="text-xs text-slate-500">Semua mata pelajaran</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i class="bi bi-calendar-check text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Rincian</p>
                <p class="text-xl font-bold text-slate-900 flex items-center gap-1.5">
                    <span class="text-emerald-700">{{ $nHadir }}H</span><span class="text-slate-300 font-normal">/</span>
                    <span class="text-sky-700">{{ $nIzin }}I</span><span class="text-slate-300 font-normal">/</span>
                    <span class="text-amber-700">{{ $nSakit }}S</span><span class="text-slate-300 font-normal">/</span>
                    <span class="text-rose-600">{{ $nAlpa }}A</span>
                </p>
                <span class="text-xs text-slate-500">Hadir / Izin / Sakit / Alpa</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-blue-600"><i class="bi bi-pie-chart-fill text-2xl"></i></div>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Presensi via QR</p>
                <p class="text-3xl font-bold text-blue-700">{{ $totalQr }} <span class="text-sm font-normal text-slate-500">kali</span></p>
                <span class="text-xs text-slate-500">{{ $persenQr }}% dari total</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700"><i class="bi bi-qr-code text-2xl"></i></div>
        </div>
    </section>

    {{-- FILTER --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm space-y-3">
        <form method="GET" action="{{ route('lms.siswa.presensi.riwayat') }}" class="flex flex-col md:flex-row md:items-center gap-3">
            @if ($statusAktif)<input type="hidden" name="status" value="{{ $statusAktif }}">@endif
            <div class="relative flex-1 max-w-md">
                <select name="mapel" onchange="this.form.submit()"
                    class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach ($daftarMapel as $pm)
                        <option value="{{ $pm->id }}" @selected((string) $mapelAktif === (string) $pm->id)>{{ $namaMapelFn($pm) }}</option>
                    @endforeach
                </select>
            </div>
            @if ($statusAktif || $mapelAktif)
                <a href="{{ route('lms.siswa.presensi.riwayat') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 h-10 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-colors">
                    <i class="bi bi-x-circle"></i>Reset
                </a>
            @endif
        </form>
        <div class="flex flex-wrap items-center gap-1.5 text-xs">
            <span class="text-slate-500 font-semibold mr-1">Filter cepat:</span>
            <a href="{{ $chip(null) }}" class="px-3.5 py-1.5 rounded-lg transition-all {{ $kelasChip(! $statusAktif) }}">Semua ({{ $total }})</a>
            @foreach (['Hadir' => $nHadir, 'Izin' => $nIzin, 'Sakit' => $nSakit, 'Alpa' => $nAlpa] as $st => $n)
                <a href="{{ $chip($st) }}" class="px-3.5 py-1.5 rounded-lg transition-all inline-flex items-center gap-1.5 {{ $kelasChip($statusAktif === $st) }}">
                    <span class="w-2 h-2 rounded-full {{ $dot[$st] }}"></span>{{ $st }} ({{ $n }})
                </a>
            @endforeach
        </div>
    </section>

    {{-- TABEL --}}
    <section class="bg-white border border-slate-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4 whitespace-nowrap">Tanggal & Waktu</th>
                        <th class="py-3.5 px-4">Mata Pelajaran & Pengampu</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Metode</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($riwayat as $p)
                        @php
                            $tgl = $C::parse($p->tanggal);
                            $st = ucfirst(strtolower((string) $p->status));
                            $st = isset($badge[$st]) ? $st : 'Alpa';
                            $src = strtolower((string) ($p->sumber ?? ''));
                            $viaQr = str_contains($src, 'barcode') || str_contains($src, 'qr') || str_contains($src, 'scan');
                            $pm = $p->pengampuMapel;
                            $guru = $pm?->guru;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-900">{{ $tgl->translatedFormat('d M Y') }}</div>
                                <div class="text-xs font-mono {{ $viaQr ? 'text-blue-600' : 'text-slate-400' }} flex items-center gap-1">
                                    <i class="bi bi-clock"></i>{{ $p->updated_at?->format('H:i') }} WIB
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-900">{{ $pm ? $namaMapelFn($pm) : '-' }}</div>
                                <div class="text-xs text-slate-500 flex items-center gap-1"><i class="bi bi-person"></i>{{ $guru->nama ?? ($guru->name ?? '-') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge[$st] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dot[$st] }}"></span>{{ $st }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if ($viaQr)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium"><i class="bi bi-qr-code-scan"></i>QR Barcode</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-medium"><i class="bi bi-pencil"></i>Dicatat Guru</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">{{ $p->keterangan ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <i class="bi bi-calendar-x text-3xl text-slate-300 block mb-2"></i>
                                Belum ada data presensi{{ ($statusAktif || $mapelAktif) ? ' untuk filter ini' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-500">
            <div>
                @if ($riwayat->total() > 0)
                    Menampilkan <span class="font-semibold text-slate-900">{{ $riwayat->firstItem() }}–{{ $riwayat->lastItem() }}</span>
                    dari <span class="font-semibold text-slate-900">{{ $riwayat->total() }}</span> data presensi
                @endif
            </div>
            <div>{{ $riwayat->links() }}</div>
        </div>
    </section>

    <section class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0"><i class="bi bi-info-circle text-xl"></i></div>
        <div>
            <h2 class="font-semibold text-slate-900">Ketentuan Presensi</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Scan QR hanya berlaku selama guru membuka sesi presensi. Jika ada data yang tidak sesuai, hubungi guru mata pelajaran atau wali kelas.
            </p>
        </div>
    </section>
@endsection