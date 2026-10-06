@extends('admin.layouts.app')

@section('title', 'Import Guru dari Excel')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">Import Guru dari Excel</h4>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Format Excel</div>
        <div class="card-body">
            <p class="small text-muted">
                Kolom: <strong>Nama Guru</strong>, <strong>Email Guru</strong> (opsional — kosongin biar auto-generate),
                <strong>Mata Pelajaran</strong>, <strong>Kelas Diajar</strong>, <strong>Wali Kelas Dari</strong> (opsional).
            </p>
            <p class="small text-muted mb-0">
                1 guru ngajar di beberapa kelas? Bikin beberapa baris (email/nama sama, sistem otomatis anggap 1 orang).
                Kolom "Wali Kelas Dari" cukup diisi di satu baris guru itu aja. Mata pelajaran &amp; kelas dicocokkan
                ke data yang sudah ada di sistem (nama harus persis sama, huruf besar/kecil gak masalah).
            </p>
            <a href="{{ route('admin.guru.import.template') }}" class="btn btn-outline-success btn-sm mt-3">
                <i class="bi bi-download me-1"></i> Unduh Template Excel
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.guru.import.process') }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label small">File Excel (.xlsx)</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="bi bi-cloud-upload me-1"></i> Import Sekarang
                </button>
            </form>
        </div>
    </div>
@endsection
