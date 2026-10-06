@extends('admin.layouts.app')

@section('title', 'Kelola Guru - ' . $guru->name)

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ $guru->name }}</h4>
            <p class="text-muted mb-0">{{ $guru->email }}</p>
        </div>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        {{-- Penugasan Mengajar --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-plus-circle me-1"></i> Tambah Penugasan Mengajar</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.guru.mengajar.store', $guru) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Mata Pelajaran</label>
                                <select name="mata_pelajaran_id" class="form-select" required>
                                    <option value="">Pilih mata pelajaran</option>
                                    @foreach ($mataPelajaranList as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Kelas</label>
                                <select name="kelas_id" class="form-select" required>
                                    <option value="">Pilih kelas</option>
                                    @foreach ($kelasList as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->jurusan->nama ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" class="form-control" value="{{ $tahunAjaran }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Semester</label>
                                <select name="semester" class="form-select" required>
                                    <option value="Ganjil" @selected($semester === 'Ganjil')>Ganjil</option>
                                    <option value="Genap" @selected($semester === 'Genap')>Genap</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-save me-1"></i> Tambahkan
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Daftar Penugasan Mengajar</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Periode</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengampuMapel as $p)
                                <tr>
                                    <td>{{ $p->mataPelajaran->nama ?? '-' }}</td>
                                    <td>{{ $p->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="small text-muted">{{ $p->tahun_ajaran }} — {{ $p->semester }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.guru.mengajar.destroy', $p) }}"
                                              onsubmit="return confirm('Hapus penugasan ini? Materi/tugas/nilai yang sudah ada di kelas ini ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada penugasan mengajar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Wali Kelas --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-clipboard-data me-1"></i> Wali Kelas</div>
                <div class="card-body">
                    <p class="small text-muted">Periode: <strong>{{ $tahunAjaran }} — {{ $semester }}</strong></p>

                    @if ($waliKelasSaatIni)
                        <div class="alert alert-success d-flex justify-content-between align-items-center">
                            <span>Wali kelas <strong>{{ $waliKelasSaatIni->kelas->nama_kelas ?? '-' }}</strong></span>
                            <form method="POST" action="{{ route('admin.guru.wali-kelas.destroy', $waliKelasSaatIni) }}"
                                  onsubmit="return confirm('Cabut status wali kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Cabut</button>
                            </form>
                        </div>
                    @else
                        <p class="text-muted small">Belum jadi wali kelas manapun di periode ini.</p>
                    @endif

                    <form method="POST" action="{{ route('admin.guru.wali-kelas.store', $guru) }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">
                        <label class="form-label small">
                            {{ $waliKelasSaatIni ? 'Ganti jadi wali kelas...' : 'Jadikan wali kelas...' }}
                        </label>
                        <select name="kelas_id" class="form-select mb-2" required>
                            <option value="">Pilih kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->jurusan->nama ?? '-' }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-info-circle"></i> Kalau kelas yang dipilih sudah punya wali kelas lain di periode ini, otomatis digantikan guru ini.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
