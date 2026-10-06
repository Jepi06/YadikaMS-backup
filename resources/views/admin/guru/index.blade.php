@extends('admin.layouts.app')

@section('title', 'Kelola Guru')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Kelola Guru</h4>
            <p class="text-muted mb-0 small">Periode aktif: {{ $tahunAjaran }} — {{ $semester }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Tambah Guru
            </a>
            <a href="{{ route('admin.guru.import.form') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </a>
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Cari nama/email...">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Mengajar (periode aktif)</th>
                        <th>Wali Kelas</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guru as $g)
                        @php $wali = $waliKelasMap->get($g->id); @endphp
                        <tr>
                            <td>{{ $g->name }}</td>
                            <td class="small text-muted">{{ $g->email }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">{{ $g->mengajar_aktif_count }} kelas</span>
                            </td>
                            <td>
                                @if ($wali)
                                    <span class="badge bg-success-subtle text-success-emphasis">{{ $wali->kelas->nama_kelas ?? '-' }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.guru.kelola', $g) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-sliders"></i> Kelola
                                </a>
                                                               <a href="{{ route('admin.guru.edit', $g) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.guru.reset-password', $g) }}" class="d-inline"
                                      onsubmit="return confirm('Reset password {{ $g->name }} ke default \'password\'?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </form> 
                                <form method="POST" action="{{ route('admin.guru.destroy', $g) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus akun {{ $g->name }}? Ini gak bisa dibatalin.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada guru LMS terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($guru->hasPages())
            <div class="p-3 border-top">{{ $guru->links() }}</div>
        @endif
    </div>
@endsection