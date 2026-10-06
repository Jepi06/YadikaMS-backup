    @extends('lms.layouts.app')

    @section('title', 'Hasil Presensi')

    @section('content')
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="card-body">
                        @if ($berhasil)
                            <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                            <h4 class="fw-bold mt-3">Presensi Berhasil</h4>
                            <p class="text-muted">{{ $pesan }}</p>
                            @isset($waktu)
                                <p class="small text-muted mb-0">Tercatat pukul {{ $waktu->format('H:i') }}</p>
                            @endisset
                        @else
                            <i class="bi bi-x-circle-fill text-danger" style="font-size:4rem;"></i>
                            <h4 class="fw-bold mt-3">Presensi Gagal</h4>
                            <p class="text-muted">{{ $pesan }}</p>
                        @endif

                        <a href="{{ route('lms.siswa.dashboard') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-house me-1"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
