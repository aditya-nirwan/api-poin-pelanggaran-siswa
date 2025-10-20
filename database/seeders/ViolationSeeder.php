<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Violation;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationCategory;
use Illuminate\Support\Carbon;

class ViolationSeeder extends Seeder
{
  public function run(): void
  {
    $students = Student::all();
    $teachers = User::where('role', 'guru')->get();
    $categories = ViolationCategory::all();

    foreach ($students as $student) {
      for ($i = 0; $i < 3; $i++) {
        Violation::create([
          'student_id'   => $student->id,
          'teacher_id'   => $teachers->random()->id,
          'category_id'  => $categories->random()->id,
          'tanggal'      => Carbon::now()->subDays(rand(0, 30)),
          'poin'         => rand(5, 50),
          'catatan'      => 'Pelanggaran dicatat oleh guru piket',
        ]);
      }
    }
  }
}
