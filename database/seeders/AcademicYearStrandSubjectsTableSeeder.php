<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYearStrandAdviser;
use App\Models\AcademicYearStrandSubject;

class AcademicYearStrandSubjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        $ay = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        if (!$ay) return;
        $advisers = AcademicYearStrandAdviser::where('academic_year_id', $ay->id)->get();
        $subjects = Subject::inRandomOrder()->get();
        $teachers = Teacher::inRandomOrder()->get();
        $ti = 0;
        foreach ($advisers as $adviser) {
            $pick = $subjects->random(min(2, max(1, $subjects->count())));
            foreach ($pick as $subject) {
                $teacher = $teachers[$ti % max(1, $teachers->count())] ?? Teacher::factory()->create();
                $ti++;
                AcademicYearStrandSubject::firstOrCreate([
                    'academic_year_strand_adviser_id' => $adviser->id,
                    'academic_year_id' => $ay->id,
                    'strand_id' => $adviser->strand_id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                ], [
                    'written_works_percentage' => 20,
                    'performance_tasks_percentage' => 60,
                    'quarterly_assessment_percentage' => 20,
                ]);
            }
        }
    }
}
