<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guidance;
use App\Models\Student;
use App\Models\User;
use App\Models\Sanction;
use Illuminate\Support\Carbon;

class GuidanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $teachers = User::where('role', 'guru')->get();
        $sanctions = Sanction::all();

        foreach ($students as $student) {
            for ($i = 0; $i < 2; $i++) {
                Guidance::create([
                    'student_id'  => $student->id,
                    'teacher_id'  => $teachers->random()->id,
                    'sanction_id' => $sanctions->random()->id,
                    'tanggal'     => Carbon::now()->subDays(rand(1, 20)),
                    'catatan'     => 'Pembinaan karena akumulasi poin pelanggaran',
                    'status'      => rand(1, 4),
                ]);
            }
        }
    }
}
