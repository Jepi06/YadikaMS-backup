<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold small">NIS <span class="text-danger">*</span></label>
        <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror"
            value="{{ old('nis', $siswa?->nis) }}" required placeholder="Contoh: 2024001">
        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
            value="{{ old('nama', $siswa?->nama) }}" required>
        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
        <select name="jenis_kelamin" class="form-select" required>
            <option value="L" {{ old('jenis_kelamin', $siswa?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin', $siswa?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Kelas <span class="text-danger">*</span></label>
        <select name="kelas_id" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}" {{ old('kelas_id', $siswa?->kelas_id) == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">No. HP</label>
        <input type="text" name="no_hp" class="form-control"
            value="{{ old('no_hp', $siswa?->no_hp) }}" placeholder="08xxxxxxxxxx">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $siswa?->alamat) }}</textarea>
    </div>
</div>
