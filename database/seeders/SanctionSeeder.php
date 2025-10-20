<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sanction;

class SanctionSeeder extends Seeder
{
  public function run(): void
  {
    Sanction::create([
      'min_poin' => 15,
      'max_poin' => 75,
      'sanksi' => "Diberikan peringatan lisan sampai tertulis dan pemberitahuan pertama.",
    ]);

    Sanction::create([
      'min_poin' => 76,
      'max_poin' => 150,
      'sanksi' => "Diberikan peringatan tertulis kedua.",
    ]);

    Sanction::create([
      'min_poin' => 151,
      'max_poin' => 250,
      'sanksi' => "Diberi peringatan tertulis dan orang tua dihadirkan ke sekolah. Siswa membuat pernyataan. (SP1)",
    ]);

    Sanction::create([
      'min_poin' => 251,
      'max_poin' => 300,
      'sanksi' => "Peringatan tertulis bermaterai yang ditandatangani siswa, wali kelas, BK, dan Waka. (SP2)",
    ]);
  }
}
