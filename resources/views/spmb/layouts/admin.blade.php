<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel SPMB') - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; }

        .sidebar {
            min-height: 100vh;
            background: #0d2b4e;
            color: #fff;
        }

        .sidebar a {
            color: #cfe0f2;
            text-decoration: none;
            display: block;
            padding: .55rem 1rem;
            border-radius: .375rem;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #12406f;
            color: #fff;
        }

        .sidebar .section-label {
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .05em;
            color: #7fa8d3;
            padding: 1rem 1rem .25rem;
        }

        .badge-kuota { font-size: .7rem; }
    </style>
</head>

<body>
    <div class="d-flex">
        {{-- Sidebar --}}
        <nav class="sidebar p-3" style="width:260px;flex-shrink:0;">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-mortarboard-fill fs-3"></i>
                <div>
                    <div class="fw-bold">SPMB</div>
                    <div class="small text-white-50">SMK Yadika Soreang</div>
                </div>
            </div>

            <a href="{{ route('spmb.admin.dashboard') }}"
                class="{{ request()->routeIs('spmb.admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('spmb.admin.pendaftar.index') }}"
                class="{{ request()->routeIs('spmb.admin.pendaftar.index') ? 'active' : '' }}">
                <i class="bi bi-people-fill me-2"></i> Semua Pendaftar
            </a>

            <div class="section-label">Per Jurusan</div>
            @foreach ($sidebarJurusan as $j)
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('spmb.admin.pendaftar.per-jurusan', $j) }}"
                        class="flex-grow-1 d-flex justify-content-between align-items-center
                            {{ request()->routeIs('spmb.admin.pendaftar.per-jurusan') && request()->route('jurusan')?->id === $j->id ? 'active' : '' }}">
                        <span><i class="bi bi-diagram-3 me-2"></i>{{ $j->nama }}</span>
                        <span class="badge bg-secondary badge-kuota me-2">{{ $j->pendaftar_count }}</span>
                    </a>
                    <a href="{{ route('spmb.admin.export.excel.per-jurusan', $j) }}"
                        class="px-2" title="Export Excel {{ $j->nama }}">
                        <i class="bi bi-file-earmark-excel text-success"></i>
                    </a>
                </div>
            @endforeach

            <div class="section-label">Export</div>
            <a href="{{ route('spmb.admin.export.excel') }}">
                <i class="bi bi-file-earmark-excel me-2"></i> Export Excel
            </a>
            <a href="{{ route('spmb.admin.export.pdf') }}">
                <i class="bi bi-file-earmark-pdf me-2"></i> Rekap PDF
            </a>

            <div class="section-label">Akun</div>
            <a href="{{ route('spmb.admin.profil.edit') }}"
                class="{{ request()->routeIs('spmb.admin.profil.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle me-2"></i> Profil Saya
            </a>
            <form action="{{ route('spmb.logout') }}" method="POST" class="mt-1">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </nav>

        {{-- Main --}}
        <div class="flex-grow-1 d-flex flex-column">
            {{-- Topbar --}}
            <div class="bg-white border-bottom px-4 py-2 d-flex align-items-center justify-content-end">
                @php $spmbUser = auth('spmb')->user(); @endphp
                <a href="{{ route('spmb.admin.profil.edit') }}"
                    class="d-flex align-items-center text-decoration-none me-3">
                    @if ($spmbUser?->avatar)
                        <img src="{{ Storage::url($spmbUser->avatar) }}"
                            class="rounded-circle me-2"
                            style="width:28px;height:28px;object-fit:cover">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2 fw-bold"
                            style="width:28px;height:28px;font-size:.75rem">
                            {{ strtoupper(substr($spmbUser?->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <span class="small text-muted">{{ $spmbUser?->name }}</span>
                </a>
            </div>

            {{-- Content --}}
            <main class="flex-grow-1 p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>