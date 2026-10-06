@extends('Mapping.layouts.app')
@section('title', 'Guru Pembimbing')
@section('page-title', 'Data Guru Pembimbing')

@section('content')
<div class="card">
    <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="bi bi-person-badge me-2 text-primary"></i>Daftar Guru Pembimbing</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
            <i class="bi bi-plus-lg me-1"></i>Tambah Guru
        </button>
    </div>
    <div class="card-body border-bottom py-2">
        <form method="GET" class="row g-2">
            <div class="col-sm-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / NIP..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search me-1"></i>Cari</button>
                <a href="{{ route('guru.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>NIP</th><th>Nama</th><th>No. HP</th><th>Email</th><th>Jml Siswa</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guru as $i => $g)
                    <tr>
                        <td>{{ $guru->firstItem() + $i }}</td>
                        <td class="font-monospace small">{{ $g->nip ?? '-' }}</td>
                        <td class="fw-semibold">{{ $g->nama }}</td>
                        <td>{{ $g->no_hp ?? '-' }}</td>
                        <td>{{ $g->email ?? '-' }}</td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $g->penempatan_pkl_count }} siswa</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditGuru{{ $g->id }}"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapusGuru{{ $g->id }}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditGuru{{ $g->id }}" tabindex="-1">
                        <div class="modal-dialog"><form action="{{ route('guru.update', $g) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header"><h6 class="modal-title fw-semibold">Edit Guru Pembimbing</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">@include('Mapping.guru._form', ['guru' => $g])</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </form></div>
                    </div>

                    <div class="modal fade" id="modalHapusGuru{{ $g->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm"><form action="{{ route('guru.destroy', $g) }}" method="POST">
                            @csrf @method('DELETE')
                            <div class="modal-content">
                                <div class="modal-header border-0"><h6 class="modal-title fw-semibold text-danger">Hapus Guru</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body pt-0">Hapus <strong>{{ $g->nama }}</strong>?</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </div>
                            </div>
                        </form></div>
                    </div>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">Belum ada data guru pembimbing.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($guru->hasPages())<div class="card-footer">{{ $guru->links() }}</div>@endif
</div>

<div class="modal fade" id="modalTambahGuru" tabindex="-1">
    <div class="modal-dialog"><form action="{{ route('guru.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title fw-semibold">Tambah Guru Pembimbing</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('Mapping.guru._form', ['guru' => null])</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </div>
    </form></div>
</div>
@endsection
