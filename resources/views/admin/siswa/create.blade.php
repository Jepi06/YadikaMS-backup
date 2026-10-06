{{--
    Partial form: dipakai oleh create.blade.php dan edit.blade.php.
    Variabel yang harus ada di context:
        $siswa (Siswa|null) - null untuk create
        $kelas (Collection)
        $action (string)    - URL form action
        $method (string)    - PUT untuk edit, POST untuk create
--}}

@extends('admin.layouts.app')

@section('title', isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">{{ isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa' }}</h4>
        <small class="text-muted">
            {{ isset($siswa) ? "Edit data {$siswa->nama}" : 'Tambah siswa baru satu per satu' }}
        </small>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
           <form method="POST" action="{{ route('admin.siswa.store') }}">
                    @csrf
                    @if (isset($siswa)) @method('PUT') @endif

                    {{-- ── Identitas ─────────────────────────────────── --}}
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Identitas Siswa</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror"
                                value="{{ old('nis', $siswa->nis ?? '') }}"
                                placeholder="Nomor Induk Siswa">
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $siswa->nama ?? '') }}"
                                placeholder="Nama lengkap siswa">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">Pilih…</option>
                                @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $val => $label)
                                    <option value="{{ $val }}"
                                        @selected(old('jenis_kelamin', $siswa->jenis_kelamin ?? '') === $val)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. HP / WA</label>
                            <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp', $siswa->no_hp ?? '') }}"
                                placeholder="08xxxxxxxxxx">
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                rows="2" placeholder="Alamat tempat tinggal siswa">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Kelas ────────────────────────────────────────── --}}
                    <hr class="my-3">
                    <h6 class="fw-bold text-muted text-uppercase small mb-3">Penempatan Kelas</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror">
                                <option value="">Pilih kelas…</option>
                                @php $grouped = $kelas->groupBy('tingkat'); @endphp
                                @foreach (['X', 'XI', 'XII'] as $tingkat)
                                    @if ($grouped->has($tingkat))
                                        <optgroup label="Tingkat {{ $tingkat }}">
                                            @foreach ($grouped[$tingkat] as $k)
                                                <option value="{{ $k->id }}"
                                                    @selected(old('kelas_id', $siswa->kelas_id ?? '') == $k->id)>
                                                    {{ $k->nama_kelas }}
                                                    @if ($k->jurusan) ({{ $k->jurusan->kode }}) @endif
                                                    — Wali: {{ $k->waliKelas?->name ?? 'Belum ada' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Submit ───────────────────────────────────────── --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>
                            {{ isset($siswa) ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                        </button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar tip --}}
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb-fill text-warning me-1"></i>Tips</h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li class="mb-2"><strong>NIS</strong> harus unik — tidak boleh sama antar siswa.</li>
                    <li class="mb-2">Pilihan kelas sudah mencakup info <strong>jurusan</strong> dan <strong>wali kelas</strong>.</li>
                    <li class="mb-2">Untuk <strong>menambah banyak siswa</strong> sekaligus, gunakan fitur
                        <a href="{{ route('admin.siswa.import.form') }}">Import Excel</a>.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
