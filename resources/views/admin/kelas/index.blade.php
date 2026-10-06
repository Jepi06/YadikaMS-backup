@extends('admin.layouts.app')

@section('title', 'Kelola Kelas')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Kelola Kelas</h4>
        <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">Cari nama kelas</label>
                    <input type="text" name="cari" value="{{ request('cari') }}"
                        class="form-control form-control-sm" placeholder="mis. XI RPL 1">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">Jurusan</label>
                    <select name="jurusan_id" class="form-select form-select-sm">
                        <option value="">Semua Jurusan</option>
                        @foreach ($jurusanList as $j)
                            <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>
                                {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">Tingkat</label>
                    <select name="tingkat" class="form-select form-select-sm">
                        <option value="">Semua Tingkat</option>
                        @foreach (['X', 'XI', 'XII'] as $t)
                            <option value="{{ $t }}" @selected(request('tingkat') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    @if (request()->anyFilled(['cari', 'jurusan_id', 'tingkat']))
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th class="text-center">Jml Siswa</th>
                        <th class="text-end" style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $k)
                        <tr>
                            <td class="fw-semibold">{{ $k->nama_kelas }}</td>
                            <td>{{ $k->tingkat }}</td>
                            <td>{{ $k->jurusan->nama ?? '-' }}</td>
                            <td>{{ $k->waliKelas->name ?? '-' }}</td>
                            <td class="text-center">{{ $k->siswa_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.kelas.edit', $k) }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus kelas {{ $k->nama_kelas }}? Tindakan ini tidak bisa dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($kelas->hasPages())
            <div class="card-footer bg-white">
                {{ $kelas->links() }}
            </div>
        @endif
    </div>
@endsection