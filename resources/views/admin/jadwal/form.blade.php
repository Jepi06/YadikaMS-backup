@extends('admin.layouts.app')

@section('title', $jadwal ? 'Edit Jadwal' : 'Tambah Jadwal')

@section('content')
    <h4 class="mb-3">{{ $jadwal ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" class="card shadow-sm p-3" style="max-width:640px"
        action="{{ $jadwal ? route('admin.jadwal.update', $jadwal) : route('admin.jadwal.store') }}">
        @csrf
        @if ($jadwal) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Guru · Mapel · Kelas</label>
            <select name="pengampu_mapel_id" class="form-select" required>
                <option value="">— pilih penugasan mengajar —</option>
                @foreach ($pengampu as $p)
                    <option value="{{ $p->id }}"
                        @selected(old('pengampu_mapel_id', $jadwal->pengampu_mapel_id ?? null) == $p->id)>
                        {{ $p->guru->name ?? '-' }} · {{ $p->mataPelajaran->nama ?? '-' }} · {{ $p->kelas->nama_kelas ?? '-' }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">
                Hanya penugasan periode aktif. Belum ada? Tambahkan dulu di Kelola Guru.
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Hari</label>
            <select name="hari" class="form-select" required>
                @foreach (\App\Models\Lms\JadwalPelajaran::HARI as $k => $v)
                    <option value="{{ $k }}" @selected(old('hari', $jadwal->hari ?? null) == $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Jam mulai</label>
                <input type="time" name="jam_mulai" class="form-control" required
                    value="{{ old('jam_mulai', isset($jadwal) ? substr($jadwal->jam_mulai, 0, 5) : '') }}">
            </div>
            <div class="col">
                <label class="form-label">Jam selesai</label>
                <input type="time" name="jam_selesai" class="form-control" required
                    value="{{ old('jam_selesai', isset($jadwal) ? substr($jadwal->jam_selesai, 0, 5) : '') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Ruangan (opsional)</label>
            <input type="text" name="ruangan" class="form-control" maxlength="50"
                placeholder="mis. Lab RPL 1" value="{{ old('ruangan', $jadwal->ruangan ?? '') }}">
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-dark">Simpan</button>
            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
@endsection