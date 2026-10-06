@csrf

<div class="mb-3">
    <label class="form-label">Nama Kelas</label>
    <input type="text" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas ?? '') }}"
        class="form-control @error('nama_kelas') is-invalid @enderror" placeholder="mis. XI RPL 1" required>
    @error('nama_kelas')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Tingkat</label>
        <select name="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
            <option value="">-- Pilih Tingkat --</option>
            @foreach (['X', 'XI', 'XII'] as $t)
                <option value="{{ $t }}" @selected(old('tingkat', $kelas->tingkat ?? '') === $t)>{{ $t }}</option>
            @endforeach
        </select>
        @error('tingkat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Jurusan</label>
        <select name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
            <option value="">-- Pilih Jurusan --</option>
            @foreach ($jurusanList as $j)
                <option value="{{ $j->id }}" @selected(old('jurusan_id', $kelas->jurusan_id ?? '') == $j->id)>
                    {{ $j->nama }}
                </option>
            @endforeach
        </select>
        @error('jurusan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3 mt-3">
    <label class="form-label">Wali Kelas <span class="text-muted small">(opsional)</span></label>
    <select name="wali_kelas_id" class="form-select @error('wali_kelas_id') is-invalid @enderror">
        <option value="">-- Belum Ada Wali Kelas --</option>
        @foreach ($guruList as $g)
            <option value="{{ $g->id }}" @selected(old('wali_kelas_id', $kelas->wali_kelas_id ?? '') == $g->id)>
                {{ $g->name }}
            </option>
        @endforeach
    </select>
    @error('wali_kelas_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> Simpan
    </button>
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>