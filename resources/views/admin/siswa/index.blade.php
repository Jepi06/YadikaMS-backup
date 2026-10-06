@extends('admin.layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Manajemen Siswa</h4>
            <p class="text-muted small mb-0">Total {{ $siswa->total() }} siswa terdaftar</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.siswa.import.form') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </a>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
            </a>
        </div>
    </div>

    {{-- ── Alert ───────────────────────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Beberapa baris gagal diimport:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('import_skipped') && count(session('import_skipped')) > 0)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-info-circle-fill me-1"></i>Baris yang dilewati (NIS duplikat):</strong>
            <div class="mt-2 small">
                @foreach (session('import_skipped') as $row)
                    <span class="badge bg-secondary me-1">{{ $row['nis'] }} – {{ $row['nama'] }}</span>
                @endforeach
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Filter ───────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.siswa.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold mb-1">Cari Siswa</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Nama, NIS, atau No. HP…">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Tingkat</label>
                    <select name="tingkat" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach (['X', 'XI', 'XII'] as $t)
                            <option value="{{ $t }}" @selected(request('tingkat') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Jurusan</label>
                    <select name="jurusan_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($jurusan as $j)
                            <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>{{ $j->kode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Kelas</label>
                    <select name="kelas_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tabel ────────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px">#</th>
                        <th>Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th>JK</th>
                        <th style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswa as $i => $s)
                        <tr>
                            <td class="ps-4 text-muted small">
                                {{ $siswa->firstItem() + $i }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Avatar --}}
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                        style="width:36px;height:36px;font-size:.85rem;flex-shrink:0">
                                        {{ strtoupper(substr($s->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="line-height:1.2">{{ $s->nama }}</div>
                                        @if ($s->no_hp)
                                            <small class="text-muted">{{ $s->no_hp }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="font-monospace small">{{ $s->nis }}</span></td>
                            <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                            <td>
                                @if ($s->kelas?->jurusan)
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                        {{ $s->kelas->jurusan->kode }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $s->kelas?->waliKelas?->name ?? '-' }}</td>
                            <td>
                                <span
                                    class="badge {{ $s->jenis_kelamin === 'L' ? 'bg-info text-white' : 'bg-warning' }} bg-opacity-15 
                                            {{ $s->jenis_kelamin === 'L' ? 'text-info' : 'text-danger' }}">
                                    {{ $s->jenis_kelamin === 'L' ? '♂ L' : '♀ P' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.siswa.show', $s) }}" class="btn btn-sm btn-outline-secondary"
                                        title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.siswa.edit', $s) }}" class="btn btn-sm btn-outline-primary"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.siswa.reset-password', $s) }}"
                                        onsubmit="return confirm('Reset password akun {{ addslashes($s->nama) }} ke default \'password\'?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                            title="Reset Password">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}"
                                        onsubmit="return confirm('Hapus siswa {{ addslashes($s->nama) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                Belum ada siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($siswa->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $siswa->firstItem() }}–{{ $siswa->lastItem() }}
                    dari {{ $siswa->total() }} siswa
                </small>
                {{ $siswa->links() }}
            </div>
        @endif
    </div>

@endsection
