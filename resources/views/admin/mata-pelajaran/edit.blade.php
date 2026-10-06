@extends('admin.layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Edit Mata Pelajaran</h4>
        <small class="text-muted">{{ $mataPelajaran->kode }} — {{ $mataPelajaran->nama }}</small>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST"
                    action="{{ route('admin.mata-pelajaran.update', $mataPelajaran) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Kode <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode"
                            class="form-control @error('kode') is-invalid @enderror"
                            value="{{ old('kode', $mataPelajaran->kode) }}"
                            placeholder="Contoh: MTK">
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Mata Pelajaran <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $mataPelajaran->nama) }}"
                            placeholder="Contoh: Matematika">
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
                                    @selected(old('jurusan_id', $mataPelajaran->jurusan_id) == $j->id)>
                                    {{ $j->nama }} ({{ $j->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('jurusan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
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
        <div class="card border-0 bg-danger bg-opacity-10">
            <div class="card-body p-4">
                <h6 class="fw-bold text-danger mb-3">
                    <i class="bi bi-trash me-1"></i>Hapus Mata Pelajaran
                </h6>
                <p class="small text-muted mb-3">
                    Tidak bisa dihapus jika masih digunakan di penugasan mengajar.
                </p>
                <form method="POST"
                    action="{{ route('admin.mata-pelajaran.destroy', $mataPelajaran) }}"
                    onsubmit="return confirm('Hapus {{ addslashes($mataPelajaran->nama) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection