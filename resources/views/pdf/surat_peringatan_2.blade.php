<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; line-height: 1.6; }
        .kop { text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { float: left; width: 80px; height: 80px; margin-right: 20px; }
        .clear { clear: both; }
        .center { text-align: center; }
        .kepada {
            line-height: 0.8em;
        }
    </style>
</head>
<body>

    <div class="kop">
        <img src="{{ public_path('storage/logo-smk.png') }}" class="logo">
        <div class="header-text">
            <h2 style="margin-bottom: 5px;">SMK PEMBANGUNAN AMPEL BOYOLALI</h2>
            <p style="margin: 0;">Program Keahlian :</p>
            <p style="margin: 0;">Akuntansi | Rekayasa Perangkat Lunak | Teknik Sepeda Motor</p>
            <p style="margin: 0;">Jl. Baru, Kaligentong, Ampel, Boyolali, Jawa Tengah 57312</p>
            <p style="margin: 0;">HP 08122634131 | Email: info@smkboyolali.sch.id</p>
        </div>
    </div>

    <div class="center">
        <h3>SURAT PERINGATAN 2</h3>
        <p>Nomor: {{ $guidance->id }}/SP2/SMK-PA/{{ date('m') }}/{{ date('Y') }}</p>
    </div>

    <div class="kepada">
        <p>Kepada Yth:</p>
        <p><strong>Orang Tua/Wali dari:</strong></p>
        <p>Nama Siswa: {{ $guidance->student->user->nama}}</p>
        <p>Kelas: {{ $guidance->student->kelas }}</p>
        <p>Di Tempat</p>
    </div>

    <p class="mt-2">Dengan hormat,</p>
    <p>
        Berdasarkan data pelanggaran tata tertib di SMK Pembangunan Ampel, siswa atas nama tersebut di atas telah melakukan pelanggaran berulang
        dengan akumulasi poin mencapai <strong>{{ $guidance->student->total_poin }} poin</strong>, dan telah melewati batas yang ditentukan untuk <strong>Surat Peringatan 2 (SP2)</strong>.
    </p>

    <p>Adapun tindak lanjut dari pelanggaran ini adalah:</p>
    <ol>
        <li>Surat peringatan tertulis bermaterai.</li>
        <li>Surat ditandatangani oleh siswa, wali kelas, guru BK, dan Wakil Kepala Sekolah.</li>
        <li>Dilakukan pembinaan lanjutan bersama orang tua/wali.</li>
    </ol>

    <p>
        Kami mohon orang tua/wali hadir ke sekolah untuk proses penandatanganan dan pembinaan lebih lanjut.
    </p>

    <p>
        Demikian surat ini disampaikan. Semoga kerja sama antara sekolah dan orang tua dapat meningkatkan kedisiplinan dan tanggung jawab siswa.
    </p>

    <br><br>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left; width: 60%;"></td>
            <td style="text-align: center;">
                Boyolali, {{\Carbon\Carbon::parse($guidance->tanggal)->translatedFormat('d F Y') }}<br>
                Kepala Sekolah,<br><br><br><br>
                <strong><u>{{ $kepsek->nama .' S.Ag' ?? 'Nama Kepala Sekolah' }}</u></strong>
            </td>
        </tr>
    </table>

</body>
</html>
