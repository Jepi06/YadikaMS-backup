<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring PKL</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: #0f172a;
            color: white;
        }

        .sidebar label {
            margin-bottom: 6px;
            font-weight: 500;
        }

        .card-custom {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        table th {
            background: #0f172a !important;
            color: white !important;
            vertical-align: middle;
        }

        table td {
            vertical-align: middle;
        }

        .form-select {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 10px;
        }

        .badge {
            padding: 8px 12px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">

        <div class="row">

            <!-- SIDEBAR -->
            <div class="col-md-2 sidebar p-4">

                <h3 class="fw-bold mb-4">
                    Monitoring PKL
                </h3>

                <form method="GET">

                    <!-- FILTER JURUSAN -->
                    <div class="mb-3">

                        <label>Jurusan</label>

                        <select name="jurusan" class="form-select">

                            <option value="">
                                Semua
                            </option>

                            @foreach ($jurusan as $j)
                                <option value="{{ $j->id }}"
                                    {{ request('jurusan') == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <!-- FILTER KELAS -->
                    <div class="mb-3">

                        <label>Kelas</label>

                        <select name="kelas" class="form-select">

                            <option value="">
                                Semua
                            </option>

                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <button class="btn btn-primary w-100">
                        Filter
                    </button>

                </form>

                {{-- BARU: tombol untuk siswa yang mau mengajukan tempat PKL.
                     Diarahkan ke form pengajuan (SiswaPklController@create).
                     Kalau siswa belum login, guard 'pkl' akan otomatis
                     mengarahkan ke halaman login PKL terlebih dahulu. --}}
                <div class="ajukan-area" style="margin-top: 16px">
                    <a href="{{ route('pkl.pengajuan.create') }}" class="btn btn-success w-100 fw-semibold">
                        📄 Ajukan Tempat PKL
                    </a>
                </div>

                <div class="login-area" style="margin-top : 12px">

                    <a href="{{ route('pkl.login') }}" class="btn btn-light w-100 fw-semibold">
                        🔐 Login Sistem PKL
                    </a>
                </div>


            </div>

            <!-- CONTENT -->
            <div class="col-md-10 p-4">

                {{-- BARU: TABEL STATUS BELUM DISETUJUI --}}
                <div class="card card-custom mb-4">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <div>

                                <h3 class="fw-bold mb-1">
                                    Status Pengajuan Belum Disetujui
                                </h3>

                                <p class="text-muted mb-0">
                                    Siswa yang pengajuan PKL-nya masih dalam proses approval, beserta tahap yang
                                    sedang ditunggu.
                                </p>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Tempat PKL</th>
                                        <th width="220">Menunggu Approval Dari</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($belumApproved as $item)
                                        <tr>

                                            <td>
                                                {{ $belumApproved->firstItem() + $loop->index }}
                                            </td>

                                            <td>
                                                {{ $item->siswa->nama ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->siswa->kelas->jurusan->nama ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->tempatPkl->nama_tempat ?? '-' }}
                                            </td>

                                            <td>
                                                @php
                                                    // Urutan tahap approval: Wali Kelas -> Guru BK -> Kesiswaan -> Kepala Jurusan
                                                    $tahapan = [
                                                        'status_wali_kelas' => 'Wali Kelas',
                                                        'status_guru_bk' => 'Guru BK',
                                                        'status_kesiswaan' => 'Kesiswaan',
                                                        'status_kepala_jurusan' => 'Kepala Jurusan',
                                                    ];
                                                @endphp

                                                <div class="d-flex flex-column gap-1">
                                                    @foreach ($tahapan as $kolom => $label)
                                                        @php
                                                            $statusTahap = $item->{$kolom} ?? 'pending';

                                                            $warna = match ($statusTahap) {
                                                                'approved' => 'bg-success',
                                                                'rejected' => 'bg-danger',
                                                                default => 'bg-warning text-dark',
                                                            };

                                                            $keterangan = match ($statusTahap) {
                                                                'approved' => 'Disetujui',
                                                                'rejected' => 'Ditolak',
                                                                default => 'Menunggu',
                                                            };
                                                        @endphp

                                                        <span
                                                            class="badge {{ $warna }} d-flex justify-content-between align-items-center">
                                                            <span>{{ $label }}</span>
                                                            <span class="ms-2">{{ $keterangan }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="6" class="text-center py-4">
                                                Tidak ada pengajuan yang sedang berjalan
                                            </td>

                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                        <div class="mt-3">
                            {{ $belumApproved->links() }}
                        </div>

                    </div>

                </div>

                <div class="card card-custom">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <div>

                                <h3 class="fw-bold mb-1">
                                    Data Siswa PKL Disetujui
                                </h3>

                                <p class="text-muted mb-0">
                                    Data siswa yang telah disetujui seluruh approval.
                                </p>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Guru Pembimbing</th>
                                        <th>Tempat PKL</th>
                                        <th width="120">Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($data as $item)
                                        <tr>

                                            <td>
                                                {{ $data->firstItem() + $loop->index }}
                                            </td>

                                            <td>
                                                {{ $item->siswa->nama ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->siswa->kelas->jurusan->nama ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->guruPembimbing->nama ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->tempatPkl->nama_tempat ?? '-' }}
                                            </td>

                                            <td>
                                                <span class="badge bg-success">
                                                    Approved
                                                </span>
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="7" class="text-center py-4">
                                                Data kosong
                                            </td>

                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                        <!-- PAGINATION -->
                        <div class="mt-3">
                            {{ $data->links() }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
