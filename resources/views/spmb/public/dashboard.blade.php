<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status SPMB - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(135deg, #0d2b4e, #12406f);
            color: #fff;
            padding: 3rem 0;
        }
    </style>
</head>

<body class="bg-light">

    <!-- =========================
        NAVBAR
    ========================== -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

        <div class="container">

            <a class="navbar-brand fw-bold" href="#">
                🎓 SPMB SMK Yadika Soreang
            </a>

            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbar">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">

                        <a href="{{ route('spmb.pengajuan.create') }}" class="btn btn-warning me-2">

                            Daftar

                        </a>

                    </li>

                    <li class="nav-item">

                        <a href="{{ route('spmb.login') }}" class="btn btn-outline-light">

                            Login Admin

                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </nav>

    <!-- =========================
        CONTENT
    ========================== -->

    <div class="container py-5">

        <div class="text-center mb-4">

            <h2 class="fw-bold">

                Cek Status Pendaftaran

            </h2>

            <p class="text-muted">

                Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}

            </p>

        </div>

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <!-- FORM -->

                <div class="card shadow-sm border-0 mb-4">

                    <div class="card-body">

                        <form method="GET">

                            <div class="input-group">

                                <input type="text" class="form-control" name="cari" value="{{ $keyword }}"
                                    placeholder="Masukkan Nomor Pendaftaran atau Nama">

                                <button class="btn btn-primary">

                                    Cari

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- HASIL -->

                @if ($keyword != '')

                    <div class="card shadow-sm border-0 mb-4">

                        <div class="card-body">

                            @forelse($hasil as $p)
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">

                                    <div>

                                        <h6 class="mb-1">

                                            {{ $p->nama_lengkap }}

                                        </h6>

                                        <small class="text-muted">

                                            {{ $p->no_pendaftaran }}

                                            •

                                            {{ optional($p->jurusan)->nama }}

                                        </small>

                                    </div>

                                    <div>

                                        @if ($p->status == 'diterima')
                                            <span class="badge bg-success">

                                                Diterima

                                            </span>
                                        @elseif($p->status == 'ditolak')
                                            <span class="badge bg-danger">

                                                Tidak Diterima

                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">

                                                Menunggu

                                            </span>
                                        @endif

                                    </div>

                                </div>

                            @empty

                                <div class="text-center py-4 text-muted">

                                    Data tidak ditemukan.

                                </div>
                            @endforelse

                        </div>

                    </div>

                @endif


            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>
