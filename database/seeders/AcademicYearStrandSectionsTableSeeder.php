<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Strand;
use App\Models\Section;
use App\Models\AcademicYearStrandSection;

class AcademicYearStrandSectionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $ay = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        if (!$ay) return;
        $sections = Section::inRandomOrder()->get();
        $i = 0;
        foreach (Strand::all() as $strand) {
            $section = $sections[$i % max(1, $sections->count())] ?? Section::factory()->create(['grade' => 'G-11']);
            $i++;
            AcademicYearStrandSection::firstOrCreate([
                'academic_year_id' => $ay->id,
                'strand_id' => $strand->id,
                'section_id' => $section->id,
            ], [
                'is_active' => true,
            ]);
        }
    }
}
