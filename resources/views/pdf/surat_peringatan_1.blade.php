<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan Resmi</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .kop { 
            text-align: center; 
            border-bottom: 2px solid black; 
            padding-bottom: 10px; 
            margin-bottom: 20px;
            position: relative;
        }
        .logo { 
            position: absolute;
            left: 10px;
            top: 60px;
            width: 80px;
            height: 80px;
        }
        .header-text {
            margin-left: 100px;
            margin-right: 100px;
        }
        .content {
            margin-top: 30px;
        }
        table.detail {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table.detail tr td {
            padding: 5px 0;
            vertical-align: top;
        }
        table.detail tr td:first-child {
            width: 25%;
        }
        .footer {
            margin-top: 50px;
        }
        .signature {
            float: right;
            text-align: center;
            width: 300px;
        }
        .underline {
            text-decoration: underline;
        }
        .center {
            text-align: center;
        }
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

    <div class="content">
        <div class="center">
            <h3>SURAT PERINGATAN 1</h3>
            <p>Nomor: {{ $guidance->id }}/SP1/SMK-PA/{{ date('m') }}/{{ date('Y') }}</p>
        </div>

        <p>Kepada Yth:</p>
        <div class="kepada">
            <p><strong>Orang Tua/Wali dari:</strong></p>
            <p>Nama Siswa   : {{ $guidance->student->user->nama}}</p>
            <p>Kelas        : {{ $guidance->student->kelas }}</p>
            <p>Di Tempat</p>
        </div>

        <p class="mt-2">Dengan hormat,</p>
        <p>
            Bersama surat ini, kami sampaikan bahwa yang bersangkutan telah melakukan pelanggaran terhadap tata tertib sekolah di <strong>SMK Pembangunan Ampel</strong>,
            dengan akumulasi poin pelanggaran mencapai <strong>{{ $guidance->student->total_poin }} poin</strong>.
            Berdasarkan ketentuan yang berlaku di sekolah, akumulasi poin tersebut telah memasuki kategori pelanggaran berat yang dikenai <strong>Surat Peringatan 1 (SP1)</strong>.
        </p>

        <p>Adapun tindak lanjut dari pelanggaran ini adalah:</p>
        <ol>
            <li>Peringatan tertulis kepada siswa.</li>
            <li>Menghadirkan orang tua/wali ke sekolah untuk konsultasi dan pembinaan.</li>
            <li>Siswa diminta membuat surat pernyataan tidak mengulangi pelanggaran.</li>
        </ol>

        <p>
            Demikian surat peringatan ini dibuat untuk menjadi perhatian dan tindak lanjut. Atas kerja sama dan perhatian Bapak/Ibu, kami ucapkan terima kasih.
        </p>
    </div>

    <div class="footer">
        <div class="signature">
            <p>
                Boyolali, {{ \Carbon\Carbon::parse($guidance->created_at)->translatedFormat('d F Y') }}<br>
                Kepala Sekolah,<br><br><br><br>
                <strong><u>{{ $kepsek->nama . ' S.Ag' ?? 'Dr. Ahmad Setyawan, M.Pd.' }}</u></strong><br>
            </p>
        </div>
    </div>

</body>
</html>
