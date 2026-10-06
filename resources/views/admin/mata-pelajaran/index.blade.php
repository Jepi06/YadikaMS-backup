{{-- resources/views/admin/mata-pelajaran/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Mata Pelajaran</h4>
        <p class="text-muted small mb-0">Total {{ $mapel->total() }} mata pelajaran</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.mata-pelajaran.import.form') }}"
            class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
        </a>
        <a href="{{ route('admin.mata-pelajaran.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Mata Pelajaran
        </a>
    </div>
</div>

{{-- Alert sukses --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Alert error --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Import errors --}}
@if (session('import_mapel_errors') && count(session('import_mapel_errors')) > 0)
    <div class="alert alert-warning alert-dismissible fade show">
        <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Beberapa baris gagal diimport:</strong>
        <ul class="mb-0 mt-2 small">
            @foreach (session('import_mapel_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('import_mapel_skipped') && count(session('import_mapel_skipped')) > 0)
    <div class="alert alert-info alert-dismissible fade show">
        <strong><i class="bi bi-info-circle-fill me-1"></i>Baris dilewati (kode duplikat):</strong>
        <div class="mt-2 small">
            @foreach (session('import_mapel_skipped') as $row)
                <span class="badge bg-secondary me-1">{{ $row['kode'] }} – {{ $row['nama'] }}</span>
            @endforeach
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.mata-pelajaran.index') }}"
            class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="q" value="{{ $q }}"
                    class="form-control form-control-sm"
                    placeholder="Cari kode atau nama mapel…">
            </div>
            <div class="col-md-4">
                <select name="jurusan_id" class="form-select form-select-sm">
                    <option value="">Semua Jurusan</option>
                    <option value="umum" @selected($jurusanId === 'umum')>
                        Umum (semua jurusan)
                    </option>
                    @foreach ($jurusan as $j)
                        <option value="{{ $j->id }}" @selected($jurusanId == $j->id)>
                            {{ $j->nama }} ({{ $j->kode }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin.mata-pelajaran.index') }}"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width:50px">#</th>
                    <th>Kode</th>
                    <th>Nama Mata Pelajaran</th>
                    <th>Jenis</th>
                    <th>Jurusan</th>
                    <th style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mapel as $i => $m)
                    <tr>
                        <td class="ps-4 text-muted small">
                            {{ $mapel->firstItem() + $i }}
                        </td>
                        <td>
                            <span class="badge bg-secondary text-white bg-opacity-15 text-secondary font-monospace fw-semibold">
                                {{ $m->kode }}
                            </span>
                        </td>
                        <td class="fw-semibold">{{ $m->nama }}</td>
                        <td>
                            @if ($m->jurusan_id === null)
                                <span class="badge bg-primary bg-opacity-10 text-primary">Umum</span>
                            @else
                                <span class="badge" style="background-color:#e0e7ff;color:#4338ca">Produktif</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $m->jurusan?->nama ?? '— semua jurusan —' }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.mata-pelajaran.edit', $m) }}"
                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                    action="{{ route('admin.mata-pelajaran.destroy', $m) }}"
                                    onsubmit="return confirm('Hapus mata pelajaran {{ addslashes($m->nama) }}?')">
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
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-journal-bookmark fs-1 d-block mb-2 opacity-25"></i>
                            Belum ada mata pelajaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($mapel->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $mapel->firstItem() }}–{{ $mapel->lastItem() }}
                dari {{ $mapel->total() }} mapel
            </small>
            {{ $mapel->links() }}
        </div>
    @endif
</div>

@endsection