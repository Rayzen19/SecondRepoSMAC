<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        // Seed default user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Seed core reference data first
        $this->call([
            SubjectsTableSeeder::class,
            AcademicYearsTableSeeder::class,
            SectionsTableSeeder::class,
            StrandsTableSeeder::class,
            TeachersTableSeeder::class,
            GuardiansTableSeeder::class,
            StudentsTableSeeder::class,
            StrandSubjectsTableSeeder::class,
            AcademicYearStrandAdvisersTableSeeder::class,
            AcademicYearStrandSectionsTableSeeder::class,
            AcademicYearStrandSubjectsTableSeeder::class,
        ]);
    }
}
