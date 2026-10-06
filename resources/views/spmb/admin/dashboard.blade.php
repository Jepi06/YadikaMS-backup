@extends('spmb.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h4 class="fw-bold mb-4">Dashboard SPMB</h4>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Pendaftar</div>
                    <div class="fs-3 fw-bold">{{ $totalPendaftar }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Diterima</div>
                    <div class="fs-3 fw-bold text-success">{{ $totalDiterima }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Menunggu</div>
                    <div class="fs-3 fw-bold text-warning">{{ $totalPending }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Belum Diterima</div>
                    <div class="fs-3 fw-bold text-danger">{{ $totalDitolak }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Rekap per Jurusan</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Jurusan</th>
                        <th class="text-center">Kuota</th>
                        <th class="text-center">Total Pendaftar</th>
                        <th class="text-center">Diterima</th>
                        <th class="text-center">Sisa Kuota</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurusanList as $j)
                        <tr>
                            <td>{{ $j->nama }}</td>
                            <td class="text-center">{{ $j->kuota }}</td>
                            <td class="text-center">{{ $j->pendaftar_count }}</td>
                            <td class="text-center">{{ $j->total_diterima }}</td>
                            <td class="text-center">{{ $j->sisa_kuota }}</td>
                            <td class="text-end">
                                <a href="{{ route('spmb.admin.pendaftar.per-jurusan', $j) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
