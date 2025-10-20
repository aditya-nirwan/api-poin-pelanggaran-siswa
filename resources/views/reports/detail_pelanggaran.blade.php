<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Pelanggaran</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .judul {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .info {
            margin-bottom: 8px;
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
    </style>
</head>
<body>
    <div class="judul">Laporan Detail Pelanggaran Siswa</div>

    @php
        use Carbon\Carbon;
        $formatTanggal = fn($tgl) => Carbon::parse($tgl)->translatedFormat('d F Y');
    @endphp

    <div class="info"><strong>Nama:</strong> {{ $data->nama }}</div>
    <div class="info"><strong>Kelas:</strong> {{ $data->kelas }}</div>
    <div class="info"><strong>Total Poin:</strong> {{ $data->total_poin }}</div>

    @if($tanggal_awal && $tanggal_akhir)
        <div class="info"><strong>Periode:</strong> {{ $formatTanggal($tanggal_awal) }} s/d {{ $formatTanggal($tanggal_akhir) }}</div>
    @endif

    @if(count($data->pelanggaran) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Tanggal</th>
                    <th style="width: 45%;">Jenis Pelanggaran</th>
                    <th style="width: 20%;">Kategori</th>
                    <th style="width: 10%;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->pelanggaran as $p)
                    <tr>
                        <td>{{ $formatTanggal($p->tanggal) }}</td>
                        <td>{{ $p->jenis_pelanggaran }}</td>
                        <td>{{ $p->kategori }}</td>
                        <td>{{ $p->poin }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada pelanggaran pada periode ini.</p>
    @endif
</body>
</html>
