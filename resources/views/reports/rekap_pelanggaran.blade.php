<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Pelanggaran Siswa</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .judul {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #555;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        .siswa-info {
            margin-top: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="judul">Rekapitulasi Pelanggaran Siswa</div>

    @php
        use Carbon\Carbon;
        $tanggalFormat = fn($tgl) => Carbon::parse($tgl)->translatedFormat('d F Y');
        $no = 1;
    @endphp

    @if($kelas)
        <p><strong>Kelas:</strong> {{ $kelas }}</p>
    @endif
    <p>
        <strong>Periode:</strong>
        {{ $tanggalFormat($tanggal_awal) }} s/d {{ $tanggalFormat($tanggal_akhir) }}
    </p>

    @foreach($data as $index => $siswa)
        @if(count($siswa['pelanggaran']) > 0)
            <div class="siswa-info">
                {{ $no++ }}. {{ $siswa['nama'] }} ({{ $siswa['kelas'] }}) - Total Poin: {{ $siswa['total_poin'] }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Tanggal</th>
                        <th style="width: 40%;">Jenis Pelanggaran</th>
                        <th style="width: 20%;">Kategori</th>
                        <th style="width: 15%;">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa['pelanggaran'] as $p)
                        <tr>
                            <td>{{ Carbon::parse($p['tanggal'])->translatedFormat('d F Y') }}</td>
                            <td>{{ $p['jenis_pelanggaran'] }}</td>
                            <td>{{ $p['kategori'] }}</td>
                            <td>{{ $p['poin'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
</body>
</html>
