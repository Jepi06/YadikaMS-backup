{{-- resources/views/admin/mata-pelajaran/import.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Import Mata Pelajaran')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Import Mata Pelajaran</h4>
        <small class="text-muted">Tambah banyak mata pelajaran sekaligus dari file Excel</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Unggah File Excel</h6>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.mata-pelajaran.import.proses') }}"
                    enctype="multipart/form-data" id="importForm">
                    @csrf

                    {{-- Drop zone --}}
                    <div class="border-2 border-dashed rounded-3 p-5 text-center mb-3"
                        id="dropZone"
                        style="border-color:#dee2e6;transition:all .2s"
                        ondragover="handleDragOver(event)"
                        ondragleave="handleDragLeave(event)"
                        ondrop="handleDrop(event)">

                        <div id="dropPlaceholder">
                            <i class="bi bi-file-earmark-excel text-success fs-1 d-block mb-2"></i>
                            <div class="fw-semibold mb-1">Drag & drop file Excel ke sini</div>
                            <small class="text-muted">atau gunakan tombol di bawah</small>
                        </div>

                        <div id="filePreview" class="d-none">
                            <i class="bi bi-file-earmark-check text-success fs-1 d-block mb-2"></i>
                            <div class="fw-semibold" id="fileName"></div>
                            <small class="text-muted" id="fileSize"></small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="fileInput" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-folder2-open me-1"></i>Pilih File Excel
                        </label>
                        <input type="file" name="file" id="fileInput"
                            accept=".xlsx,.xls" class="d-none">
                        <span id="fileNameInline" class="ms-2 small text-muted">
                            Belum ada file dipilih
                        </span>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4"
                            id="submitBtn" disabled>
                            <i class="bi bi-upload me-1"></i>Mulai Import
                        </button>
                        <a href="{{ route('admin.mata-pelajaran.import.template') }}"
                            class="btn btn-outline-primary px-4">
                            <i class="bi bi-download me-1"></i>Download Template
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div id="uploadProgress" class="card border-0 shadow-sm mt-3 d-none">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="spinner-border text-primary" role="status"
                        style="width:24px;height:24px">
                        <span class="visually-hidden">Loading…</span>
                    </div>
                    <div>
                        <div class="fw-semibold">Sedang memproses…</div>
                        <small class="text-muted">Mohon tunggu, jangan tutup halaman ini</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        {{-- Format kolom --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="bi bi-table me-1 text-success"></i>Format Kolom Excel
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 small align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kolom</th>
                            <th>Wajib?</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-monospace text-primary fw-semibold">kode</td>
                            <td><span class="badge bg-danger-subtle text-danger">Wajib</span></td>
                            <td class="text-muted">Kode unik, maks 20 karakter</td>
                        </tr>
                        <tr>
                            <td class="font-monospace text-primary fw-semibold">nama</td>
                            <td><span class="badge bg-danger-subtle text-danger">Wajib</span></td>
                            <td class="text-muted">Nama lengkap mata pelajaran</td>
                        </tr>
                        <tr>
                            <td class="font-monospace text-primary fw-semibold">jurusan</td>
                            <td><span class="text-muted">—</span></td>
                            <td class="text-muted">Kode jurusan (RPL/AKL/PHT). Kosongkan jika mapel umum</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tips --}}
        <div class="card border-0 bg-primary bg-opacity-10">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i>Penting
                </h6>
                <ul class="small mb-0 ps-3">
                    <li class="mb-2">Baris <strong>pertama</strong> adalah heading, data mulai baris ke-2.</li>
                    <li class="mb-2">Kolom <code>jurusan</code> diisi dengan <strong>kode jurusan</strong> (bukan nama). Lihat sheet <em>Daftar Jurusan</em> di template.</li>
                    <li class="mb-2">Mapel dengan <strong>kode yang sudah ada</strong> akan dilewati.</li>
                    <li class="mb-2">Kode akan otomatis diubah menjadi <strong>huruf kapital</strong>.</li>
                    <li class="mb-2">Download template sudah berisi contoh data dan referensi jurusan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleDragOver(e) {
        e.preventDefault();
        document.getElementById('dropZone').style.borderColor = '#0d6efd';
        document.getElementById('dropZone').style.background = '#f0f4ff';
    }
    function handleDragLeave(e) {
        document.getElementById('dropZone').style.borderColor = '#dee2e6';
        document.getElementById('dropZone').style.background = '';
    }
    function handleDrop(e) {
        e.preventDefault();
        handleDragLeave(e);
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('fileInput').files = dt.files;
            showFilePreview(file);
        }
    }
    function showFilePreview(file) {
        document.getElementById('dropPlaceholder').classList.add('d-none');
        document.getElementById('filePreview').classList.remove('d-none');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('fileNameInline').textContent = file.name;
        document.getElementById('submitBtn').disabled = false;
    }
    window.addEventListener('DOMContentLoaded', function () {
        document.getElementById('fileInput').addEventListener('change', function () {
            if (this.files[0]) showFilePreview(this.files[0]);
        });
        document.getElementById('importForm').addEventListener('submit', function () {
            document.getElementById('uploadProgress').classList.remove('d-none');
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span>Memproses…';
        });
    });
</script>
@endpush

@endsection