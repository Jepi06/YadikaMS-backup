<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm text-center p-5">
                    <div class="mb-3">
                        <span class="fs-1">✅</span>
                    </div>
                    <h4 class="fw-bold mb-2">Pendaftaran Berhasil!</h4>
                    <p class="text-muted mb-4">
                        Simpan nomor pendaftaran di bawah ini baik-baik.
                        Nomor ini dipakai untuk cek status kelulusan nanti.
                    </p>

                    <div class="border rounded p-3 mb-4 bg-light">
                        <small class="text-muted d-block">Nomor Pendaftaran</small>
                        <span class="fs-3 fw-bold text-primary">{{ $pendaftar->no_pendaftaran }}</span>
                    </div>

                    <dl class="row text-start small mb-4">
                        <dt class="col-5">Nama</dt>
                        <dd class="col-7">{{ $pendaftar->nama_lengkap }}</dd>
                        <dt class="col-5">Jurusan</dt>
                        <dd class="col-7">{{ $pendaftar->jurusan->nama ?? '-' }}</dd>
                        <dt class="col-5">Status</dt>
                        <dd class="col-7"><span class="badge bg-warning text-dark">Menunggu Verifikasi</span></dd>
                    </dl>

                    <a href="{{ route('spmb.public.index') }}?cari={{ $pendaftar->no_pendaftaran }}"
                        class="btn btn-primary">
                        Cek Status Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
