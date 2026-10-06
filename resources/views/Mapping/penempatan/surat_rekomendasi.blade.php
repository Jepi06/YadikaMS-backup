<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Rekomendasi PKL</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 13px; color: #111; line-height: 1.5; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 20px; }
        .kop h2, .kop h4 { margin: 2px 0; }
        .judul { text-align: center; text-decoration: underline; font-weight: bold; margin: 20px 0; }
        .no-surat { text-align: center; margin-top: -15px; margin-bottom: 20px; }
        table.data { width: 100%; border-collapse: collapse; margin: 10px 0 20px 0; }
        table.data td { padding: 3px 6px; vertical-align: top; }
        table.data td.label { width: 190px; }
        .ttd { width: 100%; margin-top: 50px; }
        .ttd td { text-align: center; vertical-align: top; padding: 0 20px; }
        .ttd .nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>SMK YADIKA SOREANG</h2>
        <h4>Program Keahlian Pengembangan Perangkat Lunak dan Gim (PPLG)</h4>
        <small>Soreang, Kabupaten Bandung, Jawa Barat</small>
    </div>

    <div class="judul">SURAT REKOMENDASI PRAKTIK KERJA LAPANGAN (PKL)</div>
    <div class="no-surat">Nomor: {{ $nomorSurat }}</div>

    <p>Yang bertanda tangan di bawah ini, Kepala Program Keahlian PPLG SMK Yadika Soreang, dengan ini
        merekomendasikan siswa berikut untuk melaksanakan Praktik Kerja Lapangan (PKL):</p>

    <table class="data">
        <tr><td class="label">Nama Siswa</td><td>: {{ $penempatan->siswa->nama }}</td></tr>
        <tr><td class="label">NIS</td><td>: {{ $penempatan->siswa->nis }}</td></tr>
        <tr><td class="label">Kelas / Jurusan</td><td>: {{ $penempatan->siswa->kelas->nama_kelas ?? '-' }} / {{ $penempatan->siswa->kelas->jurusan->nama ?? '-' }}</td></tr>
        <tr><td class="label">Tempat PKL</td><td>: {{ $penempatan->tempatPkl->nama_tempat }}</td></tr>
        <tr><td class="label">Alamat Tempat PKL</td><td>: {{ $penempatan->tempatPkl->alamat }}</td></tr>
        <tr><td class="label">Guru Pembimbing</td><td>: {{ $penempatan->guruPembimbing->nama }}</td></tr>
        <tr><td class="label">Periode PKL</td><td>: {{ $penempatan->tanggal_mulai->translatedFormat('d F Y') }} s.d. {{ $penempatan->tanggal_selesai->translatedFormat('d F Y') }}</td></tr>
        <tr><td class="label">Tahun Ajaran</td><td>: {{ $penempatan->tahun_ajaran }}</td></tr>
    </table>

    <p>Surat ini diterbitkan setelah pengajuan disetujui oleh Wali Kelas, Guru BK, Kesiswaan, dan Kepala Jurusan,
        untuk dipergunakan sebagaimana mestinya.</p>

    <table class="ttd">
        <tr>
            <td></td>
            <td>
                Soreang, {{ $tanggalCetak }}<br>
                Kepala Program Keahlian PPLG
                <div class="nama">{{ $penempatan->approverKepalaJurusan->name ?? '________________' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
