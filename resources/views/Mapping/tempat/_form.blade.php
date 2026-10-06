<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold small">Nama Tempat PKL <span class="text-danger">*</span></label>
        <input type="text" name="nama_tempat" class="form-control" value="{{ old('nama_tempat', $tempat?->nama_tempat) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">Alamat <span class="text-danger">*</span></label>
        <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $tempat?->alamat) }}</textarea>
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Bidang Usaha</label>
        <input type="text" name="bidang_usaha" class="form-control" value="{{ old('bidang_usaha', $tempat?->bidang_usaha) }}">
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Nama Kontak</label>
        <input type="text" name="nama_kontak" class="form-control" value="{{ old('nama_kontak', $tempat?->nama_kontak) }}">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">No. Telepon</label>
        <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp', $tempat?->no_telp) }}">
    </div>
</div>
