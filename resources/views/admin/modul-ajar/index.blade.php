@extends('admin.layouts.app')

@section('title', 'Arsip Modul Ajar')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Arsip Modul Ajar</h4>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Cari judul/nama guru...">
            <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Diunggah</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modulAjar as $m)
                        <tr>
                            <td>
                                {{ $m->judul }}
                                @if ($m->deskripsi)
                                    <div class="small text-muted">{{ $m->deskripsi }}</div>
                                @endif
                            </td>
                            <td>{{ $m->pengampuMapel->guru->name ?? '-' }}</td>
                            <td>{{ $m->pengampuMapel->mataPelajaran->nama ?? '-' }}</td>
                            <td>{{ $m->pengampuMapel->kelas->nama_kelas ?? '-' }}</td>
                            <td class="small text-muted">{{ $m->created_at->translatedFormat('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.modul-ajar.file', $m) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-download"></i> Unduh
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada modul ajar yang diunggah guru mana pun.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($modulAjar->hasPages())
            <div class="p-3 border-top">{{ $modulAjar->links() }}</div>
        @endif
    </div>
@endsection
