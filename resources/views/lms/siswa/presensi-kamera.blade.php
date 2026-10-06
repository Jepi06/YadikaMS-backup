{{-- resources/views/lms/siswa/presensi-kamera.blade.php
     Variabel dari Siswa\PresensiController@kamera:
       $siswa → siswa login (relasi kelas sudah di-load) --}}
@extends('lms.layouts.app')

@section('title', 'Presensi Sekarang - LMS Yadika')
@section('breadcrumb', 'Presensi Sekarang')

@section('content')
    @php
        $kelasSiswa = $siswa->kelas->nama_kelas ?? ($siswa->kelas->nama ?? null);
    @endphp

    {{-- HEADER --}}
    <section class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('lms.siswa.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <a href="{{ route('lms.siswa.presensi.riwayat') }}" class="hover:text-blue-600 transition-colors">Presensi</a>
                <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-800 font-semibold">Presensi Sekarang</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Presensi Sekarang</h1>
            <p class="text-sm text-slate-500 max-w-2xl">Arahkan kamera ke QR code dinamis yang ditampilkan guru di depan kelas.</p>
        </div>
        <a href="{{ route('lms.siswa.presensi.riwayat') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition-all self-start md:self-auto">
            <i class="bi bi-clock-history"></i><span>Riwayat Presensi</span>
        </a>
    </section>

    {{-- IDENTITAS --}}
    <section class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0"><i class="bi bi-person-badge text-2xl"></i></div>
        <div class="min-w-0">
            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-semibold">Identitas Siswa</p>
            <p class="font-bold text-slate-900 truncate">{{ $siswa->nama ?? ($siswa->name ?? '-') }}</p>
            <p class="text-xs font-mono text-slate-400">
                @if ($siswa->nisn ?? null)NISN: {{ $siswa->nisn }}@endif
                @if ($kelasSiswa) • {{ $kelasSiswa }}@endif
            </p>
        </div>
    </section>

    {{-- SCANNER --}}
    <section class="w-full max-w-xl mx-auto bg-white border border-slate-200/70 rounded-2xl shadow-sm p-5 sm:p-6 flex flex-col items-center gap-4">
        <div class="w-full aspect-square max-w-md bg-slate-900 rounded-2xl relative overflow-hidden">
            <div id="reader" class="w-full h-full"></div>

            {{-- Bingkai bidik --}}
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div class="w-3/5 aspect-square rounded-xl border-2 border-sky-300/80"></div>
            </div>
        </div>

        <div class="w-full flex items-center gap-3 p-3 rounded-xl bg-slate-50">
            <i class="bi bi-camera-video text-blue-600 text-xl shrink-0"></i>
            <div id="status" class="text-sm text-slate-800 font-medium leading-tight">Menyalakan kamera…</div>
        </div>

        <button type="button" onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-colors">
            <i class="bi bi-arrow-clockwise"></i><span>Muat Ulang Scanner</span>
        </button>
    </section>

    {{-- PANDUAN --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm space-y-1.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="bi bi-brightness-high text-xl"></i></div>
            <h3 class="font-semibold text-slate-900">Pencahayaan Cukup</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Hindari silau dari proyektor dan pastikan QR tidak terhalang bayangan.</p>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm space-y-1.5">
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-blue-600 flex items-center justify-center"><i class="bi bi-arrow-repeat text-xl"></i></div>
            <h3 class="font-semibold text-slate-900">QR Selalu Berganti</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Kode berubah otomatis, jadi screenshot milik teman tidak akan berlaku.</p>
        </div>
        <div class="bg-white border border-slate-200/70 rounded-xl p-4 shadow-sm space-y-1.5">
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-blue-700 flex items-center justify-center"><i class="bi bi-shield-check text-xl"></i></div>
            <h3 class="font-semibold text-slate-900">Izin Akses Kamera</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Jika layar hitam, ketuk ikon gembok di alamat browser, izinkan Camera, lalu muat ulang.</p>
        </div>
    </section>
@endsection

@push('styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        #reader video { border-radius: 1rem; }
    </style>
@endpush

@push('scripts')
    <script>
        const statusEl = document.getElementById('status');
        let sudahDiproses = false;

        function onScanSuccess(decodedText) {
            if (sudahDiproses) return;
            sudahDiproses = true;

            statusEl.textContent = 'QR terdeteksi, memproses presensi…';
            statusEl.className = 'text-sm font-medium leading-tight text-emerald-700';

            // Hasil decode berisi URL lengkap ke /lms/siswa/presensi/scan/{token}
            // yang sudah dibuat guru — cukup arahkan browser ke sana.
            window.location.href = decodedText;
        }

        function onScanFailure() {
            // Dipanggil terus-menerus saat belum ada QR terdeteksi di frame — normal, diamkan saja.
        }

        const scanner = new Html5Qrcode('reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 240, height: 240 } },
            onScanSuccess,
            onScanFailure
        ).then(() => {
            statusEl.textContent = 'Kamera aktif — arahkan ke QR presensi di layar kelas.';
        }).catch((err) => {
            statusEl.textContent = 'Tidak bisa mengakses kamera. Pastikan izin kamera diaktifkan untuk situs ini.';
            statusEl.className = 'text-sm font-medium leading-tight text-rose-700';
        });
    </script>
@endpush