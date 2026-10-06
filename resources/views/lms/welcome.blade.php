<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background:#f4f7fb; }
        .hero {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 55%, #1976d2 100%);
            color:#fff;
        }
        .hero .btn-light { font-weight:600; }
        .feature-icon {
            width:56px; height:56px; border-radius:14px;
            background:#e3f2fd; color:#0d47a1;
            display:flex; align-items:center; justify-content:center;
            font-size:1.5rem;
        }
        .navbar-brand { font-weight:700; color:#0d47a1 !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('lms') }}">
                <i class="bi bi-mortarboard-fill me-1"></i> LMS Yadika Soreang
            </a>
            <div class="ms-auto">
                <a href="{{ route('lms.login') }}" class="btn btn-primary px-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </a>
            </div>
        </div>
    </nav>

    <header class="hero py-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-white text-primary mb-3 px-3 py-2">SMK Yadika Soreang</span>
                    <h1 class="display-5 fw-bold mb-3">Learning Management System</h1>
                    <p class="fs-5 mb-4 opacity-75">
                        Satu platform untuk kelola materi, tugas, dan presensi kelas —
                        mulai dari presensi manual sampai scan barcode langsung dari guru pengampu.
                    </p>
                    <a href="{{ route('lms.login') }}" class="btn btn-light btn-lg px-4">
                        Masuk ke LMS <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <i class="bi bi-mortarboard" style="font-size:12rem; opacity:.25;"></i>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Fitur Utama</h2>
                <p class="text-muted">Dirancang untuk guru dan siswa PPLG & seluruh jurusan</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-3">
                        <div class="card-body">
                            <div class="feature-icon mb-3"><i class="bi bi-journal-text"></i></div>
                            <h5 class="fw-bold">Materi & Tugas</h5>
                            <p class="text-muted mb-0">Guru unggah materi dan tugas per kelas, siswa mengumpulkan langsung secara online.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-3">
                        <div class="card-body">
                            <div class="feature-icon mb-3"><i class="bi bi-qr-code-scan"></i></div>
                            <h5 class="fw-bold">Presensi Barcode</h5>
                            <p class="text-muted mb-0">Guru membuka sesi presensi berupa QR code; siswa cukup scan untuk tercatat hadir. Sisanya bisa diinput manual.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-3">
                        <div class="card-body">
                            <div class="feature-icon mb-3"><i class="bi bi-graph-up"></i></div>
                            <h5 class="fw-bold">Rekap & Penilaian</h5>
                            <p class="text-muted mb-0">Guru menilai tugas dan memantau rekap kehadiran per kelas dengan mudah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 bg-white border-top text-center text-muted small">
        &copy; {{ date('Y') }} SMK Yadika Soreang — Learning Management System
    </footer>

</body>
</html>
