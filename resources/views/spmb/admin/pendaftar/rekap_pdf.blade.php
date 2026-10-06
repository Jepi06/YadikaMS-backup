<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 0; }
        .subtitle { color: #666; margin-top: 2px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px 8px; }
        th { background: #f0f0f0; text-align: left; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background: #f7f7f7; }
    </style>
</head>
<body>
    <h2>Rekap Penerimaan Peserta Didik Baru (SPMB)</h2>
    <div class="subtitle">SMK Yadika Soreang &mdash; dicetak {{ $tanggalCetak }}</div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Jurusan</th>
                <th class="text-center">Kuota</th>
                <th class="text-center">Diterima</th>
                <th class="text-center">Sisa Kuota</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $row)
                <tr>
                    <td>{{ $row['kode'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td class="text-center">{{ $row['kuota'] }}</td>
                    <td class="text-center">{{ $row['diterima'] }}</td>
                    <td class="text-center">{{ $row['sisa_kuota'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total Diterima</td>
                <td class="text-center">{{ $totalDiterima }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
