@extends('Mapping.layouts.app')
@section('title', 'Detail Penempatan')
@section('page-title', 'Detail Penempatan PKL')

@section('content')
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-person-lines-fill me-2 text-primary"></i>Informasi Siswa & Penempatan</span>
                    {{-- BARU: tombol cetak surat rekomendasi PDF, hanya muncul kalau
                     sudah approved penuh (sesuai validasi di controller) --}}
                    @if ($penempatan->status === 'approved')
                        <a href="{{ route('penempatan.surat-rekomendasi', $penempatan) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Cetak Surat Rekomendasi
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted w-40">Siswa</td>
                            <td class="fw-semibold">{{ $penempatan->siswa->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIS</td>
                            <td class="font-monospace">{{ $penempatan->siswa->nis }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kelas</td>
                            <td>{{ $penempatan->siswa->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tempat PKL</td>
                            <td class="fw-semibold">{{ $penempatan->tempatPkl->nama_tempat }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat Tempat</td>
                            <td>{{ $penempatan->tempatPkl->alamat }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bidang Usaha</td>
                            <td>{{ $penempatan->tempatPkl->bidang_usaha ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Guru Pembimbing</td>
                            <td class="fw-semibold">{{ $penempatan->guruPembimbing->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Ajaran</td>
                            <td>{{ $penempatan->tahun_ajaran }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periode</td>
                            <td>{{ $penempatan->tanggal_mulai->format('d M Y') }} —
                                {{ $penempatan->tanggal_selesai->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>{!! $penempatan->status_badge !!}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Keterangan</td>
                            <td>{{ $penempatan->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header py-3">
                    <i class="bi bi-check2-all me-2 text-primary"></i>Status Approval
                </div>
                <div class="card-body">
                    {{-- BARU: ringkasan approver yang masih ditunggu, pakai
                     accessor pending_approvers dari model --}}
                    @if ($penempatan->status === 'diajukan' && count($penempatan->pending_approvers))
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="bi bi-hourglass-split me-1"></i>
                            Menunggu approval dari: <strong>{{ implode(', ', $penempatan->pending_approvers) }}</strong>
                        </div>
                    @endif

                    @php
                        $steps = [
                            [
                                'label' => 'Wali Kelas',
                                'status' => $penempatan->status_wali_kelas,
                                'catatan' => $penempatan->catatan_wali_kelas,
                                'approver' => $penempatan->approverWaliKelas,
                                'at' => $penempatan->approved_at_wali_kelas,
                            ],
                            [
                                'label' => 'Guru BK',
                                'status' => $penempatan->status_guru_bk,
                                'catatan' => $penempatan->catatan_guru_bk,
                                'approver' => $penempatan->approverGuruBk,
                                'at' => $penempatan->approved_at_guru_bk,
                            ],
                            [
                                'label' => 'Kesiswaan',
                                'status' => $penempatan->status_kesiswaan,
                                'catatan' => $penempatan->catatan_kesiswaan,
                                'approver' => $penempatan->approverKesiswaan,
                                'at' => $penempatan->approved_at_kesiswaan,
                            ],
                            [
                                'label' => 'Kepala Jurusan',
                                'status' => $penempatan->status_kepala_jurusan,
                                'catatan' => $penempatan->catatan_kepala_jurusan,
                                'approver' => $penempatan->approverKepalaJurusan,
                                'at' => $penempatan->approved_at_kepala_jurusan,
                            ],
                        ];
                    @endphp

                    @foreach ($steps as $idx => $step)
                        @php
                            $icon = match ($step['status']) {
                                'approved' => '<i class="bi bi-check-circle-fill text-success fs-5"></i>',
                                'rejected' => '<i class="bi bi-x-circle-fill text-danger fs-5"></i>',
                                default => '<i class="bi bi-circle text-muted fs-5"></i>',
                            };
                            $label_class = match ($step['status']) {
                                'approved' => 'text-success',
                                'rejected' => 'text-danger',
                                default => 'text-muted',
                            };
                        @endphp
                        <div class="d-flex gap-3 mb-3">
                            <div class="flex-shrink-0 mt-1">{!! $icon !!}</div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $idx + 1 }}. {{ $step['label'] }}</div>
                                <div class="small {{ $label_class }}">
                                    {{ ucfirst($step['status']) }}
                                    @if ($step['approver'])
                                        oleh <strong>{{ $step['approver']->name }}</strong>
                                        · {{ $step['at']?->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                                @if ($step['catatan'])
                                    <div class="small text-muted fst-italic mt-1">"{{ $step['catatan'] }}"</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('penempatan.index') }}" class="btn btn-secondary btn-sm mt-2">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
@endsection
