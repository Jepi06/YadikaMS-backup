<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold small">Tempat PKL <span class="text-danger">*</span></label>
        <select name="tempat_pkl_id" class="form-select" required>
            <option value="">-- Pilih --</option>
            @foreach($tempatPkl as $t)
                <option value="{{ $t->id }}" {{ old('tempat_pkl_id', $penempatan?->tempat_pkl_id) == $t->id ? 'selected' : '' }}>
                    {{ $t->nama_tempat }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">Guru Pembimbing <span class="text-danger">*</span></label>
        <select name="guru_pembimbing_id" class="form-select" required>
            <option value="">-- Pilih --</option>
            @foreach($guruPembimbing as $g)
                <option value="{{ $g->id }}" {{ old('guru_pembimbing_id', $penempatan?->guru_pembimbing_id) == $g->id ? 'selected' : '' }}>
                    {{ $g->nama }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Tahun Ajaran <span class="text-danger">*</span></label>
        <input type="text" name="tahun_ajaran" class="form-control"
            value="{{ old('tahun_ajaran', $penempatan?->tahun_ajaran) }}" placeholder="2026/2027" required>
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Tanggal Mulai <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_mulai" class="form-control"
            value="{{ old('tanggal_mulai', $penempatan?->tanggal_mulai?->format('Y-m-d')) }}" required>
    </div>
    <div class="col-6">
        <label class="form-label fw-semibold small">Tanggal Selesai <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_selesai" class="form-control"
            value="{{ old('tanggal_selesai', $penempatan?->tanggal_selesai?->format('Y-m-d')) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold small">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $penempatan?->keterangan) }}</textarea>
    </div>
</div>
