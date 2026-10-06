{{-- resources/views/spmb/profile.blade.php --}}
{{-- PENTING: ganti @extends di bawah dengan layout yang SAMA seperti di file profile.blade.php SPMB kamu sebelumnya --}}
@extends('spmb.layouts.admin')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@php($spmbUser = auth('spmb')->user())

@section('content')
    <div class="row g-6">
        {{-- Informasi Akun --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-person-circle me-1"></i> Informasi Akun</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                    @endif

                    @if ($errors->hasAny(['name', 'avatar']))
                        <div class="alert alert-danger py-2 small">
                            {{ $errors->first('name') ?: $errors->first('avatar') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <x-profil-avatar :user="$spmbUser" :uploadRoute="route('spmb.admin.spmb.profil.avatar')" :deleteRoute="route('spmb.admin.spmb.profil.avatar.delete')" />
                    </div>

                    <form method="POST" action="{{ route('spmb.admin.profil.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small">Nama</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $spmbUser->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Email</label>
                            <input type="email" class="form-control" value="{{ $spmbUser->email }}" disabled>
                            <div class="form-text">Email dipakai untuk login, hubungi admin kalau perlu diubah.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-shield-lock me-1"></i> Ganti Password</div>
                <div class="card-body">
                    @if ($errors->hasAny(['current_password', 'password']))
                        <div class="alert alert-danger py-2 small">
                            {{ $errors->first('current_password') ?: $errors->first('password') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('spmb.admin.profil.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small">Password Lama</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Password Baru</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                            <div class="form-text">Minimal 8 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required
                                minlength="8">
                        </div>

                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-key me-1"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0 small">
        <i class="bi bi-info-circle me-1"></i>
        Akun ini dipakai bersama di sistem PKL, SPMB, dan LMS — perubahan nama/password
        di sini otomatis berlaku juga saat login ke modul lain.
    </div>
@endsection
