@extends('Mapping.layouts.app')
@section('title', 'Penempatan PKL')
@section('page-title', 'Penempatan PKL')

@section('content')
    <div class="card">
        <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-map me-2 text-primary"></i>Data Penempatan PKL</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPenempatan">
                <i class="bi bi-plus-lg me-1"></i>Tambah Penempatan
            </button>
        </div>

        {{-- Filter --}}
        <div class="card-body border-bottom py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-sm-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari nama / NIS..." value="{{ request('search') }}">
                </div>
                <div class="col-sm-2">
                    <select name="kelas_id" class="form-select form-select-sm">
                        <option value="">-- Semua Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i
                            class="bi bi-search me-1"></i>Cari</button>
                    <a href="{{ route('penempatan.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Tempat PKL</th>
                            <th>Guru Pembimbing</th>
                            <th>Periode</th>
                            <th>Approval</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penempatan as $i => $p)
                            @php
                                // BARU: tandai apakah data ini masih perlu dilengkapi admin
                                // (berasal dari pengajuan publik siswa: guru/tanggal masih kosong).
                                $belumLengkap = is_null($p->guru_pembimbing_id) || is_null($p->tanggal_mulai);
                            @endphp
                            <tr>
                                <td>{{ $penempatan->firstItem() + $i }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $p->siswa->nama }}</div>
                                    <small class="text-muted font-monospace">{{ $p->siswa->nis }}</small>
                                </td>
                                <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td>
                                    <div>{{ $p->tempatPkl->nama_tempat }}</div>
                                    <small class="text-muted">{{ $p->tempatPkl->bidang_usaha }}</small>
                                </td>
                                {{-- Guru Pembimbing: fallback jika belum diisi admin --}}
                                <td>
                                    @if ($p->guruPembimbing)
                                        {{ $p->guruPembimbing->nama }}
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Belum diisi</span>
                                    @endif
                                </td>
                                {{-- Periode: fallback jika belum diisi admin --}}
                                <td>
                                    @if ($p->tanggal_mulai && $p->tanggal_selesai)
                                        <small>{{ $p->tanggal_mulai->format('d/m/Y') }}</small><br>
                                        <small class="text-muted">s/d {{ $p->tanggal_selesai->format('d/m/Y') }}</small>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Progress approval --}}
                                    <div class="d-flex gap-1">
                                        @php
                                            $steps = [
                                                ['label' => 'WK', 'status' => $p->status_wali_kelas],
                                                ['label' => 'BK', 'status' => $p->status_guru_bk],
                                                ['label' => 'KS', 'status' => $p->status_kesiswaan],
                                                ['label' => 'KJ', 'status' => $p->status_kepala_jurusan],
                                            ];
                                        @endphp
                                        @foreach ($steps as $step)
                                            @php
                                                $bg = match ($step['status']) {
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    default => 'bg-light border text-muted',
                                                };
                                                $tc = in_array($step['status'], ['approved', 'rejected'])
                                                    ? 'text-white'
                                                    : '';
                                            @endphp
                                            <span class="badge-step {{ $bg }} {{ $tc }}"
                                                title="{{ $step['label'] }}">{{ $step['label'] }}</span>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">{{ $p->approval_progress }}/4</small>
                                </td>
                                <td>{!! $p->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('penempatan.show', $p) }}" class="btn btn-sm btn-outline-info"
                                        title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if ($p->status === 'draft')
                                        @if ($belumLengkap)
                                            {{-- BARU: tombol tunggal "Lengkapi" untuk pengajuan publik siswa
                                                 yang guru pembimbing / jadwalnya masih kosong --}}
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalLengkapi{{ $p->id }}" title="Lengkapi Data">
                                                <i class="bi bi-clipboard-check me-1"></i>Lengkapi
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $p->id }}" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('penempatan.ajukan', $p) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-primary" title="Ajukan"
                                                    onclick="return confirm('Ajukan penempatan ini untuk proses approval?')">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    @if ($p->status === 'approved')
                                        <a href="{{ route('penempatan.surat-rekomendasi', $p) }}"
                                            class="btn btn-sm btn-outline-success" title="Cetak Surat Rekomendasi">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    @endif
                                    @if ($p->status !== 'approved')
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#modalHapusPenempatan{{ $p->id }}" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            {{-- Modal Edit Penempatan (untuk draft yang SUDAH lengkap, mis. dibuat admin lewat bulk) --}}
                            @if ($p->status === 'draft' && !$belumLengkap)
                                <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('penempatan.update', $p) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-semibold">Edit Penempatan —
                                                        {{ $p->siswa->nama }}</h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @include('Mapping.penempatan._form', [
                                                        'penempatan' => $p,
                                                        'tempatPkl' => $tempatPkl,
                                                        'guruPembimbing' => $guruPembimbing,
                                                    ])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            {{-- BARU: Modal Lengkapi — khusus pengajuan publik siswa (guru pembimbing & jadwal) --}}
                            @if ($p->status === 'draft' && $belumLengkap)
                                <div class="modal fade" id="modalLengkapi{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('penempatan.lengkapi', $p) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-semibold">
                                                        <i class="bi bi-clipboard-check me-2 text-warning"></i>
                                                        Lengkapi Data — {{ $p->siswa->nama }}
                                                    </h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-muted small">
                                                        Siswa mengajukan tempat PKL
                                                        <strong>{{ $p->tempatPkl->nama_tempat }}</strong>
                                                        secara mandiri. Lengkapi guru pembimbing dan jadwal di bawah ini —
                                                        pengajuan akan otomatis diajukan ke proses approval setelah
                                                        disimpan.
                                                    </p>
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold small">Guru Pembimbing
                                                                <span class="text-danger">*</span></label>
                                                            <select name="guru_pembimbing_id" class="form-select"
                                                                required>
                                                                <option value="">-- Pilih --</option>
                                                                @foreach ($guruPembimbing as $g)
                                                                    <option value="{{ $g->id }}">
                                                                        {{ $g->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold small">Tahun Ajaran <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="tahun_ajaran"
                                                                class="form-control" placeholder="2026/2027" required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold small">Tanggal Mulai <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="date" name="tanggal_mulai"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold small">Tanggal Selesai
                                                                <span class="text-danger">*</span></label>
                                                            <input type="date" name="tanggal_selesai"
                                                                class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-send me-1"></i>Lengkapi &amp; Ajukan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            {{-- Modal Hapus --}}
                            <div class="modal fade" id="modalHapusPenempatan{{ $p->id }}" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <form action="{{ route('penempatan.destroy', $p) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header border-0">
                                                <h6 class="modal-title fw-semibold text-danger"><i
                                                        class="bi bi-exclamation-triangle me-2"></i>Hapus Penempatan</h6>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body pt-0">
                                                Yakin hapus penempatan <strong>{{ $p->siswa->nama }}</strong> di
                                                <strong>{{ $p->tempatPkl->nama_tempat }}</strong>?
                                                @if ($belumLengkap)
                                                    <div class="alert alert-warning small mt-2 mb-0 py-2">
                                                        Ini adalah pengajuan mandiri siswa. Jika dihapus, siswa yang
                                                        bersangkutan bisa mengajukan ulang lewat halaman publik.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">Tidak ada data penempatan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($penempatan->hasPages())
            <div class="card-footer">{{ $penempatan->links() }}</div>
        @endif
    </div>

    {{-- ===================== MODAL TAMBAH PENEMPATAN (BULK) ===================== --}}
    <div class="modal fade" id="modalTambahPenempatan" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('penempatan.store') }}" method="POST" id="formTambahPenempatan">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah
                            Penempatan PKL</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Step 1: Pilih Kelas → muncul daftar siswa --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold small">1. Pilih Kelas <span
                                        class="text-danger">*</span></label>
                                <select id="selectKelasModal" class="form-select">
                                    <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}
                                            ({{ $k->jurusan->nama ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Step 2: Daftar Siswa --}}
                            <div class="col-12" id="siswaSectionWrapper" style="display:none">
                                <label class="form-label fw-semibold small">2. Pilih Siswa <span
                                        class="text-danger">*</span>
                                    <small class="text-muted fw-normal ms-1">(bisa pilih lebih dari satu)</small>
                                </label>
                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        id="btnPilihSemua">Pilih Semua</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        id="btnBatalSemua">Batal Semua</button>
                                    <span id="jumlahDipilih"
                                        class="ms-auto small text-primary fw-semibold align-self-center"></span>
                                </div>
                                <div id="listSiswa"
                                    style="max-height:220px; overflow-y:auto; border:1px solid #dee2e6; border-radius:8px; padding:.5rem;">
                                    <div class="text-center text-muted py-3" id="loadingSiswa">
                                        <div class="spinner-border spinner-border-sm me-2"></div>Memuat data siswa...
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="my-1">
                            </div>

                            {{-- Info PKL --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tempat PKL <span
                                        class="text-danger">*</span></label>
                                <select name="tempat_pkl_id" class="form-select" required>
                                    <option value="">-- Pilih Tempat --</option>
                                    @foreach ($tempatPkl as $t)
                                        <option value="{{ $t->id }}">{{ $t->nama_tempat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Guru Pembimbing <span
                                        class="text-danger">*</span></label>
                                <select name="guru_pembimbing_id" class="form-select" required>
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach ($guruPembimbing as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Tahun Ajaran <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="tahun_ajaran" class="form-control" placeholder="2026/2027"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Tanggal Mulai <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Tanggal Selesai <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSimpanPenempatan" disabled>
                            <i class="bi bi-save me-1"></i>Simpan Penempatan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        {{--
    PERBAIKAN: sebelumnya hardcode "/pkl-api/kelas/${kelasId}/siswa" yang TIDAK
    cocok dengan route asli (/pkl/api/kelas/{kelas}/siswa, name: api.kelas.siswa).
    Ini bikin fetch selalu 404 dan daftar siswa di modal tidak pernah muncul.
    Sekarang dibangun dari route() helper supaya otomatis benar walau prefix
    route berubah di kemudian hari.
--}}
        const urlSiswaByKelasTemplate = "{{ route('api.kelas.siswa', ['kelas' => '__KELAS_ID__']) }}";
        const urlSiswaByKelas = (kelasId) => urlSiswaByKelasTemplate.replace('__KELAS_ID__', kelasId);

        const selectKelas = document.getElementById('selectKelasModal');
        const listSiswa = document.getElementById('listSiswa');
        const wrapper = document.getElementById('siswaSectionWrapper');
        const btnSimpan = document.getElementById('btnSimpanPenempatan');
        const jumlahDipilih = document.getElementById('jumlahDipilih');

        function updateJumlah() {
            const checked = document.querySelectorAll('#listSiswa input[name="siswa_ids[]"]:checked');
            jumlahDipilih.textContent = checked.length > 0 ? `${checked.length} siswa dipilih` : '';
            btnSimpan.disabled = checked.length === 0;
        }

        selectKelas.addEventListener('change', async function() {
            const kelasId = this.value;
            if (!kelasId) {
                wrapper.style.display = 'none';
                return;
            }

            wrapper.style.display = '';
            listSiswa.innerHTML =
                `<div class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm me-2"></div>Memuat...</div>`;

            try {
                const res = await fetch(urlSiswaByKelas(kelasId));
                const data = await res.json();

                if (!data.length) {
                    listSiswa.innerHTML =
                        `<div class="text-center text-muted py-3">Tidak ada siswa di kelas ini.</div>`;
                    return;
                }

                listSiswa.innerHTML = data.map(s => `
            <label class="siswa-checkbox-item d-flex align-items-center gap-3 ${s.sudah_ditempatkan ? 'disabled' : ''}">
                <input type="checkbox" name="siswa_ids[]" value="${s.id}"
                    class="form-check-input flex-shrink-0 siswa-cb"
                    ${s.sudah_ditempatkan ? 'disabled' : ''}
                    onchange="updateJumlahGlobal()">
                <div>
                    <div class="fw-semibold">${s.nama}</div>
                    <small class="text-muted font-monospace">${s.nis} · ${s.jenis_kelamin === 'L' ? 'L' : 'P'}</small>
                    ${s.sudah_ditempatkan ? '<span class="badge bg-success-subtle text-success ms-2">Sudah Ditempatkan</span>' : ''}
                </div>
            </label>
        `).join('');

                updateJumlah();
            } catch (e) {
                listSiswa.innerHTML =
                    `<div class="text-center text-danger py-3">Gagal memuat data siswa.</div>`;
            }
        });

        window.updateJumlahGlobal = updateJumlah;

        document.getElementById('btnPilihSemua').addEventListener('click', () => {
            document.querySelectorAll('#listSiswa input.siswa-cb:not([disabled])').forEach(cb => cb.checked = true);
            document.querySelectorAll('#listSiswa .siswa-checkbox-item:not(.disabled)').forEach(el => el.classList
                .add('selected'));
            updateJumlah();
        });
        document.getElementById('btnBatalSemua').addEventListener('click', () => {
            document.querySelectorAll('#listSiswa input.siswa-cb').forEach(cb => cb.checked = false);
            document.querySelectorAll('#listSiswa .siswa-checkbox-item').forEach(el => el.classList.remove(
                'selected'));
            updateJumlah();
        });

        // Toggle selected class
        listSiswa.addEventListener('change', e => {
            if (e.target.classList.contains('siswa-cb')) {
                const label = e.target.closest('.siswa-checkbox-item');
                label?.classList.toggle('selected', e.target.checked);
            }
        });
    </script>
@endpush
