@extends('Mapping.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="fs-1 opacity-75"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $stats['total_siswa'] }}</div>
                        <div class="small opacity-75">Total Siswa</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="fs-1 opacity-75"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $stats['sudah_ditempatkan'] }}</div>
                        <div class="small opacity-75">Sudah Ditempatkan</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- BARU: card ini menampilkan $stats['belum_mengajukan'] yang sudah
         dikirim DashboardController tapi sebelumnya tidak ditampilkan --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="fs-1 opacity-75"><i class="bi bi-exclamation-circle-fill"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $stats['belum_mengajukan'] }}</div>
                        <div class="small opacity-75">Belum Mengajukan PKL</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="fs-1 opacity-75"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $stats['menunggu_approval'] }}</div>
                        <div class="small opacity-75">Menunggu Approval</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="fs-1 opacity-75"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $stats['total_tempat'] }}</div>
                        <div class="small opacity-75">Tempat PKL</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-clock-history me-2 text-primary"></i>Penempatan Terbaru</span>
                    <a href="{{ route('penempatan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tempat PKL</th>
                                    <th>Guru Pembimbing</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPenempatan as $p)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $p->siswa->nama }}</div>
                                            <small class="text-muted">{{ $p->siswa->nis }}</small>
                                        </td>
                                        <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                                        <td>{{ $p->tempatPkl->nama_tempat }}</td>
                                        <td>{{ $p->guruPembimbing?->nama ?? '-' }}</td>
                                        <td>{!! $p->status_badge !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada data penempatan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARU: widget daftar siswa yang belum mengajukan PKL,
         dari $siswaBelumMengajukan yang dikirim DashboardController --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-exclamation-circle me-2 text-danger"></i>Belum Mengajukan</span>
                    <a href="{{ route('siswa.belum-mengajukan') }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($siswaBelumMengajukan as $s)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $s->nama }}</div>
                                <small class="text-muted">{{ $s->kelas->nama_kelas ?? '-' }}</small>
                            </div>
                            <small class="text-muted font-monospace">{{ $s->nis }}</small>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-check2-all fs-4 d-block mb-1 text-success"></i>
                            Semua siswa sudah mengajukan PKL.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
