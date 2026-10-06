@extends('admin.layouts.app')

@section('title', 'Hasil Import Jadwal')

@section('content')
    <h4 class="mb-3">Hasil Import Jadwal</h4>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Berhasil ({{ count($hasil) }})</div>
                <ul class="list-group list-group-flush small">
                    @forelse ($hasil as $h)
                        <li class="list-group-item">{{ $h }}</li>
                    @empty
                        <li class="list-group-item text-muted">Tidak ada.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">Gagal ({{ count($gagal) }})</div>
                <ul class="list-group list-group-flush small">
                    @forelse ($gagal as $e)
                        <li class="list-group-item">{{ $e }}</li>
                    @empty
                        <li class="list-group-item text-muted">Tidak ada.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-dark mt-3">Lihat jadwal</a>
@endsection