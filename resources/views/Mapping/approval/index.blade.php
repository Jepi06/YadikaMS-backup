@extends('Mapping.layouts.app')
@section('title', 'Approval PKL')
@section('page-title', 'Approval Penempatan PKL')

@section('content')
<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-check2-circle me-2 text-primary"></i>
        {{-- PERBAIKAN: $user->getRoleLabel() tidak ada di model User (akan error
             "Call to undefined method"). Accessor yang benar-benar ada adalah
             getRolePklLabelAttribute(), dipanggil sebagai property role_pkl_label. --}}
        Daftar Penempatan Menunggu Persetujuan — <span class="text-primary">{{ $user->role_pkl_label }}</span>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penempatan as $i => $p)
                    <tr>
                        <td>{{ $penempatan->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold">{{ $p->siswa->nama }}</div>
                            <small class="text-muted font-monospace">{{ $p->siswa->nis }}</small>
                        </td>
                        <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $p->tempatPkl->nama_tempat }}</td>
                        <td>{{ $p->guruPembimbing->nama }}</td>
                        <td>
                            <small>{{ $p->tanggal_mulai->format('d/m/Y') }} – {{ $p->tanggal_selesai->format('d/m/Y') }}</small>
                        </td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('penempatan.show', $p) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalApprove{{ $p->id }}">
                                <i class="bi bi-check-lg me-1"></i>Setujui
                            </button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#modalReject{{ $p->id }}">
                                <i class="bi bi-x-lg me-1"></i>Tolak
                            </button>
                        </td>
                    </tr>

                    {{-- Modal Approve --}}
                    <div class="modal fade" id="modalApprove{{ $p->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <form action="{{ route('approval.approve', $p) }}" method="POST">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-semibold text-success"><i class="bi bi-check-circle me-2"></i>Setujui Penempatan</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="small mb-2">Anda akan menyetujui penempatan <strong>{{ $p->siswa->nama }}</strong> di <strong>{{ $p->tempatPkl->nama_tempat }}</strong>.</p>
                                        <label class="form-label small fw-semibold">Catatan (opsional)</label>
                                        <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Tambahkan catatan..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success btn-sm">Ya, Setujui</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Modal Reject --}}
                    <div class="modal fade" id="modalReject{{ $p->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <form action="{{ route('approval.reject', $p) }}" method="POST">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-semibold text-danger"><i class="bi bi-x-circle me-2"></i>Tolak Penempatan</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="small mb-2">Tolak penempatan <strong>{{ $p->siswa->nama }}</strong>?</p>
                                        <label class="form-label small fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                        <textarea name="catatan" class="form-control form-control-sm" rows="2" required placeholder="Tulis alasan penolakan..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger btn-sm">Ya, Tolak</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-check2-all fs-3 d-block mb-2 text-success"></i>
                        Tidak ada penempatan yang perlu disetujui saat ini.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($penempatan->hasPages())
    <div class="card-footer">{{ $penempatan->links() }}</div>
    @endif
</div>
@endsection