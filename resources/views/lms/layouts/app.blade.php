{{-- resources/views/lms/layouts/app.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $user = Auth::guard('lms')->user();
    $namaUser = $user->nama ?? ($user->name ?? 'Pengguna');
    $inisial = strtoupper(mb_substr($namaUser, 0, 1));

    // Helper: pakai route kalau sudah ada, kalau belum jadi '#'
    $r = fn($name, $params = []) => Route::has($name) ? route($name, $params) : '#';

    /*
     * Tentukan role yang dipakai untuk menampilkan sidebar.
     * 1) Prioritas: prefix route yang sedang dibuka (lms.siswa.* / lms.guru.*)
     * 2) Kalau di halaman bersama (mis. profil): lihat role akun.
     *    Akun yang murni siswa => sidebar siswa, selain itu => sidebar guru.
     */
    if (request()->routeIs('lms.siswa.*')) {
        $isSiswa = true;
    } elseif (request()->routeIs('lms.guru.*')) {
        $isSiswa = false;
    } else {
        $isSiswa = $user && $user->isSiswaLms() && ! $user->isGuruLms();
    }

    $roleLabel      = $isSiswa ? 'Siswa' : 'Guru';
    $roleLabelPanjang = $isSiswa ? 'Siswa' : 'Guru Pengampu';
    $dashboardUrl   = $isSiswa ? $r('lms.siswa.dashboard') : $r('lms.guru.dashboard');
    $breadcrumbDefault = $isSiswa ? 'Dashboard Siswa' : 'Dashboard Guru';

    $menuAktif = 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold';
    $menuBiasa = 'text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LMS Yadika')</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 500: '#3b82f6',
                            600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#0f172a',
                        },
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'] },
                },
            },
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f1f5f9] text-slate-800 antialiased min-h-screen flex">

    {{-- Backdrop sidebar (mobile) --}}
    <div id="sidebarBackdrop" onclick="toggleSidebar()"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    {{-- ========== SIDEBAR ========== --}}
    <aside id="mainSidebar"
        class="fixed lg:sticky top-0 left-0 h-screen w-72 bg-[#0f172a] text-slate-300 z-50 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0 border-r border-slate-800 shadow-2xl">
        <div>
            <div class="px-6 py-6 flex items-center justify-between border-b border-slate-800/80">
                <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                        <i class="bi bi-mortarboard-fill text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-white tracking-tight leading-none text-base">LMS Yadika</div>
                        <div class="text-[11px] text-slate-400 font-medium tracking-wide mt-1">Soreang Campus</div>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <div class="px-4 py-5 space-y-6 overflow-y-auto max-h-[calc(100vh-190px)] custom-scrollbar">

                @if ($isSiswa)
                    {{-- ===== MENU SISWA ===== --}}
                    <div>
                        <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Utama</div>
                        <nav class="space-y-1">
                            <a href="{{ $r('lms.siswa.dashboard') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.siswa.dashboard') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-grid-1x2-fill text-base"></i>
                                <span>Dashboard Siswa</span>
                            </a>

                            <a href="{{ $r('lms.siswa.kelas.index') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.siswa.kelas.*', 'lms.siswa.materi.*', 'lms.siswa.tugas.*') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-journal-text text-base"></i>
                                <span>Mata Pelajaran</span>
                                @isset($tugasBelumDikumpulkan)
                                    @if ($tugasBelumDikumpulkan > 0)
                                        <span class="ml-auto px-2 py-0.5 text-[11px] rounded-full bg-rose-500/15 text-rose-300 font-semibold border border-rose-500/30" title="Tugas belum dikumpulkan">{{ $tugasBelumDikumpulkan }}</span>
                                    @endif
                                @endisset
                            </a>

                            <a href="{{ $r('lms.siswa.presensi.kamera') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.siswa.presensi.kamera', 'lms.siswa.presensi.scan') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-qr-code-scan text-base"></i>
                                <span>Presensi</span>
                            </a>

                            <a href="{{ $r('lms.siswa.presensi.riwayat') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.siswa.presensi.riwayat') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-clock-history text-base"></i>
                                <span>Riwayat Presensi</span>
                            </a>
                        </nav>
                    </div>
                @else
                    {{-- ===== MENU GURU ===== --}}
                    <div>
                        <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Utama</div>
                        <nav class="space-y-1">
                            {{-- Dashboard --}}
                            <a href="{{ $r('lms.guru.dashboard') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.guru.dashboard') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-grid-1x2-fill text-base"></i>
                                <span>Dashboard Guru</span>
                            </a>

                            {{-- Kelas Saya (aktif juga di halaman presensi/materi/tugas/nilai/modul per kelas) --}}
                            <a href="{{ $r('lms.guru.kelas.index') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.guru.kelas.*', 'lms.guru.presensi.*', 'lms.guru.materi.*', 'lms.guru.tugas.*', 'lms.guru.nilai.*') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-door-open text-base"></i>
                                <span>Kelas Saya</span>
                                @isset($totalKelas)
                                    <span class="ml-auto px-2 py-0.5 text-[11px] rounded-full bg-slate-800 text-slate-300 font-semibold border border-slate-700">{{ $totalKelas }} Kelas</span>
                                @endisset
                            </a>

                            {{-- Modul Ajar: dikelola per kelas, jadi arahkan ke daftar kelas --}}
                            <a href="{{ $r('lms.guru.kelas.index') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.guru.modul-ajar.*') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-journal-bookmark text-base"></i>
                                <span>Modul Ajar</span>
                            </a>

                            {{-- Wali Kelas --}}
                            <a href="{{ $r('lms.guru.wali-kelas.index') }}"
                                class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.guru.wali-kelas.*') ? $menuAktif : $menuBiasa }}">
                                <i class="bi bi-person-badge text-base"></i>
                                <span>Wali Kelas</span>
                            </a>
                        </nav>
                    </div>
                @endif

                {{-- ===== Bersama (semua role) ===== --}}
                <div>
                    <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Pengaturan & Akun</div>
                    <nav class="space-y-1">
                        <a href="{{ $r('lms.profil.edit') }}"
                            class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('lms.profil.*') ? $menuAktif : $menuBiasa }}">
                            <i class="bi bi-person-gear text-base"></i>
                            <span>Profil Saya</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Profil + logout --}}
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/50 border border-slate-800">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center font-bold text-white text-sm shadow-sm flex-shrink-0">
                        {{ $inisial }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-semibold text-white truncate">{{ $namaUser }}</div>
                        <div class="text-[11px] text-slate-400 truncate">{{ $roleLabelPanjang }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ $r('lms.logout') }}">
                    @csrf
                    <button type="submit" title="Keluar"
                        class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                        <i class="bi bi-box-arrow-right text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ========== KONTEN UTAMA ========== --}}
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors">
                    <i class="bi bi-list text-xl"></i>
                </button>
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <span>LMS Yadika</span>
                    <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
                    <span class="text-slate-800 font-semibold">@yield('breadcrumb', $breadcrumbDefault)</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @isset($tahunAjaran)
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold">
                        <i class="bi bi-calendar3"></i>
                        <span>T.A {{ $tahunAjaran }}@isset($semester) • Semester {{ $semester }}@endisset</span>
                    </div>
                @endisset

                <div class="h-6 w-px bg-slate-200"></div>

                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-blue-50">
                        {{ $inisial }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-bold text-slate-800 leading-tight">{{ $namaUser }}</div>
                        <div class="text-[11px] text-slate-500 font-medium">{{ $roleLabel }}</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto space-y-8">
            @if (session('status'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const tertutup = sidebar.classList.contains('-translate-x-full');
            sidebar.classList.toggle('-translate-x-full', !tertutup);
            backdrop.classList.toggle('hidden', !tertutup);
        }
    </script>
    @stack('scripts')
</body>
</html>