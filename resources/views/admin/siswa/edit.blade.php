{{--
    Edit siswa — view ini identik dengan create, bedanya:
    - $action   = route edit
    - $siswa    = model yang di-edit (di-inject old value)
    - Heading berubah jadi "Edit Siswa"
--}}

@extends('admin.layouts.app')

@section('title', 'Edit Siswa')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Edit Siswa</h4>
        <small class="text-muted">Edit data {{ $siswa->nama }}</small>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}">
                    @csrf
                    @method('PUT')

                    {{-- ── Identitas ─────────────────────────────────── --}}
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Identitas Siswa</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis"
                                class="form-control @error('nis') is-invalid @enderror"
                                value="{{ old('nis', $siswa->nis) }}"
                                placeholder="Nomor Induk Siswa">
                            @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama"
                                class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $siswa->nama) }}"
                                placeholder="Nama lengkap siswa">
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin"
                                class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih…</option>
                                @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $val => $label)
                                    <option value="{{ $val }}"
                                        @selected(old('jenis_kelamin', $siswa->jenis_kelamin) === $val)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. HP / WA</label>
                            <input type="text" name="no_hp"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp', $siswa->no_hp) }}"
                                placeholder="08xxxxxxxxxx">
                            @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat"
                                class="form-control @error('alamat') is-invalid @enderror"
                                rows="2"
                                placeholder="Alamat tempat tinggal siswa">{{ old('alamat', $siswa->alamat) }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- ── Kelas ──────────────────────────────────────── --}}
                    <hr class="my-3">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Penempatan Kelas</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id"
                                class="form-select @error('kelas_id') is-invalid @enderror">
                                <option value="">Pilih kelas…</option>
                                @php $grouped = $kelas->groupBy('tingkat'); @endphp
                                @foreach (['X', 'XI', 'XII'] as $tingkat)
                                    @if ($grouped->has($tingkat))
                                        <optgroup label="Tingkat {{ $tingkat }}">
                                            @foreach ($grouped[$tingkat] as $k)
                                                <option value="{{ $k->id }}"
                                                    @selected(old('kelas_id', $siswa->kelas_id) == $k->id)>
                                                    {{ $k->nama_kelas }}
                                                    @if ($k->jurusan) ({{ $k->jurusan->kode }}) @endif
                                                    — Wali: {{ $k->waliKelas?->name ?? 'Belum ada' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            @error('kelas_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- ── Submit ─────────────────────────────────────── --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.siswa.show', $siswa) }}"
                            class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar: info ringkasan --}}
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 bg-light mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Info Siswa</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">NIS</dt>
                    <dd class="col-7 font-monospace">{{ $siswa->nis }}</dd>
                    <dt class="col-5 text-muted">Kelas saat ini</dt>
                    <dd class="col-7">{{ $siswa->kelas->nama_kelas ?? '-' }}</dd>
                    <dt class="col-5 text-muted">Wali Kelas</dt>
                    <dd class="col-7">{{ $siswa->kelas?->waliKelas?->name ?? '-' }}</dd>
                    <dt class="col-5 text-muted">Terdaftar</dt>
                    <dd class="col-7">{{ $siswa->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card border-danger border-0 bg-danger bg-opacity-10">
            <div class="card-body p-4">
                <h6 class="fw-bold text-danger mb-3"><i class="bi bi-trash me-1"></i>Hapus Siswa</h6>
                <p class="small text-muted mb-3">
                    Menghapus siswa akan menghilangkan semua data terkait. Tindakan ini tidak bisa dibatalkan.
                </p>
                <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}"
                    onsubmit="return confirm('Yakin hapus siswa {{ addslashes($siswa->nama) }}? Tindakan ini permanen.')">
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
