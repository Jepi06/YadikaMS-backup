{{--
    View: Mapping/public/ajukan.blade.php
    Halaman PUBLIK (tanpa login) untuk siswa mengajukan tempat PKL sendiri.

    Sesuaikan @extends() di bawah dengan nama layout publik yang sudah ada
    di project (yang dipakai oleh Mapping.public.public / dashboard publik).
--}}
@extends('Mapping.layouts.app')

@section('title', 'Ajukan Tempat PKL')

@section('content')
    <div class="container py-4" style="max-width: 760px;">

        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1"><i class="bi bi-clipboard-plus text-primary me-2"></i>Ajukan Tempat PKL</h4>
            <p class="text-muted mb-0">Pilih kelas, nama kamu, dan tempat PKL. Guru pembimbing & jadwal akan
                dilengkapi oleh admin setelah pengajuan ini masuk.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('pkl.pengajuan.store') }}" method="POST" id="formPengajuanPublik">
                    @csrf

                    {{-- STEP 1: Pilih Kelas --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">1. Pilih Kelas <span
                                class="text-danger">*</span></label>
                        <select name="kelas_id" id="selectKelasPublik" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }} ({{ $k->jurusan->nama ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STEP 2: Pilih Nama (muncul setelah kelas dipilih) --}}
                    <div class="mb-3" id="wrapperSiswaPublik" style="display:none">
                        <label class="form-label fw-semibold small">2. Pilih Nama Kamu <span class="text-danger">*</span>
                            <small class="text-muted fw-normal ms-1">(bisa pilih lebih dari satu jika mengajukan bersama
                                teman satu tempat PKL)</small>
                        </label>

                        <input type="text" id="searchSiswaPublik" class="form-control form-control-sm mb-2"
                            placeholder="Cari nama / NIS...">

                        <div id="listSiswaPublik"
                            style="max-height:240px; overflow-y:auto; border:1px solid #dee2e6; border-radius:8px; padding:.5rem;">
                            <div class="text-center text-muted py-3">Pilih kelas terlebih dahulu.</div>
                        </div>
                        <small id="infoJumlahDipilihPublik" class="text-primary fw-semibold d-block mt-1"></small>
                    </div>

                    <hr>

                    {{-- STEP 3: Tempat PKL --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small d-block">3. Tempat PKL <span
                                class="text-danger">*</span></label>

                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="tempat_option" id="optTempatExisting"
                                value="existing" {{ old('tempat_option', 'existing') == 'existing' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="optTempatExisting">Pilih Tempat
                                Terdaftar</label>

                            <input type="radio" class="btn-check" name="tempat_option" id="optTempatBaru" value="baru"
                                {{ old('tempat_option') == 'baru' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="optTempatBaru">Tambah Tempat Baru</label>
                        </div>

                        {{-- Pilihan A: tempat sudah terdaftar --}}
                        <div id="blokTempatExisting">
                            <select name="tempat_pkl_id" class="form-select">
                                <option value="">-- Pilih Tempat PKL --</option>
                                @foreach ($tempatPkl as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('tempat_pkl_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->nama_tempat }} @if ($t->bidang_usaha)
                                            — {{ $t->bidang_usaha }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilihan B: tambah tempat baru --}}
                        <div id="blokTempatBaru" style="display:none">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="tempat_baru_nama" class="form-control"
                                        placeholder="Nama tempat PKL" value="{{ old('tempat_baru_nama') }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="tempat_baru_bidang" class="form-control"
                                        placeholder="Bidang usaha (opsional)" value="{{ old('tempat_baru_bidang') }}">
                                </div>
                                <div class="col-12">
                                    <textarea name="tempat_baru_alamat" class="form-control" rows="2" placeholder="Alamat lengkap tempat PKL">{{ old('tempat_baru_alamat') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional...">{{ old('keterangan') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold" id="btnKirimPengajuan" disabled>
                        <i class="bi bi-send me-1"></i>Kirim Pengajuan
                    </button>
                    <p class="text-muted small text-center mt-2 mb-0">
                        Setelah dikirim, admin akan melengkapi guru pembimbing & jadwal PKL, lalu pengajuan
                        diproses lewat persetujuan Wali Kelas → Guru BK → Kesiswaan → Kepala Jurusan.
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // URL endpoint publik untuk ambil daftar siswa per kelas (tanpa login)
        const urlSiswaByKelasTemplatePublik = "{{ route('pkl.pengajuan.api.kelas.siswa', ['kelas' => '__KELAS_ID__']) }}";
        const urlSiswaByKelasPublik = (kelasId) => urlSiswaByKelasTemplatePublik.replace('__KELAS_ID__', kelasId);

        const selectKelasPublik = document.getElementById('selectKelasPublik');
        const wrapperSiswaPublik = document.getElementById('wrapperSiswaPublik');
        const listSiswaPublik = document.getElementById('listSiswaPublik');
        const searchSiswaPublik = document.getElementById('searchSiswaPublik');
        const infoJumlahPublik = document.getElementById('infoJumlahDipilihPublik');
        const btnKirimPengajuan = document.getElementById('btnKirimPengajuan');

        let dataSiswaPublik = [];

        function renderSiswaPublik(keyword = '') {
            const kw = keyword.trim().toLowerCase();
            const filtered = dataSiswaPublik.filter(s =>
                s.nama.toLowerCase().includes(kw) || (s.nis ?? '').toLowerCase().includes(kw)
            );

            if (!filtered.length) {
                listSiswaPublik.innerHTML = `<div class="text-center text-muted py-3">Tidak ada nama yang cocok.</div>`;
                return;
            }

            listSiswaPublik.innerHTML = filtered.map(s => `
            <label class="siswa-checkbox-item d-flex align-items-center gap-3 ${s.sudah_ditempatkan ? 'disabled' : ''}" style="padding:.4rem .25rem; cursor:${s.sudah_ditempatkan ? 'not-allowed' : 'pointer'};">
                <input type="checkbox" name="siswa_ids[]" value="${s.id}"
                    class="form-check-input flex-shrink-0 siswa-cb-publik"
                    ${s.sudah_ditempatkan ? 'disabled' : ''}>
                <div>
                    <div class="fw-semibold">${s.nama}</div>
                    <small class="text-muted font-monospace">${s.nis ?? ''} · ${s.jenis_kelamin === 'L' ? 'L' : 'P'}</small>
                    ${s.sudah_ditempatkan ? '<span class="badge bg-warning-subtle text-warning ms-2">Sudah Mengajukan</span>' : ''}
                </div>
            </label>
        `).join('');
        }

        function updateJumlahPublik() {
            const checked = document.querySelectorAll('#listSiswaPublik input.siswa-cb-publik:checked');
            infoJumlahPublik.textContent = checked.length > 0 ? `${checked.length} nama dipilih` : '';
            evaluateBtnState();
        }

        function evaluateBtnState() {
            const adaSiswaDipilih = document.querySelectorAll('#listSiswaPublik input.siswa-cb-publik:checked').length > 0;
            btnKirimPengajuan.disabled = !adaSiswaDipilih;
        }

        selectKelasPublik.addEventListener('change', async function() {
            const kelasId = this.value;
            if (!kelasId) {
                wrapperSiswaPublik.style.display = 'none';
                dataSiswaPublik = [];
                evaluateBtnState();
                return;
            }

            wrapperSiswaPublik.style.display = '';
            searchSiswaPublik.value = '';
            listSiswaPublik.innerHTML =
                `<div class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm me-2"></div>Memuat...</div>`;

            try {
                const res = await fetch(urlSiswaByKelasPublik(kelasId));
                dataSiswaPublik = await res.json();

                if (!dataSiswaPublik.length) {
                    listSiswaPublik.innerHTML =
                        `<div class="text-center text-muted py-3">Tidak ada siswa di kelas ini.</div>`;
                    return;
                }

                renderSiswaPublik();
                evaluateBtnState();
            } catch (e) {
                listSiswaPublik.innerHTML =
                    `<div class="text-center text-danger py-3">Gagal memuat data siswa.</div>`;
            }
        });

        // Fitur search
        searchSiswaPublik.addEventListener('input', function() {
            renderSiswaPublik(this.value);
        });

        // Toggle checkbox jumlah terpilih (delegasi karena elemen dibuat dinamis)
        listSiswaPublik.addEventListener('change', function(e) {
            if (e.target.classList.contains('siswa-cb-publik')) {
                updateJumlahPublik();
            }
        });

        // Toggle blok tempat existing vs baru
        const blokTempatExisting = document.getElementById('blokTempatExisting');
        const blokTempatBaru = document.getElementById('blokTempatBaru');
        const radioTempat = document.querySelectorAll('input[name="tempat_option"]');

        function toggleTempatBlok() {
            const pilihan = document.querySelector('input[name="tempat_option"]:checked').value;
            blokTempatExisting.style.display = pilihan === 'existing' ? '' : 'none';
            blokTempatBaru.style.display = pilihan === 'baru' ? '' : 'none';
        }
        radioTempat.forEach(r => r.addEventListener('change', toggleTempatBlok));
        toggleTempatBlok();
    </script>
@endpush
