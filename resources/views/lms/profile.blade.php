@extends('lms.layouts.app')

@section('title', 'Profil Saya')
@section('breadcrumb', 'Profil Saya')

@section('content')
    @php
        $inisial = collect(preg_split('/\s+/', trim($lmsUser->name)))->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    @endphp

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
        <a href="{{ url('/lms') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
        <i class="bi bi-chevron-right text-[10px] text-slate-300"></i>
        <span class="text-slate-800 font-semibold">Profil Saya</span>
    </nav>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight mb-5">Profil Saya</h1>

    {{-- FLASH SUKSES --}}
    @if (session('status'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 flex items-start gap-2 mb-4">
            <i class="bi bi-check-circle mt-0.5"></i><span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- PERINGATAN PASSWORD DEFAULT --}}
    @if ($errors->has('password') && $errors->first('password') === 'Harap ganti password default sebelum melanjutkan.')
        <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 flex items-start justify-between gap-3 mb-5">
            <div class="flex items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-0.5"></i>
                <span>{{ $errors->first('password') }}</span>
            </div>
            <a href="#keamanan-password" class="shrink-0 px-3 py-1 rounded-md bg-white text-amber-700 hover:bg-amber-100 text-xs font-semibold shadow-sm transition-colors">
                Ganti Sekarang
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        {{-- KOLOM KIRI: AVATAR & INFO AKUN --}}
        <div class="lg:col-span-4 flex flex-col gap-5">

            {{-- AVATAR --}}
            <div class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-6 flex flex-col items-center text-center">
                <div class="mb-3">
                    @if ($lmsUser->avatar)
                        <img src="{{ Storage::url($lmsUser->avatar) }}" alt="Avatar"
                            class="w-24 h-24 rounded-full object-cover shadow-sm">
                    @else
                        <div class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-2xl shadow-sm">
                            {{ $inisial ?: strtoupper(substr($lmsUser->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="font-bold text-slate-900">{{ $lmsUser->name }}</div>
                <div class="text-sm text-slate-500 mb-4">{{ $lmsUser->email }}</div>

                <form method="POST" action="{{ route('lms.profil.avatar') }}" enctype="multipart/form-data" class="w-full">
                    @csrf
                    <label for="avatarInput"
                        class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold cursor-pointer transition-colors">
                        <i class="bi bi-camera"></i><span>Ganti Foto</span>
                    </label>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                    @error('avatar')
                        <div class="text-rose-600 text-xs mt-1.5">{{ $message }}</div>
                    @enderror
                </form>

                @if ($lmsUser->avatar)
                    <form method="POST" action="{{ route('lms.profil.avatar.delete') }}" class="w-full mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus foto profil?')"
                            class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-lg bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 text-sm font-semibold transition-colors">
                            <i class="bi bi-trash"></i><span>Hapus Foto</span>
                        </button>
                    </form>
                @endif

                <span class="text-[11px] text-slate-400 mt-3">Format JPG, PNG, atau WEBP.</span>
            </div>

            {{-- INFO AKUN --}}
            <div class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-5 space-y-4">
                <div class="flex items-center gap-2 font-semibold text-slate-900">
                    <i class="bi bi-person-circle text-blue-600 text-lg"></i><span>Informasi Akun</span>
                </div>

                @if ($errors->hasAny(['name']))
                    <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs px-3 py-2">{{ $errors->first('name') }}</div>
                @endif

                <form method="POST" action="{{ route('lms.profil.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $lmsUser->name) }}" required
                            class="w-full h-10 px-3 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600">Email</label>
                        <input type="email" value="{{ $lmsUser->email }}" disabled
                            class="w-full h-10 px-3 rounded-lg bg-slate-100 text-slate-400 text-sm cursor-not-allowed">
                        <p class="text-[11px] text-slate-400">Hubungi admin untuk mengubah email.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600">Role</label>
                        <input type="text" value="{{ $lmsUser->role_lms_label }}" disabled
                            class="w-full h-10 px-3 rounded-lg bg-slate-100 text-slate-400 text-sm cursor-not-allowed">
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 h-11 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                        <i class="bi bi-save"></i><span>Simpan Perubahan</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: GANTI PASSWORD --}}
        <div class="lg:col-span-8 flex flex-col gap-5">
            <div id="keamanan-password" class="bg-white border border-slate-200/70 rounded-xl shadow-sm p-5 space-y-4">
                <div class="flex items-center gap-2 font-semibold text-slate-900">
                    <i class="bi bi-shield-lock text-blue-600 text-lg"></i><span>Ganti Password</span>
                </div>

                @if ($errors->hasAny(['current_password', 'password']))
                    <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs px-3 py-2">
                        {{ $errors->first('current_password') ?: $errors->first('password') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('lms.profil.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600">Password Lama</label>
                        <div class="relative flex items-center">
                            <input type="password" name="current_password" id="current_password" required
                                class="w-full h-10 px-3 pr-10 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                            <button type="button" data-toggle="current_password" class="absolute right-3 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-600">Password Baru</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password" id="password" required minlength="8"
                                    class="w-full h-10 px-3 pr-10 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                                <button type="button" data-toggle="password" class="absolute right-3 text-slate-400 hover:text-slate-600">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400">Minimal 8 karakter.</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-600">Konfirmasi Password Baru</label>
                            <div class="relative flex items-center">
                                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                                    class="w-full h-10 px-3 pr-10 rounded-lg bg-slate-100 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition">
                                <button type="button" data-toggle="password_confirmation" class="absolute right-3 text-slate-400 hover:text-slate-600">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Indikator kekuatan — dihitung langsung dari isi #password, bukan angka tetap --}}
                    <div class="p-3 rounded-lg bg-slate-50 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Kekuatan Password Baru: <strong id="strengthLabel" class="text-slate-700">-</strong></span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                            <div id="strengthBar" class="h-full rounded-full bg-slate-300 transition-all" style="width: 0%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 flex items-center gap-1">
                            <i class="bi bi-info-circle"></i>Kombinasi huruf besar, angka, dan simbol membuat password lebih kuat.
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 h-11 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold shadow-sm transition-all">
                            <i class="bi bi-key"></i><span>Ubah Password</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- INFO EKOSISTEM AKUN --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                <i class="bi bi-info-circle text-blue-700 mt-0.5"></i>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Akun ini dipakai bersama di sistem PKL, SPMB, dan LMS — perubahan nama/password di sini otomatis berlaku juga saat login ke modul lain.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('[data-toggle]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const input = document.getElementById(btn.dataset.toggle);
                    const icon = btn.querySelector('i');
                    if (!input) return;
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            });

            const passwordInput = document.getElementById('password');
            const bar = document.getElementById('strengthBar');
            const label = document.getElementById('strengthLabel');

            function hitungKekuatan(v) {
                if (!v) return 0;
                let skor = 0;
                if (v.length >= 8) skor++;
                if (v.length >= 12) skor++;
                if (/[A-Z]/.test(v)) skor++;
                if (/[0-9]/.test(v)) skor++;
                if (/[^A-Za-z0-9]/.test(v)) skor++;
                return skor; // 0-5
            }

            passwordInput?.addEventListener('input', () => {
                const skor = hitungKekuatan(passwordInput.value);
                const persen = (skor / 5) * 100;
                bar.style.width = persen + '%';

                if (!passwordInput.value) {
                    label.textContent = '-';
                    bar.className = 'h-full rounded-full bg-slate-300 transition-all';
                } else if (skor <= 1) {
                    label.textContent = 'Lemah';
                    bar.className = 'h-full rounded-full bg-rose-500 transition-all';
                } else if (skor <= 3) {
                    label.textContent = 'Sedang';
                    bar.className = 'h-full rounded-full bg-amber-500 transition-all';
                } else {
                    label.textContent = 'Kuat';
                    bar.className = 'h-full rounded-full bg-emerald-500 transition-all';
                }
            });
        })();
    </script>
@endpush