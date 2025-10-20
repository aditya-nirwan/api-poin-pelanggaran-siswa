<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('school_years')->insert([
            [
                'tahun_awal' => 2024,
                'tahun_akhir' => 2025,
                'semester_ganjil_start' => '2024-07-01',
                'semester_ganjil_end' => '2024-12-31',
                'semester_genap_start' => '2025-01-01',
                'semester_genap_end' => '2025-06-30',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_awal' => 2025,
                'tahun_akhir' => 2026,
                'semester_ganjil_start' => '2025-07-01',
                'semester_ganjil_end' => '2025-12-31',
                'semester_genap_start' => '2026-01-01',
                'semester_genap_end' => '2026-06-30',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
