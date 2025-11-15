<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;

echo "=== Creating Strand and Inserting HUMSS Subjects ===\n\n";

// First, create the HUMSS strand if it doesn't exist
$strand = Strand::firstOrCreate(
    ['code' => 'HUMSS'],
    [
        'name' => 'Humanities and Social Sciences',
        'description' => 'The Humanities and Social Sciences (HUMSS) strand is designed for learners who want to pursue college degrees in education, liberal arts, or other social science-related courses.'
    ]
);

if ($strand->wasRecentlyCreated) {
    echo "✓ Created HUMSS strand\n\n";
} else {
    echo "✓ HUMSS strand already exists\n\n";
}

echo "Strand ID: {$strand->id}\n";
echo "Strand Code: {$strand->code}\n";
echo "Strand Name: {$strand->name}\n\n";

// Define the subjects based on the image provided
$subjects = [
    // Core Subjects
    ['code' => 'HUMSS-ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-PHILO101', 'name' => 'Introduction to the Philosophy of the Human Person / Pambungad sa Pilosopiya ng Tao', 'units' => 3, 'semester' => '2nd', 'type' => 'core'],
    ['code' => 'HUMSS-PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-PD101', 'name' => 'Personal Development / Pansariling Kaunlaran', 'units' => 3, 'semester' => '1st', 'type' => 'core'],
    ['code' => 'HUMSS-SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd', 'type' => 'core'],
    ['code' => 'HUMSS-DRR101', 'name' => 'Disaster Readiness and Risk Reduction (taken instead of Earth and Life Science for those in the STEM Strand)', 'units' => 3, 'semester' => '2nd', 'type' => 'core'],
    
    // Applied Track Subjects
    ['code' => 'HUMSS-ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '2nd', 'type' => 'applied'],
    ['code' => 'HUMSS-RES101', 'name' => 'Practical Research 1', 'units' => 3, 'semester' => '2nd', 'type' => 'applied'],
    ['code' => 'HUMSS-RES102', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd', 'type' => 'applied'],
    ['code' => 'HUMSS-FIL201', 'name' => 'Filipino sa Piling Larang (Akademik)', 'units' => 3, 'semester' => '2nd', 'type' => 'applied'],
    ['code' => 'HUMSS-ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd', 'type' => 'applied'],
    ['code' => 'HUMSS-III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd', 'type' => 'applied'],
    
    // Specialized Subjects
    ['code' => 'HUMSS-CW101', 'name' => 'Creative Writing', 'units' => 3, 'semester' => '1st', 'type' => 'specialized'],
    ['code' => 'HUMSS-CW102', 'name' => 'Creative Nonfiction', 'units' => 3, 'semester' => '2nd', 'type' => 'specialized'],
    ['code' => 'HUMSS-SOC201', 'name' => 'Disciplines and Ideas in the Social Sciences', 'units' => 3, 'semester' => '1st', 'type' => 'specialized'],
    ['code' => 'HUMSS-SOC202', 'name' => 'Disciplines and Ideas in the Applied Social Sciences', 'units' => 3, 'semester' => '2nd', 'type' => 'specialized'],
    ['code' => 'HUMSS-PHILO201', 'name' => 'Trends, Networks and Critical Thinking in the 21st Century', 'units' => 3, 'semester' => '1st', 'type' => 'specialized'],
    ['code' => 'HUMSS-COMM101', 'name' => 'Community Engagement, Solidarity and Citizenship', 'units' => 3, 'semester' => '2nd', 'type' => 'specialized'],
    ['code' => 'HUMSS-WORLD101', 'name' => 'World Religions and Belief Systems', 'units' => 3, 'semester' => '1st', 'type' => 'specialized'],
    ['code' => 'HUMSS-HIS101', 'name' => 'Philippine Politics and Governance', 'units' => 3, 'semester' => '2nd', 'type' => 'specialized'],
];

echo "=== Processing Subjects ===\n\n";

$created = 0;
$updated = 0;
$linked = 0;
$errors = 0;

foreach ($subjects as $subjectData) {
    try {
        // Create or update the subject
        $subject = Subject::updateOrCreate(
            ['code' => $subjectData['code']],
            [
                'name' => $subjectData['name'],
                'units' => $subjectData['units'],
                'type' => $subjectData['type'],
                'semester' => $subjectData['semester'],
            ]
        );
        
        if ($subject->wasRecentlyCreated) {
            $created++;
            echo "✓ Created: {$subject->code} - {$subject->name}\n";
        } else {
            $updated++;
            echo "✓ Updated: {$subject->code} - {$subject->name}\n";
        }
        
        // Link to strand if not already linked
        $strandSubject = StrandSubject::firstOrCreate(
            [
                'strand_id' => $strand->id,
                'subject_id' => $subject->id,
            ],
            [
                'grade_level' => '11', // Default grade level
            ]
        );
        
        if ($strandSubject->wasRecentlyCreated) {
            $linked++;
            echo "  → Linked to HUMSS strand\n";
        }
        
    } catch (\Exception $e) {
        $errors++;
        echo "❌ Error with {$subjectData['code']}: {$e->getMessage()}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Subjects created: {$created}\n";
echo "Subjects updated: {$updated}\n";
echo "New strand-subject links: {$linked}\n";
echo "Errors: {$errors}\n";
echo "\n✅ HUMSS subjects insertion completed!\n";
