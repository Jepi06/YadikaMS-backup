@extends('admin.layouts.app')

@section('title', 'Profil Saya')

@section('content')

<h4 class="fw-bold mb-4">Profil Saya</h4>

@if ($errors->has('password'))
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ $errors->first('password') }}
    </div>
@endif

<div class="row g-4">

    {{-- Foto Profil --}}
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm text-center p-4">
            {{-- Avatar --}}
            <div class="mx-auto mb-3">
                @if ($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}"
                        alt="Avatar"
                        class="rounded-circle object-fit-cover"
                        style="width:100px;height:100px">
                @else
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto text-white fw-black fs-2"
                        style="width:100px;height:100px">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="fw-bold">{{ $user->name }}</div>
            <div class="text-muted small mb-4">Super Admin</div>

            {{-- Upload avatar --}}
            <form method="POST" action="{{ route('admin.profil.avatar') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label for="avatarInput" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-camera me-1"></i>Ganti Foto
                    </label>
                    <input type="file" id="avatarInput" name="avatar"
                        accept="image/*" class="d-none"
                        onchange="this.form.submit()">
                </div>
                @error('avatar')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </form>

            {{-- Hapus avatar --}}
            @if ($user->avatar)
                <form method="POST" action="{{ route('admin.profil.avatar.delete') }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                        onclick="return confirm('Hapus foto profil?')">
                        <i class="bi bi-trash me-1"></i>Hapus Foto
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Informasi Akun --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-circle me-1"></i> Informasi Akun
            </div>
            <div class="card-body p-4">
                @if (session('status'))
                    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                @endif
                @error('name')
                    <div class="alert alert-danger py-2 small">{{ $message }}</div>
                @enderror

                <form method="POST" action="{{ route('admin.profil.update') }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control"
                            value="{{ $user->email }}" disabled>
                        <div class="form-text">Hubungi developer untuk ubah email.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Role</label>
                        <input type="text" class="form-control"
                            value="Super Admin" disabled>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-shield-lock me-1"></i> Ganti Password
            </div>
            <div class="card-body p-4">
                @if ($errors->hasAny(['current_password', 'password']))
                    <div class="alert alert-danger py-2 small">
                        {{ $errors->first('current_password') ?: $errors->first('password') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profil.password') }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password Lama</label>
                        <input type="password" name="current_password"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password Baru</label>
                        <input type="password" name="password"
                            class="form-control" required minlength="8">
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                            class="form-control" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-key me-1"></i>Ubah Password
                    </button>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-3 small">
            <i class="bi bi-info-circle me-1"></i>
            Akun ini dipakai bersama di PKL, SPMB, dan LMS — perubahan nama/password
            berlaku di semua modul.
        </div>
    </div>
</div>

@endsection