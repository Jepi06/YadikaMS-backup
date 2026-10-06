@extends('lms.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <h4 class="fw-bold mb-4">Dashboard Admin LMS</h4>

    <div class="row g-3 mb-4">
        @php
            $cards = [
                [
                    'label' => 'Total Siswa',
                    'value' => $stats['total_siswa'],
                    'icon' => 'bi-people-fill',
                    'color' => 'primary',
                ],
                [
                    'label' => 'Total Kelas',
                    'value' => $stats['total_kelas'],
                    'icon' => 'bi-door-open-fill',
                    'color' => 'success',
                ],
                [
                    'label' => 'Total Guru',
                    'value' => $stats['total_guru'],
                    'icon' => 'bi-person-video3',
                    'color' => 'info',
                ],
                [
                    'label' => 'Mata Pelajaran',
                    'value' => $stats['total_mapel'],
                    'icon' => 'bi-journal-bookmark-fill',
                    'color' => 'warning',
                ],
                [
                    'label' => 'Kelas Mengajar',
                    'value' => $stats['total_kelas_mengajar'],
                    'icon' => 'bi-diagram-3-fill',
                    'color' => 'secondary',
                ],
                [
                    'label' => 'Tugas Aktif',
                    'value' => $stats['total_tugas_aktif'],
                    'icon' => 'bi-clipboard-check-fill',
                    'color' => 'danger',
                ],
            ];
        @endphp
        @foreach ($cards as $c)
            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi {{ $c['icon'] }} text-{{ $c['color'] }} fs-3"></i>
                        <h3 class="fw-bold mt-2 mb-0">{{ $c['value'] }}</h3>
                        <small class="text-muted">{{ $c['label'] }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Kelas Mengajar Terbaru</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengampuTerbaru as $p)
                        <tr>
                            <td>{{ $p->guru->name ?? '-' }}</td>
                            <td>{{ $p->mataPelajaran->nama ?? '-' }}</td>
                            <td>{{ $p->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $p->tahun_ajaran }}</td>
                            <td>{{ $p->semester }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
