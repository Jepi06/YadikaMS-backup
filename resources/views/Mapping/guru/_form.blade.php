<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold small">NIP</label>
        <input type="text" name="nip" class="form-control" value="{{ old('nip', $guru?->nip) }}" placeholder="Opsional">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru?->nama) }}" required>
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">No. HP</label>
        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $guru?->no_hp) }}">
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $guru?->email) }}">
    </div>
</div>
