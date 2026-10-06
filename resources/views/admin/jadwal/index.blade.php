@extends('admin.layouts.app')

@section('title', 'Jadwal Guru')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h4 class="mb-0">Jadwal Guru</h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.jadwal.pengaturan-jam') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-clock"></i> Pengaturan Jam
            </a>
            <a href="{{ route('admin.jadwal.import') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-upload"></i> Import
            </a>
            <a href="{{ route('admin.jadwal.export') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> Export
            </a>
            <a href="{{ route('admin.jadwal.create') }}" class="btn btn-dark btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Jadwal
            </a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="hari" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua hari</option>
                @foreach (\App\Models\Lms\JadwalPelajaran::HARI as $k => $v)
                    <option value="{{ $k }}" @selected($hari == $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm"
                placeholder="Cari nama guru / kelas">
        </div>
        <div class="col-auto"><button class="btn btn-sm btn-outline-secondary">Cari</button></div>
    </form>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @php $menitJp = \App\Models\Lms\PengaturanJam::ambil()->menit_jp; @endphp

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>JP</th>
                        <th>Guru</th>
                        <th>Mapel</th>
                        <th>Kelas</th>
                        <th>Ruangan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwal as $j)
                        <tr>
                            <td>{{ $j->nama_hari }}</td>
                            <td class="text-nowrap">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</td>
                            <td class="text-nowrap">
                                @if ($j->jp_mulai && $j->jumlah_jp)
                                    JP {{ $j->jp_mulai }}
                                    <span class="text-muted small">
                                        · {{ $j->jumlah_jp }} JP ({{ $j->jumlah_jp * $menitJp }} mnt)
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $j->pengampuMapel->guru->name ?? '-' }}</td>
                            <td>{{ $j->pengampuMapel->mataPelajaran->nama ?? '-' }}</td>
                            <td>{{ $j->pengampuMapel->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $j->ruangan ?: '-' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.jadwal.edit', $j) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.jadwal.destroy', $j) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada jadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $jadwal->links() }}</div>
@endsection