@extends('admin.layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Tambah Mata Pelajaran</h4>
        <small class="text-muted">Tambah mapel umum atau produktif per jurusan</small>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.mata-pelajaran.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Kode <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode"
                            class="form-control @error('kode') is-invalid @enderror"
                            value="{{ old('kode') }}"
                            placeholder="Contoh: MTK, PPLG-PW">
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Kode unik, maks 20 karakter.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Mata Pelajaran <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama') }}"
                            placeholder="Contoh: Pemrograman Web">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jurusan</label>
                        <select name="jurusan_id"
                            class="form-select @error('jurusan_id') is-invalid @enderror">
                            <option value="">— Umum (semua jurusan) —</option>
                            @foreach ($jurusan as $j)
                                <option value="{{ $j->id }}"
                                    @selected(old('jurusan_id') == $j->id)>
                                    {{ $j->nama }} ({{ $j->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('jurusan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Kosongkan jika mapel berlaku untuk semua jurusan (umum).
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                        <a href="{{ route('admin.mata-pelajaran.index') }}"
                            class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i>Panduan
                </h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-2">
                        <strong>Mapel Umum</strong> — berlaku untuk semua jurusan.
                        Contoh: Matematika, Bahasa Indonesia. Biarkan jurusan kosong.
                    </li>
                    <li class="mb-2">
                        <strong>Mapel Produktif</strong> — khusus satu jurusan.
                        Contoh: Pemrograman Web hanya untuk PPLG.
                        Pilih jurusan yang sesuai.
                    </li>
                    <li class="mb-2">
                        Kode harus unik dan singkat — dipakai sebagai referensi
                        di penugasan mengajar.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
