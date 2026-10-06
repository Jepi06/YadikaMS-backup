@extends('spmb.layouts.admin')

@section('title', 'Detail Pendaftar')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Detail Pendaftar</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Biodata</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="200">No. Pendaftaran</th>
                            <td>{{ $pendaftar->no_pendaftaran }}</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>{{ $pendaftar->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $pendaftar->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <th>Agama</th>
                            <td>{{ $pendaftar->agama }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pendaftar->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Orang Tua</th>
                            <td>{{ $pendaftar->nama_orang_tua ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Asal Sekolah</th>
                            <td>{{ $pendaftar->asal_sekolah ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td>
                                @if ($pendaftar->no_hp)
                                    <a href="{{ $pendaftar->whatsapp_link }}" target="_blank" rel="noopener"
                                        class="text-decoration-none">
                                        <i class="bi bi-whatsapp text-success me-1"></i>{{ $pendaftar->no_hp }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jurusan</th>
                            <td>{{ optional($pendaftar->jurusan)->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Catatan Admin</th>
                            <td>{{ $pendaftar->catatan_admin ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-bold">Status Penerimaan</div>
                <div class="card-body">
                    @if ($pendaftar->status === 'diterima')
                        <span class="badge bg-success mb-2">Diterima</span>
                    @elseif($pendaftar->status === 'ditolak')
                        <span class="badge bg-danger mb-2">Belum Diterima</span>
                    @else
                        <span class="badge bg-warning text-dark mb-2">Menunggu Verifikasi</span>
                    @endif

                    @if ($pendaftar->processedBy)
                        <div class="small text-muted">Diproses oleh {{ $pendaftar->processedBy->name }} pada
                            {{ optional($pendaftar->processed_at)->format('d-m-Y H:i') }}</div>
                    @endif

                    @if ($pendaftar->status === 'diterima' && $pendaftar->no_hp)
                        <a href="{{ $pendaftar->whatsapp_link }}" target="_blank" rel="noopener"
                            class="btn btn-success btn-sm w-100 mt-2">
                            <i class="bi bi-whatsapp me-1"></i>Kirim Ucapan Selamat
                        </a>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Pembayaran</div>
                <div class="card-body">
                    <div class="fs-5 fw-bold mb-1">Rp {{ number_format($pendaftar->nominal_pembayaran ?? 0, 0, ',', '.') }}
                    </div>
                    @if ($pendaftar->is_lunas)
                        <div class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Lunas
                            @if ($pendaftar->lunasBy)
                                <div class="small text-muted">oleh {{ $pendaftar->lunasBy->name }} pada
                                    {{ optional($pendaftar->lunas_at)->format('d-m-Y H:i') }}</div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted small mb-2">Belum lunas. Sisa tagihan minimal Rp
                            {{ number_format($pendaftar->sisa_tagihan, 0, ',', '.') }}</div>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#nominalModalDetail">
                            {{ $pendaftar->belumAdaNominal() ? 'Tambah Nominal' : 'Edit Nominal' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nominalModalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('spmb.admin.pendaftar.nominal', $pendaftar) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Nominal Pembayaran</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="number" name="nominal_pembayaran" min="0" step="1000" class="form-control"
                            value="{{ (int) $pendaftar->nominal_pembayaran }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
