@extends('Mapping.layouts.app')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('content')
    <div class="card">
        <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-people me-2 text-primary"></i>Daftar Siswa</span>
            <div class="d-flex align-items-center gap-2">
                {{-- BARU: badge ringkas total siswa & yang belum mengajukan,
                 datanya sudah dikirim SiswaController tapi sebelumnya belum ditampilkan --}}
                <span class="badge bg-secondary-subtle text-secondary">Total: {{ $totalSiswa }}</span>
                <a href="{{ route('siswa.belum-mengajukan') }}"
                    class="badge bg-danger-subtle text-danger text-decoration-none">
                    Belum Mengajukan: {{ $totalBelumMengajukan }}
                </a>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Siswa
                </button>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card-body border-bottom py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-sm-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari nama / NIS..." value="{{ request('search') }}">
                </div>
                <div class="col-sm-3">
                    <select name="kelas_id" class="form-select form-select-sm">
                        <option value="">-- Semua Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- BARU: filter status_pengajuan, sudah didukung SiswaController::index() --}}
                <div class="col-sm-3">
                    <select name="status_pengajuan" class="form-select form-select-sm">
                        <option value="">-- Semua Status Pengajuan --</option>
                        <option value="sudah" {{ request('status_pengajuan') === 'sudah' ? 'selected' : '' }}>Sudah
                            Mengajukan</option>
                        <option value="belum" {{ request('status_pengajuan') === 'belum' ? 'selected' : '' }}>Belum
                            Mengajukan</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i
                            class="bi bi-search me-1"></i>Cari</button>
                    <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>JK</th>
                            <th>No. HP</th>
                            <th>Status PKL</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $i => $s)
                            <tr>
                                <td>{{ $siswa->firstItem() + $i }}</td>
                                <td><span class="font-monospace">{{ $s->nis }}</span></td>
                                <td class="fw-semibold">{{ $s->nama }}</td>
                                <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                <td>{{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td>{{ $s->no_hp ?? '-' }}</td>
                                <td>
                                    @if ($s->penempatanAktif)
                                        <span class="badge bg-success-subtle text-success">Sudah Ditempatkan</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Belum</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $s->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#modalHapus{{ $s->id }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal Edit --}}
                            <div class="modal fade" id="modalEdit{{ $s->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('siswa.update', $s) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-semibold">Edit Data Siswa</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                @include('Mapping.siswa._form', [
                                                    'siswa' => $s,
                                                    'kelas' => $kelas,
                                                ])
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Simpan
                                                    Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal Hapus --}}
                            <div class="modal fade" id="modalHapus{{ $s->id }}" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <form action="{{ route('siswa.destroy', $s) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header border-0">
                                                <h6 class="modal-title fw-semibold text-danger"><i
                                                        class="bi bi-exclamation-triangle me-2"></i>Hapus Siswa</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body pt-0">
                                                Yakin ingin menghapus <strong>{{ $s->nama }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">Tidak ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($siswa->hasPages())
            <div class="card-footer">{{ $siswa->links() }}</div>
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title fw-semibold">Tambah Siswa Baru</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('Mapping.siswa._form', ['siswa' => null, 'kelas' => $kelas])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
