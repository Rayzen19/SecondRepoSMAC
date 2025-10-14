<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Strand;
use App\Models\Subject;
use App\Models\StrandSubject;

class StrandSubjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        $strands = Strand::all();
        $subjects = Subject::inRandomOrder()->take(8)->get();
        foreach ($strands as $strand) {
            foreach ($subjects->random( min(4, $subjects->count()) ) as $subject) {
                StrandSubject::firstOrCreate([
                    'strand_id' => $strand->id,
                    'subject_id' => $subject->id,
                ], [
                    'semestral_period' => $subject->semester,
                    'written_works_percentage' => 20,
                    'performance_tasks_percentage' => 60,
                    'quarterly_assessment_percentage' => 20,
                    'is_active' => true,
                ]);
            }
        }
    }
}
