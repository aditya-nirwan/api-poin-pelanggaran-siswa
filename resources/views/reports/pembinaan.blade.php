<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Laporan Pembinaan</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #eee; }
    .title { font-size: 16px; font-weight: bold; text-align: center; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="title">Laporan Pembinaan Siswa</div>

  <p><strong>Periode:</strong> {{ $periode }}</p>
  <p><strong>Status:</strong> {{ $status }}</p>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Sanksi</th>
        <th>Tanggal</th>
        <th>Guru</th>
        <th>Status</th>
        <th>Catatan</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $i => $d)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $d->nama_siswa }}</td>
          <td>{{ $d->kelas }}</td>
          <td>{{ $d->sanksi }}</td>
          <td>{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d M Y') }}</td>
          <td>{{ $d->guru }}</td>
          <td>{{ $d->status }}</td>
          <td>{{ $d->catatan }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center;">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
