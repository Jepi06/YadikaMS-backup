@extends('admin.layouts.app')

@section('title', 'Kelola Akses - ' . $user->name)

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Kelola Akses — {{ $user->name }}</h4>
            <p class="text-muted mb-0">{{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.pengguna.akses.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            @foreach ($modules as $m)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-semibold">
                            {{ $m->nama }} <code class="small text-muted">{{ $m->kode }}</code>
                        </div>
                        <div class="card-body">
                            @forelse ($m->roles as $role)
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                           class="form-check-input" id="role{{ $role->id }}"
                                           @checked(in_array($role->id, $roleIdsUser))>
                                    <label for="role{{ $role->id }}" class="form-check-label">
                                        {{ $role->nama }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada role di modul ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" name="is_super_admin" value="1" class="form-check-input" id="isSuperAdmin"
                           @checked($user->is_super_admin) @disabled($user->id === request()->attributes->get('superAdminUser')->id)>
                    <label for="isSuperAdmin" class="form-check-label fw-semibold">
                        <i class="bi bi-shield-lock-fill text-danger me-1"></i> Super Admin
                        <div class="small text-muted fw-normal">
                            Bisa buka Panel Super Admin ini dari login PKL/SPMB/LMS manapun. Berikan hati-hati.
                        </div>
                    </label>
                </div>
                @if ($user->id === request()->attributes->get('superAdminUser')->id)
                    <p class="small text-muted mt-2 mb-0">
                        <i class="bi bi-info-circle"></i> Kamu gak bisa cabut status Super Admin akun sendiri lewat sini (biar gak ke-lock out).
                    </p>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-dark mt-3">
            <i class="bi bi-save me-1"></i> Simpan Akses
        </button>
    </form>
@endsection
