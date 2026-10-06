@extends('lms.layouts.app')

@section('title', 'Rekap Nilai - ' . ($pengampuMapel->mataPelajaran->nama ?? ''))

@section('content')
    <div class="page-header d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $pengampuMapel->mataPelajaran->nama ?? '-' }}</h4>
            <p class="text-muted mb-0">{{ $pengampuMapel->kelas->nama_kelas ?? '-' }} &middot; Rekap Nilai</p>
        </div>
        <a href="{{ route('lms.guru.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}</div>
    @endif

    {{-- Bobot Penilaian --}}
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-sliders me-1 text-primary"></i> Bobot Penilaian</div>
        <div class="card-body">
            <form method="POST" action="{{ route('lms.guru.nilai.bobot', $pengampuMapel) }}" id="formBobot">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-6 col-md-2">
                        <label class="form-label small">Tugas (%)</label>
                        <input type="number" name="bobot_tugas" class="form-control bobot-input" value="{{ $bobot->bobot_tugas }}" min="0" max="100" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small">STS (%)</label>
                        <input type="number" name="bobot_sts" class="form-control bobot-input" value="{{ $bobot->bobot_sts }}" min="0" max="100" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small">SAS (%)</label>
                        <input type="number" name="bobot_sas" class="form-control bobot-input" value="{{ $bobot->bobot_sas }}" min="0" max="100" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small">Sikap (%)</label>
                        <input type="number" name="bobot_sikap" class="form-control bobot-input" value="{{ $bobot->bobot_sikap }}" min="0" max="100" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <span class="small text-muted">Total:</span>
                        <div id="totalBobot" class="fw-bold fs-5">100%</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </div>
                <p class="small text-muted mt-2 mb-0">Total keempat bobot harus persis 100%.</p>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('lms.guru.nilai.export', $pengampuMapel) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export ke Excel
        </a>
    </div>

    {{-- Rekap Nilai --}}
    <form method="POST" action="{{ route('lms.guru.nilai.sikap', $pengampuMapel) }}">
        @csrf
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-1 text-primary"></i> Rekap Nilai Siswa</span>
                <span class="small text-muted">Nilai Akhir dihitung otomatis dari bobot di atas</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Siswa</th>
                            <th style="width:100px">Rata Tugas</th>
                            <th style="width:100px">STS</th>
                            <th style="width:100px">SAS</th>
                            <th style="width:160px">Sikap</th>
                            <th>Catatan Sikap</th>
                            <th style="width:110px">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapSiswa as $siswaId => $r)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $r->siswa->nama }}</span>
                                    @unless ($r->lengkap)
                                        <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Belum Lengkap</span>
                                    @endunless
                                </td>
                                <td>
                                    @if ($r->rata_tugas !== null)
                                        <span class="badge bg-primary-subtle text-primary">{{ number_format($r->rata_tugas, 1) }}</span>
                                        <div class="small text-muted">{{ $r->jumlah_tugas_dinilai }} tugas</div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" name="ujian[{{ $siswaId }}][sts]" class="form-control form-control-sm"
                                           value="{{ $r->nilai_sts }}" min="0" max="100" step="0.1">
                                </td>
                                <td>
                                    <input type="number" name="ujian[{{ $siswaId }}][sas]" class="form-control form-control-sm"
                                           value="{{ $r->nilai_sas }}" min="0" max="100" step="0.1">
                                </td>
                                <td>
                                    <select name="sikap[{{ $siswaId }}][predikat]" class="form-select form-select-sm">
                                        @foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $predikat)
                                            <option value="{{ $predikat }}" @selected(($r->sikap_predikat ?? 'Baik') === $predikat)>{{ $predikat }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="sikap[{{ $siswaId }}][catatan]"
                                           value="{{ $r->sikap_catatan }}" class="form-control form-control-sm" placeholder="Opsional">
                                </td>
                                <td>
                                    <span class="badge bg-dark fs-6">{{ $r->nilai_akhir }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty-state">Belum ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rekapSiswa->count())
                <div class="p-3 border-top d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan STS, SAS &amp; Sikap
                    </button>
                    <span class="small text-muted">
                        Nilai Akhir langsung ter-update otomatis begitu disimpan.
                    </span>
                </div>
            @endif
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function hitungTotalBobot() {
        const inputs = document.querySelectorAll('.bobot-input');
        let total = 0;
        inputs.forEach(el => total += Number(el.value || 0));
        const el = document.getElementById('totalBobot');
        el.textContent = total + '%';
        el.className = 'fw-bold fs-5 ' + (total === 100 ? 'text-success' : 'text-danger');
    }
    document.querySelectorAll('.bobot-input').forEach(el => el.addEventListener('input', hitungTotalBobot));
    hitungTotalBobot();
</script>
@endpush
