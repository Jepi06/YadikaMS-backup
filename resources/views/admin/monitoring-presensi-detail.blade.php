@extends('admin.layouts.app')

@section('title', 'Detail Presensi')

@section('content')
    @if ($tanggal === now()->toDateString())
        <meta http-equiv="refresh" content="30">
    @endif

    <a href="{{ route('admin.monitoring-presensi.index') }}" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> Kembali ke monitoring
    </a>

    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-2">
        <div>
            <h4 class="mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} · {{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <small class="text-muted">
                Guru: {{ $pengampuMapel->guru->name ?? '-' }} ·
                {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }} ·
                @if ($sesi)
                    presensi dibuka {{ $sesi->dibuka_at->format('H:i') }}, tutup {{ $sesi->ditutup_at->format('H:i') }}
                @else
                    presensi tidak dibuka
                @endif
            </small>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm"
                onchange="this.form.submit()">
        </form>
    </div>

    <div class="d-flex gap-2 mb-3">
        <span class="badge bg-success fs-6">Sudah: {{ $sudah }}</span>
        <span class="badge bg-danger fs-6">Belum: {{ $belum }}</span>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th style="width:50px">#</th><th>Nama siswa</th><th>Status</th><th>Waktu</th></tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $r)
                        @php $p = $r['presensi']; @endphp
                        <tr class="{{ $p ? '' : 'table-danger' }}">
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $r['siswa']->nama ?? $r['siswa']->name ?? '-' }}</td>
                            <td>
                                @if (! $p)
                                    <span class="badge bg-danger">Belum presensi</span>
                                @else
                                    @php
                                        $warna = ['Hadir' => 'success', 'Izin' => 'info', 'Sakit' => 'warning', 'Alpa' => 'dark'][$p->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $warna }}">{{ $p->status }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $p?->created_at?->format('H:i') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection