<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Super Admin') - SMK Yadika Soreang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .sidebar {
            width: 250px;
            min-height: 100vh;
            max-height: 100vh;
            /* tambahkan ini */
            overflow-y: auto;
            /* tambahkan ini */
            background: #1f2937;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .25s ease;
        }

        .sidebar .brand {
            color: #fff;
            font-weight: 700;
            padding: 1.25rem;
            display: block;
        }

        .sidebar .brand small {
            display: block;
            font-weight: 400;
            color: #9ca3af;
            font-size: .72rem;
        }

        .sidebar .nav-link {
            color: #d1d5db;
            padding: .65rem 1.25rem;
            font-size: .92rem;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left .25s ease;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            z-index: 1030;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <i class="bi bi-shield-lock-fill me-1"></i> Super Admin
            <small>Lintas Modul: PKL · SPMB · LMS</small>
        </a>

        <nav class="nav flex-column mt-2 flex-grow-1">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}"
                href="{{ route('admin.pengguna.index') }}">
                <i class="bi bi-people-fill me-2"></i> Kelola Pengguna
            </a>
            <a class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}"
                href="{{ route('admin.siswa.index') }}">
                <i class="bi bi-person-lines-fill me-2"></i> Kelola Siswa
            </a>
            <a class="nav-link {{ request()->routeIs('admin.kenaikan-kelas.*') ? 'active' : '' }}"
                href="{{ route('admin.kenaikan-kelas.index') }}">
                <i class="bi bi-arrow-up-circle me-2"></i> Kenaikan Kelas
            </a>
            <a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}"
                href="{{ route('admin.kelas.index') }}">
                <i class="bi bi-door-open-fill me-2"></i> Kelola Kelas
            </a>
            <a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}"
                href="{{ route('admin.guru.index') }}">
                <i class="bi bi-person-video3 me-2"></i> Kelola Guru
            </a>
            <a class="nav-link {{ request()->routeIs('admin.mata-pelajaran.*') ? 'active' : '' }}"
                href="{{ route('admin.mata-pelajaran.index') }}">
                <i class="bi bi-journal-bookmark-fill me-2"></i> Mata Pelajaran
            </a>
            <a class="nav-link {{ request()->routeIs('admin.modul-ajar.*') ? 'active' : '' }}"
                href="{{ route('admin.modul-ajar.index') }}">
                <i class="bi bi-archive-fill me-2"></i> Arsip Modul Ajar
            </a>
            <a class="nav-link {{ request()->routeIs('admin.monitoring-presensi.*') ? 'active' : '' }}"
                href="{{ route('admin.monitoring-presensi.index') }}">
                <i class="bi bi-broadcast me-2"></i> Monitoring Presensi
            </a>
            <a class="nav-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}"
                href="{{ route('admin.jadwal.index') }}">
                <i class="bi bi-calendar-week me-2"></i> Jadwal Guru
            </a>
            <a class="nav-link {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}"
                href="{{ route('admin.profil.edit') }}">
                <i class="bi bi-person-circle me-2"></i> Profil Saya
            </a>


        </nav>

        {{-- Logout di paling bawah --}}
        <div class="px-3 py-3" style="border-top: 1px solid #374151">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent" style="color:#f87171">
                    <i class="bi bi-box-arrow-left me-2"></i> Logout
                </button>
            </form>
        </div>
    </aside>


    <div class="main-content">
        <div class="topbar d-flex align-items-center justify-content-between px-3 py-2">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <span class="small text-muted">Panel Super Admin</span>
            </div>
            {{-- Topbar kanan: avatar + nama --}}
            @php $superAdmin = request()->attributes->get('superAdminUser'); @endphp
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.profil.edit') }}" class="d-flex align-items-center text-decoration-none">
                    @if ($superAdmin?->avatar)
                        <img src="{{ Storage::url($superAdmin->avatar) }}" class="rounded-circle me-2"
                            style="width:28px;height:28px;object-fit:cover">
                    @else
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center me-2 fw-bold"
                            style="width:28px;height:28px;font-size:.75rem">
                            {{ strtoupper(substr($superAdmin?->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <span class="small text-muted">{{ $superAdmin?->name }}</span>
                </a>
                <span class="badge bg-dark">Super Admin</span>
            </div>
        </div>

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <main class="p-4">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
        const sidebar = document.querySelector('.sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggle');

        function closeSidebar() {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        }

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', closeSidebar);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
