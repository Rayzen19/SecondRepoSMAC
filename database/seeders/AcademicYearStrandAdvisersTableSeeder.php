<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Strand;
use App\Models\Teacher;
use App\Models\AcademicYearStrandAdviser;

class AcademicYearStrandAdvisersTableSeeder extends Seeder
{
    public function run(): void
    {
        $ay = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        if (!$ay) return;
        $teachers = Teacher::inRandomOrder()->get();
        $i = 0;
        foreach (Strand::all() as $strand) {
            $teacher = $teachers[$i % max(1, $teachers->count())] ?? Teacher::factory()->create();
            $i++;
            AcademicYearStrandAdviser::firstOrCreate([
                'academic_year_id' => $ay->id,
                'strand_id' => $strand->id,
            ], [
                'teacher_id' => $teacher->id,
            ]);
        }
    }
}
