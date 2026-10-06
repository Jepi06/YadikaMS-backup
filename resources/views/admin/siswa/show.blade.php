@extends('admin.layouts.app')

@section('title', 'Detail Siswa')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Detail Siswa</h4>
        <small class="text-muted">{{ $siswa->nis }}</small>
    </div>
</div>

<div class="row g-4">
    {{-- ── Card utama ──────────────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                {{-- Header profil --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center
                                justify-content-center fw-bold fs-2"
                        style="width:72px;height:72px;flex-shrink:0">
                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">{{ $siswa->nama }}</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                {{ $siswa->kelas?->jurusan?->kode ?? '-' }}
                            </span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold">
                                {{ $siswa->kelas?->nama_kelas ?? '-' }}
                            </span>
                            <span class="badge {{ $siswa->jenis_kelamin === 'L' ? 'bg-info' : 'bg-danger' }} bg-opacity-10
                                            {{ $siswa->jenis_kelamin === 'L' ? 'text-info' : 'text-danger' }} fw-semibold">
                                {{ $siswa->jenis_kelamin_label }}
                            </span>
                        </div>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </div>
                </div>

                <hr>

                {{-- Data detail --}}
                <div class="row g-3">
                    @php
                        $fields = [
                            ['label' => 'NIS',              'value' => $siswa->nis,                  'mono' => true],
                            ['label' => 'Nama Lengkap',     'value' => $siswa->nama],
                            ['label' => 'Jenis Kelamin',    'value' => $siswa->jenis_kelamin_label],
                            ['label' => 'No. HP / WA',      'value' => $siswa->no_hp       ?? '—'],
                            ['label' => 'Kelas',            'value' => $siswa->kelas?->nama_kelas ?? '—'],
                            ['label' => 'Jurusan',          'value' => $siswa->kelas?->jurusan?->nama ?? '—'],
                            ['label' => 'Tingkat',          'value' => $siswa->kelas?->tingkat ?? '—'],
                            ['label' => 'Wali Kelas',       'value' => $siswa->kelas?->waliKelas?->name ?? '—'],
                            ['label' => 'Terdaftar sejak',  'value' => $siswa->created_at->isoFormat('D MMMM YYYY')],
                        ];
                    @endphp

                    @foreach ($fields as $f)
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">{{ $f['label'] }}</div>
                            <div class="fw-semibold {{ ($f['mono'] ?? false) ? 'font-monospace' : '' }}">
                                {{ $f['value'] }}
                            </div>
                        </div>
                    @endforeach

                    @if ($siswa->alamat)
                        <div class="col-12">
                            <div class="small text-muted mb-1">Alamat</div>
                            <div class="fw-semibold">{{ $siswa->alamat }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        {{-- Akun user (opsional) --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-person-badge me-1"></i>Akun Login</h6>
                @if ($siswa->user)
                    <dl class="row small mb-0">
                        <dt class="col-4 text-muted">Nama</dt>
                        <dd class="col-8">{{ $siswa->user->name }}</dd>
                        <dt class="col-4 text-muted">Email</dt>
                        <dd class="col-8 text-break">{{ $siswa->user->email }}</dd>
                        <dt class="col-4 text-muted">Status</dt>
                        <dd class="col-8">
                            @if ($siswa->user->is_active)
                                <span class="badge bg-success-subtle text-success">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                            @endif
                        </dd>
                    </dl>
                @else
                    <p class="text-muted small mb-2">Belum terhubung ke akun login.</p>
                    <small class="text-muted">
                        Siswa bisa ditautkan ke akun User untuk bisa login ke LMS.
                    </small>
                @endif
            </div>
        </div>

        {{-- Hapus --}}
        <div class="card border-0 bg-danger bg-opacity-10">
            <div class="card-body p-4">
                <h6 class="fw-bold text-danger mb-3">
                    <i class="bi bi-trash me-1"></i>Hapus Siswa
                </h6>
                <p class="small text-muted mb-3">Tindakan ini tidak bisa dibatalkan.</p>
                <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}"
                    onsubmit="return confirm('Yakin hapus {{ addslashes($siswa->nama) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i>Hapus Siswa Ini
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
