<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Formulir Pendaftaran Siswa Baru</h3>
                    <p class="text-muted">Isi data dengan lengkap dan benar. Setelah submit, kamu akan mendapat nomor
                        pendaftaran untuk cek status.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm p-4">
                    <form method="POST" action="{{ route('spmb.pengajuan.store') }}">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control"
                                    value="{{ old('nama_lengkap') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                                    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Agama</label>
                                <select name="agama" class="form-select" required>
                                    @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" @selected(old('agama') === $agama)>
                                            {{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jurusan Pilihan</label>
                                <select name="jurusan_id" class="form-select" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($jurusanList as $j)
                                        <option value="{{ $j->id }}" @selected((int) old('jurusan_id') === $j->id)>
                                            {{ $j->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Asal Sekolah</label>
                                <input type="text" name="asal_sekolah" class="form-control"
                                    value="{{ old('asal_sekolah') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Orang Tua</label>
                                <input type="text" name="nama_orang_tua" class="form-control"
                                    value="{{ old('nama_orang_tua') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP (aktif WhatsApp)</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}"
                                    required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            Kirim Pendaftaran
                        </button>
                    </form>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('spmb.public.index') }}" class="small text-decoration-none">&larr; Kembali</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
