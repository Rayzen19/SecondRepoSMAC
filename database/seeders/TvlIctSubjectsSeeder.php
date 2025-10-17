<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;
use Illuminate\Database\Seeder;

class TvlIctSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds all subjects for TVL-ICT strand (Core, Applied, and Specialized)
     */
    public function run(): void
    {
        // Find the TVL-ICT strand
        $tvlIctStrand = Strand::where('code', 'TVL-ICT')->first();
        
        if (!$tvlIctStrand) {
            $this->command->error('TVL-ICT strand not found. Please run StrandsTableSeeder first.');
            return;
        }

        // Core Curriculum Subjects
        $coreSubjects = [
            ['code' => 'ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st'],
            ['code' => 'FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st'],
            ['code' => 'LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st'],
            ['code' => 'MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st'],
            ['code' => 'MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st'],
            ['code' => 'MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'SCI103', 'name' => 'Earth Science (STEM)', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'PHILO101', 'name' => 'Introduction to Philosophy of the Human Person', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st'],
            ['code' => 'PD101', 'name' => 'Personal Development / Pansariling Kaunlaran', 'units' => 3, 'semester' => '1st'],
            ['code' => 'SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'DRR101', 'name' => 'Disaster Readiness and Risk Reduction', 'units' => 3, 'semester' => '2nd'],
        ];

        // Applied Track Subjects
        $appliedSubjects = [
            ['code' => 'ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '1st'],
            ['code' => 'RES101', 'name' => 'Practical Research 1', 'units' => 3, 'semester' => '1st'],
            ['code' => 'RES102', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'FIL201', 'name' => 'Filipino sa Piling Larang (Tech Voc.)', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ICT101', 'name' => 'Empowerment Technologies (for the Strand)', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd'],
        ];

        // Specialized Subjects (TVL–ICT)
        $specializedSubjects = [
            ['code' => 'ICT201', 'name' => 'Computer Programming (JAVA)', 'units' => 4, 'semester' => '1st'],
            ['code' => 'ICT202', 'name' => 'Computer Programming (.NET TECHNOLOGY)', 'units' => 4, 'semester' => '2nd'],
            ['code' => 'ICT301', 'name' => 'Work Immersion / Research / Career Advocacy / Culminating Activity', 'units' => 4, 'semester' => '2nd'],
        ];

        $this->command->info('Creating Core Curriculum Subjects...');
        $this->createSubjectsAndLink($coreSubjects, 'core', $tvlIctStrand);

        $this->command->info('Creating Applied Track Subjects...');
        $this->createSubjectsAndLink($appliedSubjects, 'applied', $tvlIctStrand);

        $this->command->info('Creating Specialized Subjects (TVL-ICT)...');
        $this->createSubjectsAndLink($specializedSubjects, 'specialized', $tvlIctStrand);

        $this->command->info('✅ All TVL-ICT subjects created and linked successfully!');
    }

    /**
     * Create subjects and link them to the TVL-ICT strand
     */
    private function createSubjectsAndLink(array $subjects, string $type, Strand $strand): void
    {
        foreach ($subjects as $subjectData) {
            // Create or update the subject
            $subject = Subject::updateOrCreate(
                ['code' => $subjectData['code']],
                [
                    'name' => $subjectData['name'],
                    'description' => $subjectData['description'] ?? null,
                    'units' => $subjectData['units'],
                    'type' => $type,
                    'semester' => $subjectData['semester'],
                ]
            );

            // Link subject to TVL-ICT strand
            StrandSubject::updateOrCreate(
                [
                    'strand_id' => $strand->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'semestral_period' => $subjectData['semester'],
                    'written_works_percentage' => 20,
                    'performance_tasks_percentage' => 60,
                    'quarterly_assessment_percentage' => 20,
                    'is_active' => true,
                ]
            );

            $this->command->line("  ✓ {$subjectData['code']} - {$subjectData['name']}");
        }
    }
}
