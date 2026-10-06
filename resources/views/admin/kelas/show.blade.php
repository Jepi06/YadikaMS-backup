@extends('admin.layouts.app')

@section('title', 'Detail Kelas')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $kelas->nama_kelas }}</h4>
            <p class="text-muted mb-0">
                {{ $kelas->tingkat }} · {{ $kelas->jurusan->nama ?? '-' }} ·
                Wali Kelas: {{ $kelas->waliKelas->name ?? '-' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.kelas.edit', $kelas) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-people-fill me-1"></i> Daftar Siswa
                    <span class="badge bg-secondary ms-1">{{ $kelas->siswa->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama Siswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelas->siswa as $i => $s)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $s->nama }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-journal-bookmark-fill me-1"></i> Mata Pelajaran di Kelas Ini
                    <span class="badge bg-secondary ms-1">{{ $kelas->pengampuMapel->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelas->pengampuMapel as $p)
                                <tr>
                                    <td>{{ $p->mataPelajaran->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center text-muted py-4">Belum ada mata pelajaran diampu di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection