@extends('admin.layouts.app')

@section('title', 'Import Jadwal')

@section('content')
    <a href="{{ route('admin.jadwal.index') }}" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> Kembali ke jadwal
    </a>
    <h4 class="my-3">Import Jadwal Guru</h4>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm p-3">
                <ol class="small">
                    <li>
                        <a href="{{ route('admin.jadwal.import.template') }}">Download template</a>
                        atau <a href="{{ route('admin.jadwal.export') }}">export jadwal yang ada</a>, lalu edit.
                    </li>
                    <li>Satu baris = satu guru mengajar satu mapel di satu kelas pada satu hari.</li>
                    <li>
                        <b>JP Ke</b> adalah JP pertama pelajaran itu, dan <b>Jumlah JP</b> adalah banyaknya JP.
                        Contoh: JP Ke 1, Jumlah JP 2 berarti JP 1 dan 2, lama pelajaran = 2 × menit per JP.
                    </li>
                    <li>Penugasan mengajar (guru + mapel + kelas) dibuat otomatis kalau belum ada.</li>
                </ol>

                <form method="POST" action="{{ route('admin.jadwal.import.proses') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control mb-3" accept=".xlsx,.xls" required>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="ganti" value="1" id="ganti">
                        <label class="form-check-label" for="ganti">
                            Hapus dulu semua jadwal periode aktif, lalu ganti dengan isi file ini
                        </label>
                    </div>

                    <button class="btn btn-dark">Import</button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                    <span>Jam pelajaran saat ini</span>
                    <a href="{{ route('admin.jadwal.pengaturan-jam') }}" class="small">Atur</a>
                </div>
                <table class="table table-sm mb-0">
                    @foreach ($grid as $g)
                        <tr class="{{ $g['istirahat'] ? 'table-light text-muted' : '' }}">
                            <td style="width:90px">{{ $g['istirahat'] ? 'Istirahat' : 'JP ' . $g['jp'] }}</td>
                            <td>{{ $g['mulai'] }} – {{ $g['selesai'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection