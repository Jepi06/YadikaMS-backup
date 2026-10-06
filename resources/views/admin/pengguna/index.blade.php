@extends('admin.layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Kelola Pengguna</h4>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Cari nama/email...">
            <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role PKL</th>
                        <th>Role SPMB</th>
                        <th>Role LMS</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengguna as $u)
                        <tr>
                            <td>
                                {{ $u->name }}
                                @if ($u->is_super_admin)
                                    <span class="badge bg-dark ms-1">Super Admin</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $u->email }}</td>
                            <td>
                                @forelse ($u->roles->where('module.kode', 'pkl') as $r)
                                    <span class="badge bg-primary-subtle text-primary badge-module">{{ $r->nama }}</span>
                                @empty
                                    <span class="text-muted small">-</span>
                                @endforelse
                            </td>
                            <td>
                                @forelse ($u->roles->where('module.kode', 'spmb') as $r)
                                    <span class="badge bg-info-subtle text-info-emphasis badge-module">{{ $r->nama }}</span>
                                @empty
                                    <span class="text-muted small">-</span>
                                @endforelse
                            </td>
                            <td>
                                @forelse ($u->roles->where('module.kode', 'lms') as $r)
                                    <span class="badge bg-success-subtle text-success-emphasis badge-module">{{ $r->nama }}</span>
                                @empty
                                    <span class="text-muted small">-</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.pengguna.akses', $u) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-sliders"></i> Kelola Akses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengguna ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pengguna->hasPages())
            <div class="p-3 border-top">{{ $pengguna->links() }}</div>
        @endif
    </div>
@endsection
