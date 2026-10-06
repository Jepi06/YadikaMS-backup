@extends('admin.layouts.app')

@section('title', 'Pengaturan Jam Pelajaran')

@section('content')
    <a href="{{ route('admin.jadwal.index') }}" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> Kembali ke jadwal
    </a>
    <h4 class="my-3">Pengaturan Jam Pelajaran</h4>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-5">
            <form method="POST" action="{{ route('admin.jadwal.pengaturan-jam.update') }}" class="card shadow-sm p-3">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Jam pelajaran (menit per JP)</label>
                    <div class="input-group">
                        <input type="number" name="menit_jp" class="form-control" min="10" max="120" required
                            value="{{ old('menit_jp', $p->menit_jp) }}">
                        <span class="input-group-text">menit</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Istirahat</label>
                    <div class="input-group">
                        <input type="number" name="menit_istirahat" class="form-control" min="0" max="120" required
                            value="{{ old('menit_istirahat', $p->menit_istirahat) }}">
                        <span class="input-group-text">menit</span>
                    </div>
                    <div class="form-text">
                        Jam masuk {{ \App\Support\JamPelajaran::JAM_MASUK }}, {{ \App\Support\JamPelajaran::JUMLAH_JP }} JP per hari,
                        istirahat setelah JP {{ implode(' dan ', \App\Support\JamPelajaran::ISTIRAHAT_SETELAH) }}.
                    </div>
                </div>

                <button class="btn btn-dark">Simpan</button>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">Pratinjau jam</div>
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