@extends('admin.layouts.app')

@section('title', 'Preview Kenaikan Kelas')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.kenaikan-kelas.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Preview Kenaikan Kelas</h4>
            <small class="text-muted">Periksa perubahan sebelum dikonfirmasi</small>
        </div>
    </div>

    {{-- Error & skipped --}}
    @if (count($errors) > 0)
        <div class="alert alert-warning alert-dismissible fade show mb-4">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ count($errors) }} baris bermasalah:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach ($errors as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (count($skipped) > 0)
        <div class="alert alert-info alert-dismissible fade show mb-4">
            <strong><i class="bi bi-info-circle-fill me-1"></i>{{ count($skipped) }} baris dilewati:</strong>
            <div class="mt-2 small">
                @foreach ($skipped as $s)
                    <span class="badge bg-secondary me-1">{{ $s['nis'] }} — {{ $s['alasan'] }}</span>
                @endforeach
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        @php
            $naik = collect($preview)->where('aksi', 'NAIK KELAS')->count();
            $lulus = collect($preview)->where('aksi', 'LULUS')->count();
            $keluar = collect($preview)->where('aksi', 'KELUAR')->count();
        @endphp
        <div class="col-4">
            <div class="card border-0 bg-primary bg-opacity-10 text-center p-3">
                <div class="fs-3 fw-black text-primary">{{ $naik }}</div>
                <div class="small text-muted">Naik Kelas</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 bg-success bg-opacity-10 text-center p-3">
                <div class="fs-3 fw-black text-success">{{ $lulus }}</div>
                <div class="small text-muted">Lulus</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card border-0 bg-secondary bg-opacity-10 text-center p-3">
                <div class="fs-3 fw-black text-secondary">{{ $keluar }}</div>
                <div class="small text-muted">Keluar</div>
            </div>
        </div>
    </div>

    {{-- Tabel preview --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold py-3">
            Daftar Perubahan ({{ count($preview) }} siswa)
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas Lama</th>
                        <th>Kelas Baru</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($preview as $i => $p)
                        <tr>
                            <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                            <td class="font-monospace">{{ $p['nis'] }}</td>
                            <td class="fw-semibold">{{ $p['nama'] }}</td>
                            <td class="text-muted">{{ $p['kelas_lama'] }}</td>
                            <td>
                                @if ($p['aksi'] === 'NAIK KELAS')
                                    <span class="text-primary fw-semibold">{{ $p['kelas_baru'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($p['aksi'] === 'NAIK KELAS')
                                    <span class="badge bg-primary-subtle text-primary">Naik Kelas</span>
                                @elseif ($p['aksi'] === 'LULUS')
                                    <span class="badge bg-success-subtle text-success">Lulus</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Keluar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                Tidak ada data valid untuk diproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Konfirmasi --}}
    @if (count($preview) > 0)
        <div class="card border-0 border-warning shadow-sm">
            <div class="card-body p-4 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <div class="fw-bold">Konfirmasi Kenaikan Kelas</div>
                    <small class="text-muted">
                        Perubahan ini <strong>tidak bisa dibatalkan</strong>.
                        Data nilai tetap aman.
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.kenaikan-kelas.index') }}" class="btn btn-outline-secondary px-4">
                        Batal
                    </a>
                    <form method="POST" action="{{ route('admin.kenaikan-kelas.eksekusi') }}">
                        @csrf
                        {{-- tidak perlu tmp_path lagi, diambil dari session --}}
                        <button type="submit" class="btn btn-success px-4"
                            onclick="return confirm('Yakin eksekusi kenaikan kelas untuk {{ count($preview) }} siswa?')">
                            <i class="bi bi-check-lg me-1"></i>Eksekusi Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
