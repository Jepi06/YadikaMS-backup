@extends('spmb.layouts.admin')

@section('title', 'Tambah Pendaftar')

@section('content')
    <h4 class="fw-bold mb-3">Tambah Pendaftar</h4>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('spmb.admin.pendaftar.store') }}">
                @csrf
                @include('spmb.admin.pendaftar._form', ['pendaftar' => null])
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('spmb.admin.pendaftar.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
