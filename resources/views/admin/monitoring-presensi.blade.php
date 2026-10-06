@extends('admin.layouts.app') {{-- samakan dengan layout yang dipakai halaman Jadwal Guru --}}

@section('title', 'Monitoring Presensi')

@section('content')
    <meta http-equiv="refresh" content="30">

    <div class="mb-3">
        <h4 class="mb-0">Monitoring Presensi</h4>
        <small class="text-muted">
            {{ now()->translatedFormat('l, d F Y H:i') }} · refresh otomatis tiap 30 detik
        </small>
    </div>

    {{-- ===== Banner peringatan ===== --}}
    @php $perlu = $daftarGuru->where('status', 'harus_buka'); @endphp
    @if ($perlu->isNotEmpty())
        <div class="alert alert-danger">
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ $perlu->count() }} guru seharusnya sedang mengajar tapi belum membuka presensi
            </div>
            @foreach ($perlu as $row)
                @foreach ($row['kelas']->where('jadwal_status', 'belum_buka') as $k)
                    <div class="small">
                        <b>{{ $row['guru']->name }}</b> · {{ $k['kelas'] }} · {{ $k['mapel'] }}
                        ({{ $k['jadwal_jam'] }})
                    </div>
                @endforeach
            @endforeach
        </div>
    @endif

    {{-- ===== A. Jadwal hari ini ===== --}}
    <div class="d-flex flex-wrap gap-2 mb-2">
        <span class="badge bg-danger fs-6">Belum buka (jam berjalan): {{ $ringkas['belum_buka'] ?? 0 }}</span>
        <span class="badge bg-warning text-dark fs-6">Terlewat: {{ $ringkas['terlewat'] ?? 0 }}</span>
        <span class="badge bg-success fs-6">Sudah buka: {{ $ringkas['dibuka'] ?? 0 }}</span>
        <span class="badge bg-secondary fs-6">Belum waktunya: {{ $ringkas['menunggu'] ?? 0 }}</span>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Jadwal hari ini</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Jam</th><th>Guru</th><th>Mapel · Kelas</th><th>Ruangan</th>
                        <th>Status</th><th>Presensi siswa</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalHariIni as $r)
                        <tr class="{{ $r['status'] === 'belum_buka' ? 'table-danger' : ($r['status'] === 'terlewat' ? 'table-warning' : '') }}">
                            <td class="text-nowrap">{{ $r['mulai'] }} – {{ $r['selesai'] }}</td>
                            <td class="fw-semibold">{{ $r['guru'] }}</td>
                            <td>{{ $r['mapel'] }} · {{ $r['kelas'] }}</td>
                            <td>{{ $r['ruangan'] ?: '-' }}</td>
                            <td>
                                @switch($r['status'])
                                    @case('dibuka')
                                        <span class="badge bg-success">
                                            {{ $r['sesi_aktif'] ? 'Presensi aktif' : 'Sudah dibuka' }}
                                        </span>
                                        <div class="small text-muted">sejak {{ $r['dibuka_at']?->format('H:i') }}</div>
                                        @break
                                    @case('belum_buka')
                                        <span class="badge bg-danger">Belum buka presensi</span>
                                        @break
                                    @case('terlewat')
                                        <span class="badge bg-warning text-dark">Tidak membuka</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">Belum waktunya</span>
                                @endswitch
                            </td>
                            <td>{{ $r['sudah'] }}/{{ $r['total'] }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.monitoring-presensi.detail', $r['pengampu_id']) }}"
                                    class="btn btn-sm btn-outline-dark">Detail siswa</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada jadwal hari ini. Atur di menu Jadwal Guru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== B. Status per guru ===== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <h5 class="mb-0">Status presensi per guru</h5>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-danger">Harus buka: {{ $jumlahHarusBuka }}</span>
            <span class="badge bg-warning text-dark">Terlewat: {{ $jumlahTerlewat }}</span>
            <span class="badge bg-success">Sedang buka: {{ $jumlahBuka }}</span>
            <span class="badge bg-secondary">Tutup: {{ $jumlahTutup }}</span>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:130px">Status</th>
                        <th style="width:220px">Guru</th>
                        <th>Mengajar di (kelas · mapel)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarGuru as $row)
                        <tr class="{{ $row['status'] === 'harus_buka' ? 'table-danger' : ($row['status'] === 'terlewat' ? 'table-warning' : '') }}">
                            <td>
                                @switch($row['status'])
                                    @case('buka')       <span class="badge bg-success">Sedang buka</span> @break
                                    @case('harus_buka') <span class="badge bg-danger">Harus buka</span> @break
                                    @case('terlewat')   <span class="badge bg-warning text-dark">Terlewat</span> @break
                                    @default            <span class="badge bg-secondary">Tutup</span>
                                @endswitch
                            </td>
                            <td class="fw-semibold">{{ $row['guru']->name }}</td>
                            <td>
                                @foreach ($row['kelas'] as $k)
                                    @php
                                        $cls = match (true) {
                                            $k['aktif'] => 'text-success fw-semibold',
                                            $k['jadwal_status'] === 'belum_buka' => 'text-danger fw-semibold',
                                            $k['jadwal_status'] === 'terlewat' => 'text-warning-emphasis fw-semibold',
                                            default => 'text-muted',
                                        };
                                    @endphp
                                    <div class="small py-1 {{ $cls }}">
                                        <i class="bi {{ $k['aktif'] ? 'bi-broadcast' : 'bi-door-closed' }}"></i>
                                        {{ $k['kelas'] }} · {{ $k['mapel'] }}

                                        @if ($k['aktif'])
                                            <span class="fw-normal">
                                                — tutup {{ $k['ditutup_at']->format('H:i') }},
                                                {{ $k['sudah'] }}/{{ $k['total'] }} siswa
                                                <a href="{{ route('admin.monitoring-presensi.detail', $k['pengampu_id']) }}">detail</a>
                                            </span>
                                        @elseif ($k['jadwal_status'] === 'belum_buka')
                                            <span class="fw-normal">— jadwal {{ $k['jadwal_jam'] }} sedang berjalan, presensi belum dibuka</span>
                                        @elseif ($k['jadwal_status'] === 'terlewat')
                                            <span class="fw-normal">— jadwal {{ $k['jadwal_jam'] }} sudah lewat, presensi tidak dibuka</span>
                                        @elseif ($k['jadwal_status'] === 'menunggu')
                                            <span class="fw-normal">— jadwal hari ini {{ $k['jadwal_jam'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data guru pengampu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection