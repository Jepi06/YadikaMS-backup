@extends('admin.layouts.app')

@section('title', 'Kenaikan Kelas')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Kenaikan Kelas</h4>
        <small class="text-muted">Update kelas siswa untuk tahun ajaran baru tanpa menghapus data nilai</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">

        {{-- Download Template --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">
                    <i class="bi bi-download me-1 text-success"></i>Download Template per Kelas
                </h6>
                <p class="text-muted small mb-3">
                    Template berisi daftar siswa aktif di kelas tersebut. Isi cell <strong>G1</strong> dengan nama kelas tujuan.
                </p>
                <form method="GET" action="{{ route('admin.kenaikan-kelas.template') }}"
                    class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold mb-1">Pilih Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">— Pilih kelas —</option>
                            @php $grouped = $kelas->groupBy('tingkat'); @endphp
                            @foreach (['X', 'XI', 'XII'] as $tingkat)
                                @if ($grouped->has($tingkat))
                                    <optgroup label="Tingkat {{ $tingkat }}">
                                        @foreach ($grouped[$tingkat] as $k)
                                            <option value="{{ $k->id }}">
                                                {{ $k->nama_kelas }}
                                                ({{ $k->siswa_count }} siswa aktif)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-file-earmark-excel me-1"></i>Download Template
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Upload --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-1">Upload File Kenaikan Kelas</h6>
                <p class="text-muted small mb-4">
                    File akan di-<strong>preview dulu</strong> sebelum dieksekusi.
                    Data nilai, tugas, dan pengumpulan tidak akan terhapus.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.kenaikan-kelas.preview') }}"
                    enctype="multipart/form-data" id="uploadForm">
                    @csrf

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

                    <button type="submit" class="btn btn-primary px-4"
                        id="submitBtn" disabled>
                        <i class="bi bi-eye me-1"></i>Preview Kenaikan Kelas
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-info-circle me-1 text-primary"></i>Cara Penggunaan
            </div>
            <div class="card-body p-4">
                <ol class="small mb-0 ps-3">
                    <li class="mb-2">Pilih kelas dan <strong>download template</strong>.</li>
                    <li class="mb-2">Buka file Excel, isi <strong>cell G1</strong> dengan nama kelas tujuan (lihat sheet "Daftar Kelas" untuk referensi).</li>
                    <li class="mb-2">Untuk siswa kelas XII, ubah <strong>G2</strong> menjadi <code>lulus</code>.</li>
                    <li class="mb-2"><strong>Upload</strong> file yang sudah diisi.</li>
                    <li class="mb-2">Periksa <strong>preview</strong> perubahan, lalu klik Eksekusi.</li>
                </ol>
            </div>
        </div>

        <div class="card border-0 bg-warning bg-opacity-10">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>Perhatian
                </h6>
                <ul class="small mb-0 ps-3">
                    <li class="mb-2">Data <strong>nilai, tugas, dan pengumpulan tidak dihapus</strong> — hanya kelas_id siswa yang diupdate.</li>
                    <li class="mb-2">Siswa kelas XII otomatis diberi hint <strong>lulus</strong> di template.</li>
                    <li class="mb-2">Preview akan ditampilkan sebelum perubahan benar-benar disimpan.</li>
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
});
</script>
@endpush

@endsection