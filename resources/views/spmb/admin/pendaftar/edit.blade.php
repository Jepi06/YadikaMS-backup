@extends('spmb.layouts.admin')

@section('title', 'Edit Pendaftar')

@section('content')
    <h4 class="fw-bold mb-3">Edit Pendaftar - {{ $pendaftar->nama_lengkap }}</h4>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('spmb.admin.pendaftar.update', $pendaftar) }}">
                @csrf
                @method('PUT')
                @include('spmb.admin.pendaftar._form', ['pendaftar' => $pendaftar])
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('spmb.admin.pendaftar.show', $pendaftar) }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
