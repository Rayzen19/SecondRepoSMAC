<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;
use Illuminate\Database\Seeder;

class RestoreDeletedSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Restoring deleted subjects...');

        // Get all strands
        $strands = Strand::all()->keyBy('code');

        // List of all subjects that should exist (based on the original seeder)
        // We'll check each one and recreate if missing
        
        $allSubjects = $this->getAllSubjectDefinitions();
        
        $restored = 0;
        foreach ($allSubjects as $strandCode => $categories) {
            $strand = $strands->get($strandCode);
            if (!$strand) {
                continue;
            }

            foreach ($categories as $type => $subjects) {
                foreach ($subjects as $subjectData) {
                    // Check if subject exists
                    $exists = Subject::where('code', $subjectData['code'])->exists();
                    
                    if (!$exists) {
                        // Restore the subject
                        $subject = Subject::create([
                            'code' => $subjectData['code'],
                            'name' => $subjectData['name'],
                            'units' => $subjectData['units'],
                            'type' => $type,
                            'semester' => $subjectData['semester'],
                        ]);

                        // Link to strand
                        StrandSubject::create([
                            'strand_id' => $strand->id,
                            'subject_id' => $subject->id,
                            'semestral_period' => $subjectData['semester'],
                            'written_works_percentage' => 20,
                            'performance_tasks_percentage' => 60,
                            'quarterly_assessment_percentage' => 20,
                            'is_active' => true,
                        ]);

                        $restored++;
                        $this->command->info("✓ Restored: {$subject->code} - {$subject->name}");
                    }
                }
            }
        }

        if ($restored > 0) {
            $this->command->info("✅ Restored {$restored} subject(s) successfully!");
        } else {
            $this->command->info('✓ No subjects need to be restored.');
        }
    }

    /**
     * Get all subject definitions by strand
     */
    private function getAllSubjectDefinitions(): array
    {
        return [
            'STEM' => [
                'core' => [
                    ['code' => 'STEM-ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-SCI103', 'name' => 'Earth Science', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-PHILO101', 'name' => 'Introduction to Philosophy of the Human Person', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st'],
                    ['code' => 'STEM-PD101', 'name' => 'Personal Development', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-DRR101', 'name' => 'Disaster Readiness and Risk Reduction', 'units' => 3, 'semester' => '2nd'],
                ],
                'applied' => [
                    ['code' => 'STEM-ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-RES101', 'name' => 'Practical Research 1', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-RES102', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-FIL201', 'name' => 'Filipino sa Piling Larang (Akademik)', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd'],
                ],
                'specialized' => [
                    ['code' => 'STEM-PCHEM101', 'name' => 'Pre-Calculus', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-CHEM101', 'name' => 'General Chemistry 1', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-CHEM102', 'name' => 'General Chemistry 2', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-BIO101', 'name' => 'General Biology 1', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-BIO102', 'name' => 'General Biology 2', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-PHY101', 'name' => 'General Physics 1', 'units' => 3, 'semester' => '1st'],
                    ['code' => 'STEM-PHY102', 'name' => 'General Physics 2', 'units' => 3, 'semester' => '2nd'],
                    ['code' => 'STEM-CAL101', 'name' => 'Basic Calculus', 'units' => 3, 'semester' => '2nd'],
                ],
            ],
            // Add other strands as needed...
        ];
    }
}
