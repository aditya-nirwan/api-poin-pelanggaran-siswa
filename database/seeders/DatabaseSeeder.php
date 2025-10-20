<?php

namespace Database\Seeders;

use App\Models\User;

// use Database\Seeders\ViolationSeeder;
// use Database\Seeders\GuidanceSeeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin@gmail.com',
            'password' => '123123',
            'role' => 'admin',
            'foto' => 'storage/users/user.png',
        ]);
        User::create([
            'nama' => 'Adit',
            'email' => 'adit@gmail.com',
            'password' => '123123',
            'role' => 'admin',
            'foto' => 'storage/users/user.png',
        ]);
        User::create([
            'nama' => 'Wisnu Handoko',
            'email' => 'kepsek@gmail.com',
            'password' => '123123',
            'role' => 'kepsek',
            'foto' => 'storage/users/user.png',
        ]);

        // guru
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'nama' => "Guru $i",
                'email' => "guru$i@gmail.com",
                'password' => '123123',
                'role' => 'guru',
                'foto' => 'wvYf8On1M1wu4yN8NkGZqilePemYmia0sGqmO81D.png',
            ]);
        }

        $this->call([
            SchoolYearSeeder::class,
            StudentSeeder::class,
            ViolationCategorySeeder::class,
            SanctionSeeder::class,
            // ViolationSeeder::class,
            // GuidanceSeeder::class,
        ]);
    }
}
