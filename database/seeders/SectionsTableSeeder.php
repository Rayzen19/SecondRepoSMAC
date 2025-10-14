<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['A','B','C','D','E'];
        $grades = ['G-11','G-12'];
        foreach ($grades as $grade) {
            foreach ($names as $name) {
                Section::updateOrCreate([
                    'name' => $name,
                    'grade' => $grade,
                ], []);
            }
        }
    }
}
