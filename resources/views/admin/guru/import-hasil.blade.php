@extends('admin.layouts.app')

@section('title', 'Hasil Import Guru')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Hasil Import</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru.import.form') }}" class="btn btn-sm btn-outline-secondary">Import Lagi</a>
            <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-primary">Ke Daftar Guru</a>
        </div>
    </div>

    @if (count($hasil))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold text-success">
                <i class="bi bi-check-circle me-1"></i> Berhasil ({{ count($hasil) }})
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($hasil as $h)
                    <li class="list-group-item small">{{ $h }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (count($errors))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold text-danger">
                <i class="bi bi-exclamation-triangle me-1"></i> Gagal ({{ count($errors) }})
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($errors as $e)
                    <li class="list-group-item small text-danger">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! count($hasil) && ! count($errors))
        <div class="alert alert-warning">Tidak ada baris yang diproses — file mungkin kosong.</div>
    @endif
@endsection
