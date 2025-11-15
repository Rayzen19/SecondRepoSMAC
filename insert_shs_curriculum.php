<?php
/**
 * Script to insert Senior High School Curriculum Subjects
 * Run this from command line: php insert_shs_curriculum.php
 * Or access via browser: http://127.0.0.1:8000/insert_shs_curriculum.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;

echo "=== SHS Curriculum Subject Insertion Script ===\n\n";

// Get all active strands
$allStrands = Strand::where('is_active', true)->get();

if ($allStrands->isEmpty()) {
    echo "ERROR: No active strands found in the system.\n";
    echo "Please create strands first before running this script.\n";
    exit(1);
}

echo "Found " . $allStrands->count() . " active strands:\n";
foreach ($allStrands as $strand) {
    echo "  - {$strand->code}: {$strand->name}\n";
}
echo "\n";

// Define Core/General Subjects (for ALL strands)
$coreSubjects = [
    // Grade 11 - 1st Semester
    ['code' => 'ORAL-COMM', 'name' => 'Oral Communication', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'READ-WRITE', 'name' => 'Reading and Writing', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'KOM-PANAN', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Filipino', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'PAGBASA-PAGSUSURI', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => '21ST-LIT', 'name' => '21st Century Literature from the Philippines and the World', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'CONTEMP-ARTS', 'name' => 'Contemporary Philippine Arts from the Regions', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'MEDIA-INFO-LIT', 'name' => 'Media and Information Literacy', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'GEN-MATH', 'name' => 'General Mathematics', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'STAT-PROB', 'name' => 'Statistics and Probability', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'EARTH-LIFE-SCI', 'name' => 'Earth and Life Science', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'PHYS-SCI', 'name' => 'Physical Science', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'INTRO-PHIL', 'name' => 'Introduction to Philosophy of the Human Person / Pambungad sa Pilosopiya ng Tao', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'PE-HEALTH', 'name' => 'Physical Education and Health', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'PERS-DEV', 'name' => 'Personal Development / Pansariling Kaunlaran', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'UCSP', 'name' => 'Understanding Culture, Society, and Politics', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    
    // Additional subjects with special notes
    ['code' => 'EARTH-SCI-ABM', 'name' => 'Earth Science (taken instead of Earth and Life Science for those in ABM Strand)', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
    ['code' => 'DISASTER-READY', 'name' => 'Disaster Readiness and Risk Reduction (taken instead of Physical Science for those in STEM Strand)', 'type' => 'core', 'semester' => '1st', 'grade_level' => '11', 'units' => 1],
];

// Define Applied Track Subjects (for ALL strands)
$appliedSubjects = [
    ['code' => 'ENG-ACAD-PROF', 'name' => 'English for Academic and Professional Purposes', 'type' => 'applied', 'semester' => '1st', 'grade_level' => '12', 'units' => 1],
    ['code' => 'PRAC-RES-1', 'name' => 'Practical Research 1', 'type' => 'applied', 'semester' => '1st', 'grade_level' => '12', 'units' => 1],
    ['code' => 'PRAC-RES-2', 'name' => 'Practical Research 2', 'type' => 'applied', 'semester' => '2nd', 'grade_level' => '12', 'units' => 1],
    ['code' => 'FIL-PILING', 'name' => 'Filipino sa Piling Larang (Tech Voc.)', 'type' => 'applied', 'semester' => '1st', 'grade_level' => '12', 'units' => 1],
    ['code' => 'EMP-TECH', 'name' => 'Empowerment Technologies (for the Strand)', 'type' => 'applied', 'semester' => '1st', 'grade_level' => '12', 'units' => 1],
    ['code' => 'ENTREP', 'name' => 'Entrepreneurship', 'type' => 'applied', 'semester' => '1st', 'grade_level' => '12', 'units' => 1],
    ['code' => 'INQ-INVEST-IMMER', 'name' => 'Inquiries, Investigations and Immersion', 'type' => 'applied', 'semester' => '2nd', 'grade_level' => '12', 'units' => 1],
];

// Define Specialized Subjects (example for ICT strand)
$specializedSubjects = [
    ['code' => 'COMP-PROG-JAVA', 'name' => 'Computer Programming JAVA (320 Hours)', 'type' => 'specialized', 'semester' => '1st', 'grade_level' => '12', 'units' => 1],
    ['code' => 'COMP-PROG-NET', 'name' => 'Computer Programming .NET TECHNOLOGY (320 Hours)', 'type' => 'specialized', 'semester' => '1st', 'grade_level' => '12', 'units' => 1],
    ['code' => 'WORK-IMMER', 'name' => 'Work Immersion/Research/Career Advocacy/Culminating Activity', 'type' => 'specialized', 'semester' => '2nd', 'grade_level' => '12', 'units' => 1],
];

$insertedCount = 0;
$skippedCount = 0;
$errors = [];

// Function to insert or skip subject
function insertSubject($subjectData, $strands, $isForAllStrands = true) {
    global $insertedCount, $skippedCount, $errors;
    
    // Check if subject already exists
    $existingSubject = Subject::where('code', $subjectData['code'])->first();
    
    if ($existingSubject) {
        echo "⚠ SKIPPED: {$subjectData['code']} - {$subjectData['name']} (already exists)\n";
        $skippedCount++;
        return;
    }
    
    try {
        // Create the subject
        $subject = Subject::create([
            'code' => $subjectData['code'],
            'name' => $subjectData['name'],
            'description' => $subjectData['description'] ?? null,
            'units' => $subjectData['units'] ?? 1,
            'type' => $subjectData['type'],
            'semester' => $subjectData['semester'],
        ]);
        
        // Link to strands
        $strandIds = $isForAllStrands ? $strands->pluck('id')->toArray() : $subjectData['strand_ids'];
        
        foreach ($strandIds as $strandId) {
            StrandSubject::create([
                'strand_id' => $strandId,
                'subject_id' => $subject->id,
                'grade_level' => $subjectData['grade_level'] ?? null,
                'semestral_period' => $subjectData['semester'],
                'written_works_percentage' => 20,
                'performance_tasks_percentage' => 60,
                'quarterly_assessment_percentage' => 20,
                'is_active' => true,
            ]);
        }
        
        $strandCount = count($strandIds);
        echo "✓ INSERTED: {$subjectData['code']} - {$subjectData['name']} (linked to {$strandCount} strand(s))\n";
        $insertedCount++;
        
    } catch (\Exception $e) {
        $error = "✗ ERROR: {$subjectData['code']} - " . $e->getMessage();
        echo $error . "\n";
        $errors[] = $error;
    }
}

echo "=== Inserting CORE/GENERAL Subjects (for ALL strands) ===\n";
foreach ($coreSubjects as $subject) {
    insertSubject($subject, $allStrands, true);
}

echo "\n=== Inserting APPLIED TRACK Subjects (for ALL strands) ===\n";
foreach ($appliedSubjects as $subject) {
    insertSubject($subject, $allStrands, true);
}

echo "\n=== Inserting SPECIALIZED Subjects ===\n";
echo "NOTE: Specialized subjects require manual assignment to specific strands.\n";
echo "For example, JAVA and .NET should be assigned to ICT strand only.\n";
echo "Please create these subjects manually through the admin interface.\n";
foreach ($specializedSubjects as $subject) {
    echo "  - {$subject['code']}: {$subject['name']}\n";
}

echo "\n=== SUMMARY ===\n";
echo "✓ Inserted: {$insertedCount} subjects\n";
echo "⚠ Skipped: {$skippedCount} subjects (already exist)\n";
echo "✗ Errors: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\nErrors encountered:\n";
    foreach ($errors as $error) {
        echo "  {$error}\n";
    }
}

echo "\n=== DONE ===\n";
echo "Please verify the subjects at: http://127.0.0.1:8000/admin/subjects\n";
