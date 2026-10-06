@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h4 class="fw-bold mb-1">Halo, {{ $superAdmin->name }} 👋</h4>
    <p class="text-muted mb-4">Panel ini ngatur akses lintas modul — bisa kamu buka dari login PKL, SPMB, atau LMS manapun.</p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people-fill text-primary fs-3"></i>
                    <h3 class="fw-bold mt-2 mb-0">{{ $stats['total_pengguna'] }}</h3>
                    <small class="text-muted">Total Pengguna</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-person-video3 text-success fs-3"></i>
                    <h3 class="fw-bold mt-2 mb-0">{{ $stats['total_guru_lms'] }}</h3>
                    <small class="text-muted">Guru LMS</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-shield-lock-fill text-danger fs-3"></i>
                    <h3 class="fw-bold mt-2 mb-0">{{ $stats['total_super_admin'] }}</h3>
                    <small class="text-muted">Super Admin</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Modul Tersedia</div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Modul</th>
                        <th>Kode</th>
                        <th>Jumlah Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($modules as $m)
                        <tr>
                            <td>{{ $m->nama }}</td>
                            <td><code>{{ $m->kode }}</code></td>
                            <td>{{ $m->roles_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-dark">
            <i class="bi bi-people-fill me-1"></i> Kelola Akses Pengguna
        </a>
    </div>
@endsection
