@php($old = fn($field, $default = '') => old($field, $pendaftar->{$field} ?? $default))

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="{{ $old('nama_lengkap') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-select" required>
            <option value="L" @selected($old('jenis_kelamin') === 'L')>Laki-laki</option>
            <option value="P" @selected($old('jenis_kelamin') === 'P')>Perempuan</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Agama</label>
        <select name="agama" class="form-select" required>
            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                <option value="{{ $agama }}" @selected($old('agama') === $agama)>{{ $agama }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Jurusan</label>
        <select name="jurusan_id" class="form-select" required>
            <option value="">-- Pilih Jurusan --</option>
            @foreach($jurusanList as $j)
                <option value="{{ $j->id }}" @selected((int) $old('jurusan_id') === $j->id)>{{ $j->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Asal Sekolah</label>
        <input type="text" name="asal_sekolah" class="form-control" value="{{ $old('asal_sekolah') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Nama Orang Tua</label>
        <input type="text" name="nama_orang_tua" class="form-control" value="{{ $old('nama_orang_tua') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">No. HP</label>
        <input type="text" name="no_hp" class="form-control" value="{{ $old('no_hp') }}">
    </div>

    <div class="col-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2">{{ $old('alamat') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Catatan Admin</label>
        <textarea name="catatan_admin" class="form-control" rows="2">{{ $old('catatan_admin') }}</textarea>
    </div>
</div>
