<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
  public function run()
  {
    // Path ke file CSV (simpan di storage/app/csv/)
    $csvFile = storage_path('app/csv/data_siswa.csv');

    // Cek jika file tidak ada
    if (!file_exists($csvFile)) {
      $this->command->error("File CSV tidak ditemukan di: {$csvFile}");
      $this->command->info("Pastikan file CSV sudah ditempatkan di storage/app/csv/");
      return;
    }

    // Buka file CSV
    $file = fopen($csvFile, 'r');

    // Lewati header baris pertama
    $header = fgetcsv($file);

    // Counter untuk progress
    $counter = 0;
    $created = 0;

    // Mulai progress bar
    $this->command->info("Memulai proses impor data dari CSV...");
    $bar = $this->command->getOutput()->createProgressBar(count(file($csvFile)) - 1);

    // Proses setiap baris
    while (($data = fgetcsv($file)) !== false) {
      $counter++;

      try {
        // Validasi data minimal
        if (count($data) < 7) {
          $this->command->error("Data tidak lengkap pada baris {$counter}");
          continue;
        }

        // Buat user
        $user = User::create([
          'nama'      => trim($data[0]),
          'email'     => trim($data[1]),
          'password'  => Hash::make(trim($data[2])),
          'role'      => trim($data[3]) ?? 'siswa',
          'foto'      => trim($data[4]) ?? '',
        ]);

        // Buat student terkait
        Student::create([
          'user_id'   => $user->id,
          'kelas'     => trim($data[5]),
          'no_hp'     => trim($data[6]),
        ]);

        $created++;
      } catch (\Exception $e) {
        $this->command->error("Gagal memproses baris {$counter}: " . $e->getMessage());
        continue;
      }

      $bar->advance();
    }

    fclose($file);
    $bar->finish();

    // Tampilkan ringkasan
    $this->command->newLine(2);
    $this->command->info("Proses impor selesai!");
    $this->command->info("Total data diproses: {$counter}");
    $this->command->info("Data berhasil dibuat: {$created}");
    $this->command->info("Gagal diproses: " . ($counter - $created));
  }
}
