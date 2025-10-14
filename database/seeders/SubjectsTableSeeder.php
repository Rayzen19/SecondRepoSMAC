<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a handful of common subjects explicitly
        $preset = [
            ['code' => 'ENG101', 'name' => 'English Communication', 'description' => 'Basics of English communication.', 'units' => 3, 'type' => 'Core', 'semester' => '1st'],
            ['code' => 'MATH101', 'name' => 'General Mathematics', 'description' => 'Fundamentals of mathematics for senior high.', 'units' => 3, 'type' => 'Core', 'semester' => '1st'],
            ['code' => 'SCI101', 'name' => 'Earth and Life Science', 'description' => 'Introduction to earth and life sciences.', 'units' => 3, 'type' => 'Core', 'semester' => '1st'],
            ['code' => 'PE101', 'name' => 'Physical Education', 'description' => 'Physical fitness and wellness.', 'units' => 2, 'type' => 'Applied', 'semester' => '1st'],
            ['code' => 'FIL101', 'name' => 'Filipino sa Piling Larang', 'description' => 'Filipino in specific fields.', 'units' => 3, 'type' => 'Core', 'semester' => '2nd'],
            ['code' => 'ENG102', 'name' => 'Reading and Writing', 'description' => 'Reading and writing skills development.', 'units' => 3, 'type' => 'Core', 'semester' => '2nd'],
            ['code' => 'MATH102', 'name' => 'Statistics and Probability', 'description' => 'Intro to statistics and probability.', 'units' => 3, 'type' => 'Core', 'semester' => '2nd'],
            ['code' => 'SCI102', 'name' => 'General Biology', 'description' => 'Basic concepts in biology.', 'units' => 3, 'type' => 'Specialized', 'semester' => '2nd'],
        ];

        foreach ($preset as $s) {
            Subject::updateOrCreate(
                ['code' => $s['code']],
                $s
            );
        }

        // Create additional random subjects with guaranteed unique codes
        Subject::factory()->count(12)->create();
    }
}
