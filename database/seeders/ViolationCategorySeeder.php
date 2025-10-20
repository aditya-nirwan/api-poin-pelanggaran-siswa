<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ViolationCategory;
use Illuminate\Database\Seeder;

class ViolationCategorySeeder extends Seeder
{
    public function run(): void
    {
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Pakaian',
            'jenis_pelanggaran' => 'Mengenakan seragam tidak sesuai dengan ketentuan (baju, celana/rok, kaos kaki, sepatu, ikat pinggang)',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Pakaian',
            'jenis_pelanggaran' => 'Tidak memakai atribut dan atau mengubah ketentuan (osis, dasi, lokasi sekolah, topi pada waktu upacara)',
            'poin' => 5,
        ]);

        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Terlambat mengikuti pelajaran',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Tidak masuk tanpa izin',
            'poin' => 10,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Terlambat atau tidak mengikuti upacara',
            'poin' => 5,
        ]);

        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Tidak mengikuti salah satu mapel tanpa izin',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Meninggalkan buku paket di sekolah',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Menyontek',
            'poin' => 5,
        ]);

        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Melompati pagar/jendela',
            'poin' => 10,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Kegiatan',
            'jenis_pelanggaran' => 'Membuang sampah tidak pada tempatnya',
            'poin' => 5,
        ]);

        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Sikap',
            'jenis_pelanggaran' => 'Tidak melaksanakan tugas piket kelas sesuai jadwal',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Sikap',
            'jenis_pelanggaran' => 'Membuat gaduh di kelas',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Sikap',
            'jenis_pelanggaran' => 'Mengoperasikan gawai digital saat KBM tanpa seizin guru',
            'poin' => 15,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Sikap',
            'jenis_pelanggaran' => 'Mencoret/vandalisme di sembarang tempat di lingkungan sekolah',
            'poin' => 15,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Sikap',
            'jenis_pelanggaran' => 'Merusak fasilitas sekolah',
            'poin' => 20,
        ]);

        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Estetika',
            'jenis_pelanggaran' => 'Putra memakai kalung dan atau gelang tangan dan atau cincin dan atau anting-anting',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Estetika',
            'jenis_pelanggaran' => 'Putra dan atau putri memakai gelang kaki',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Estetika',
            'jenis_pelanggaran' => 'Putra bertindik',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Estetika',
            'jenis_pelanggaran' => 'Putri bertindik lebih dari satu',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Estetika',
            'jenis_pelanggaran' => 'Mentato bagian tubuh baik tato tetap maupun sementara',
            'poin' => 15,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Estetika',
            'jenis_pelanggaran' => 'Putra berambut panjang melebihi ketentuan',
            'poin' => 5,
        ]);
        ViolationCategory::create([
            'kategori' => 'Ringan',
            'sub' => 'Estetika',
            'jenis_pelanggaran' => 'Potongan rambut tidak pantas',
            'poin' => 10,
        ]);


        // Sedang
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Membawah senjata tajam, menipu, atau mencuri',
            'poin' => 25,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Memeras uang ataupun benda berharga lain',
            'poin' => 25,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Berjudi dan atau membawa alat judi',
            'poin' => 25,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Mengendarai motor tanpa mematuhi peraturan lalu lintas',
            'poin' => 25,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Membawa rokok dan atau merokok di sekolah dan atau masih memakai seragam sekolah',
            'poin' => 20,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Mengakses situs porno di internet',
            'poin' => 25,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Mengendarai motor yang tidak standar',
            'poin' => 25,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Mengundang orang luar ke sekolah tanpa izin',
            'poin' => 20,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Berbuat asusila (pelecehan seksual, baik secara fisik maupun verbal)',
            'poin' => 100,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Melecehkan guru atau karyawan',
            'poin' => 50,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Berbuat membahayakan teman',
            'poin' => 100,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Mencemarkan nama baik sekolah',
            'poin' => 50,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Hubungan antar siswa',
            'jenis_pelanggaran' => 'Berkelahi',
            'poin' => 50,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Hubungan antar siswa',
            'jenis_pelanggaran' => 'Mengancam',
            'poin' => 50,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Hubungan antar siswa',
            'jenis_pelanggaran' => 'Kekerasan fisik',
            'poin' => 75,
        ]);
        ViolationCategory::create([
            'kategori' => 'Sedang',
            'sub' => 'Hubungan antar siswa',
            'jenis_pelanggaran' => 'Tawuran dengan pihak lain',
            'poin' => 100,
        ]);

        // Berat
        ViolationCategory::create([
            'kategori' => 'Berat',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Membawa, mengedarkan, atau menggunakan napza ',
            'poin' => 250,
        ]);
        ViolationCategory::create([
            'kategori' => 'Berat',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Membawa, mengoplos, atau mengonsumsi minuman beralkohol dan sejenisnya',
            'poin' => 250,
        ]);
        ViolationCategory::create([
            'kategori' => 'Berat',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Memfasilitasi tindakan zina dan atau berzina',
            'poin' => 250,
        ]);
        ViolationCategory::create([
            'kategori' => 'Berat',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Terbukti menikah selama masih berstatus sebagai pelajar SMK Pembangunan Ampel',
            'poin' => 250,
        ]);
        ViolationCategory::create([
            'kategori' => 'Berat',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Berurusan dengan aparat pemerintah atau kepolisian karena masalah kriminal atau asusila',
            'poin' => 250,
        ]);
        ViolationCategory::create([
            'kategori' => 'Berat',
            'sub' => 'Etika',
            'jenis_pelanggaran' => 'Mengancam, menganiaya guru atau karyawan',
            'poin' => 250,
        ]);
    }
}
