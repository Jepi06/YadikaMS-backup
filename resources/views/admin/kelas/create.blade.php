@extends('admin.layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">Tambah Kelas</h4>
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.kelas.store') }}">
                @include('admin.kelas._form')
            </form>
        </div>
    </div>
@endsection