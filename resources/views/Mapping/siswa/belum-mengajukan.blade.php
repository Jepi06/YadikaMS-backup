@extends('Mapping.layouts.app')

@section('title', 'Belum Mengajukan PKL')
@section('page-title', 'Siswa Belum Mengajukan PKL')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-exclamation-circle text-warning me-1"></i>
                Siswa Belum Mengajukan PKL
            </h5>
            <p class="text-muted mb-0 small">
                Total <strong>{{ $siswa->total() }}</strong> siswa belum mengajukan tempat PKL (atau semua pengajuannya ditolak).
            </p>
        </div>
        <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Siswa
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Filter Kelas</label>
                    <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>
                                {{ $k->nama_kelas }} — {{ $k->jurusan->nama ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if (request('kelas_id'))
                    <div class="col-auto">
                        <a href="{{ route('siswa.belum-mengajukan') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>JK</th>
                        <th>No. HP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswa as $s)
                        <tr>
                            <td>{{ $siswa->firstItem() + $loop->index }}</td>
                            <td>{{ $s->nis }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $s->kelas->jurusan->nama ?? '-' }}</td>
                            <td>{{ $s->jenis_kelamin }}</td>
                            <td>{{ $s->no_hp ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                                Semua siswa sudah mengajukan PKL 🎉
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($siswa->hasPages())
            <div class="card-footer bg-white">
                {{ $siswa->links() }}
            </div>
        @endif
    </div>
@endsection