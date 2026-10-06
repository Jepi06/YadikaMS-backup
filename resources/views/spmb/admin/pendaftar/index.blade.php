@extends('spmb.layouts.admin')

@section('title', $judulHalaman)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">{{ $judulHalaman }}</h4>
        <a href="{{ route('spmb.admin.pendaftar.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pendaftar
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Cari (nama / no. pendaftaran / no. HP)</label>
                    <input type="text" name="cari" value="{{ request('cari') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
                        <option value="diterima" @selected(request('status') === 'diterima')>Diterima</option>
                        <option value="ditolak" @selected(request('status') === 'ditolak')>Belum Diterima</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Lunas</label>
                    <select name="lunas" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="1" @selected(request('lunas') === '1')>Sudah Lunas</option>
                        <option value="0" @selected(request('lunas') === '0')>Belum Lunas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama</th>
                        @unless ($jurusanAktif)
                            <th>Jurusan</th>
                        @endunless
                        <th class="text-end">Nominal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Lunas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $p)
                        <tr>
                            <td>{{ $p->no_pendaftaran }}</td>
                            <td>{{ $p->nama_lengkap }}</td>
                            @unless ($jurusanAktif)
                                <td>{{ optional($p->jurusan)->nama ?? '-' }}</td>
                            @endunless
                            <td class="text-end">Rp {{ number_format($p->nominal_pembayaran ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if ($p->status === 'diterima')
                                    <span class="badge bg-success">Diterima</span>
                                @elseif($p->status === 'ditolak')
                                    <span class="badge bg-danger">Belum Diterima</span>
                                @else
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($p->is_lunas)
                                    <i class="bi bi-check-circle-fill text-success fs-5" title="Lunas"></i>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    <a href="{{ route('spmb.admin.pendaftar.show', $p) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if ($p->belumAdaNominal())
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#nominalModal{{ $p->id }}">
                                            Tambah Nominal
                                        </button>
                                    @elseif(!$p->is_lunas)
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#nominalModal{{ $p->id }}">
                                            Edit Nominal
                                        </button>
                                        <form action="{{ route('spmb.admin.pendaftar.lunas', $p) }}" method="POST"
                                            onsubmit="return confirm('Tandai pembayaran pendaftar ini sebagai LUNAS? Nominal tidak dapat diedit lagi setelah ini.');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success">Lunas</button>
                                        </form>
                                    @else
                                        <form action="{{ route('spmb.admin.pendaftar.batal-lunas', $p) }}" method="POST"
                                            onsubmit="return confirm('Batalkan status LUNAS pendaftar ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Batalkan
                                                Lunas</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('spmb.admin.pendaftar.edit', $p) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('spmb.admin.pendaftar.destroy', $p) }}" method="POST"
                                        onsubmit="return confirm('Hapus data pendaftar ini? Tindakan tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data pendaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $pendaftar->links() }}
        </div>
    </div>

    {{-- Modal popup tambah/edit nominal, satu per baris --}}
    @foreach ($pendaftar as $p)
        <div class="modal fade" id="nominalModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('spmb.admin.pendaftar.nominal', $p) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title">{{ $p->belumAdaNominal() ? 'Tambah' : 'Edit' }} Nominal -
                                {{ $p->nama_lengkap }}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label small">Nominal Pembayaran (Rp)</label>
                            <input type="number" name="nominal_pembayaran" min="0" step="1000"
                                class="form-control" value="{{ (int) $p->nominal_pembayaran }}" required>
                            <div class="form-text">Status otomatis menjadi <strong>Diterima</strong> jika nominal mencapai
                                Rp {{ number_format(\App\Models\SPMB\Pendaftar::MINIMAL_DITERIMA, 0, ',', '.') }}.</div>
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
    @endforeach
@endsection
