<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk LMS - SMK Yadika Soreang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">

    <div
        class="relative w-full max-w-5xl rounded-2xl shadow-xl overflow-hidden bg-white grid grid-cols-1 lg:grid-cols-12">

        {{-- KOLOM KIRI: BRANDING --}}
        <div
            class="lg:col-span-5 relative flex flex-col justify-between p-8 lg:p-10 bg-gradient-to-br from-blue-700 via-blue-600 to-sky-600 text-white overflow-hidden">
            <div class="relative z-10 flex flex-col gap-8">
                <div class="flex items-center gap-3.5">
                    <div
                        class="w-12 h-12 rounded-xl bg-white text-blue-700 flex items-center justify-center shadow-md shrink-0">
                        <i class="bi bi-mortarboard-fill text-2xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-lg tracking-tight leading-tight">SMK YADIKA</span>
                        <span class="text-[11px] uppercase tracking-wider text-blue-100">Soreang • LMS</span>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 text-xs font-semibold w-fit backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                        PORTAL PEMBELAJARAN RESMI
                    </span>
                    <h2 class="text-2xl lg:text-3xl font-bold leading-tight">Sistem Pembelajaran Digital SMK Yadika
                        Soreang</h2>
                    <p class="text-sm text-blue-100 leading-relaxed">Presensi, materi, tugas, dan rekap nilai dalam satu
                        portal untuk siswa, guru, dan wali kelas.</p>
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-white/10 backdrop-blur-sm">
                        <div class="w-9 h-9 rounded-md bg-white/15 flex items-center justify-center shrink-0"><i
                                class="bi bi-qr-code-scan text-lg"></i></div>
                        <div class="flex flex-col">
                            <span class="font-semibold text-sm">Presensi QR Dinamis</span>
                            <span class="text-xs text-blue-100">Pencatatan kehadiran harian per mata pelajaran</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-white/10 backdrop-blur-sm">
                        <div class="w-9 h-9 rounded-md bg-white/15 flex items-center justify-center shrink-0"><i
                                class="bi bi-journal-bookmark-fill text-lg"></i></div>
                        <div class="flex flex-col">
                            <span class="font-semibold text-sm">Materi &amp; Tugas Interaktif</span>
                            <span class="text-xs text-blue-100">Akses materi, kumpulkan tugas, pantau progres</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-white/10 backdrop-blur-sm">
                        <div class="w-9 h-9 rounded-md bg-white/15 flex items-center justify-center shrink-0"><i
                                class="bi bi-graph-up-arrow text-lg"></i></div>
                        <div class="flex flex-col">
                            <span class="font-semibold text-sm">Rekap Nilai Real-time</span>
                            <span class="text-xs text-blue-100">Transparansi capaian nilai dan absensi siswa</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-10 pt-4 text-xs text-blue-100">
                © {{ date('Y') }} SMK Yadika Soreang
            </div>
        </div>

        {{-- KOLOM KANAN: FORM LOGIN --}}
        <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col justify-center bg-white">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('lms') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-700 hover:text-blue-800 transition-colors">
                    <i class="bi bi-arrow-left"></i><span>Kembali ke Beranda</span>
                </a>
            </div>

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk ke LMS</h1>
                <p class="text-sm text-slate-500 mt-1">Silakan masuk dengan akun terdaftar Anda.</p>
            </div>

            @if ($errors->any())
                <div
                    class="mb-5 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 flex items-start gap-2.5 text-sm">
                    <i class="bi bi-exclamation-circle mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('lms.login.process') }}" id="loginForm" class="flex flex-col gap-4">
                @csrf

                <div class="flex flex-col gap-1.5">
                    <label for="login" class="text-sm font-semibold text-slate-700">Email / NIS</label>
                    <div class="relative flex items-center">
                        <i class="bi bi-person-badge absolute left-3.5 text-slate-400"></i>
                        <input type="text" name="login" id="login" value="{{ old('login') }}" required
                            autofocus placeholder="nama@smk.sch.id atau NIS"
                            class="w-full h-11 pl-10 pr-4 rounded-lg bg-slate-100 text-slate-900 placeholder:text-slate-400 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                    </div>
                    <p class="text-xs text-slate-400">Siswa bisa login pakai NIS, tidak wajib pakai email.</p>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-sm font-semibold text-slate-700">Password</label>
                    <div class="relative flex items-center">
                        <i class="bi bi-lock absolute left-3.5 text-slate-400"></i>
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                            class="w-full h-11 pl-10 pr-11 rounded-lg bg-slate-100 text-slate-900 placeholder:text-slate-400 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                        <button type="button" id="togglePassword" aria-label="Tampilkan atau sembunyikan password"
                            class="absolute right-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label for="remember" class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/40">
                        <span class="text-sm text-slate-700">Ingat saya</span>
                    </label>
                    <span class="text-xs text-slate-400">Lupa password? Hubungi TU sekolah.</span>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full h-11 mt-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm flex items-center justify-center gap-2 transition-all active:scale-[0.99]">
                    <span id="btnLabel">Masuk ke LMS</span>
                    <i class="bi bi-arrow-right" id="btnIcon"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('toggleIcon');
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnLabel = document.getElementById('btnLabel');
            const btnIcon = document.getElementById('btnIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                    if (btnLabel) btnLabel.textContent = 'Memverifikasi...';
                    if (btnIcon) btnIcon.className = 'bi bi-arrow-repeat animate-spin';
                });
            }
        })();
    </script>
</body>

</html>
