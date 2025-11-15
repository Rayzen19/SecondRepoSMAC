<?php
/**
 * Script to insert Senior High School Specialized Subjects
 * Run this from command line: php insert_specialized_subjects.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;

echo "=== SHS Specialized Subjects Insertion Script ===\n\n";

// Get all active strands
$allStrands = Strand::where('is_active', true)->get();

echo "Available strands:\n";
foreach ($allStrands as $strand) {
    echo "  - {$strand->code}: {$strand->name} (ID: {$strand->id})\n";
}
echo "\n";

// Find specific strands
$ictStrand = Strand::where('code', 'LIKE', '%CP%')->orWhere('code', 'LIKE', '%ICT%')->first();
$tvlStrands = Strand::where('code', 'LIKE', 'TVL-%')->pluck('id')->toArray();

// Define Specialized Subjects
$specializedSubjects = [
    [
        'code' => 'COMP-PROG-JAVA',
        'name' => 'Computer Programming JAVA (320 Hours)',
        'type' => 'specialized',
        'semester' => '1st',
        'grade_level' => '12',
        'units' => 4,
        'description' => 'Computer Programming specialization in JAVA programming language - 320 hours',
        'strand_codes' => ['TVL-CP'], // Computer Programming strand
    ],
    [
        'code' => 'COMP-PROG-NET',
        'name' => 'Computer Programming .NET TECHNOLOGY (320 Hours)',
        'type' => 'specialized',
        'semester' => '2nd',
        'grade_level' => '12',
        'units' => 4,
        'description' => 'Computer Programming specialization in .NET technology - 320 hours',
        'strand_codes' => ['TVL-CP'], // Computer Programming strand
    ],
    [
        'code' => 'WORK-IMMER',
        'name' => 'Work Immersion/Research/Career Advocacy/Culminating Activity',
        'type' => 'specialized',
        'semester' => '2nd',
        'grade_level' => '12',
        'units' => 2,
        'description' => 'Work immersion and culminating activities',
        'strand_codes' => ['ALL'], // Available for all strands
    ],
];

$insertedCount = 0;
$skippedCount = 0;
$errors = [];

// Function to insert specialized subject
function insertSpecializedSubject($subjectData, $allStrands) {
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
        
        // Determine which strands should get this subject
        $targetStrands = [];
        
        if (in_array('ALL', $subjectData['strand_codes'])) {
            // Link to all strands
            $targetStrands = $allStrands;
        } else {
            // Link to specific strands by code
            foreach ($subjectData['strand_codes'] as $strandCode) {
                $strand = $allStrands->firstWhere('code', $strandCode);
                if ($strand) {
                    $targetStrands[] = $strand;
                } else {
                    echo "  ⚠ Warning: Strand '{$strandCode}' not found\n";
                }
            }
        }
        
        if (empty($targetStrands)) {
            echo "  ⚠ Warning: No target strands found for {$subjectData['code']}\n";
        }
        
        // Link to strands
        foreach ($targetStrands as $strand) {
            StrandSubject::create([
                'strand_id' => $strand->id,
                'subject_id' => $subject->id,
                'grade_level' => $subjectData['grade_level'] ?? null,
                'semestral_period' => $subjectData['semester'],
                'written_works_percentage' => 20,
                'performance_tasks_percentage' => 60,
                'quarterly_assessment_percentage' => 20,
                'is_active' => true,
            ]);
        }
        
        $strandCount = count($targetStrands);
        $strandNames = collect($targetStrands)->pluck('code')->join(', ');
        echo "✓ INSERTED: {$subjectData['code']} - {$subjectData['name']}\n";
        echo "  Linked to {$strandCount} strand(s): {$strandNames}\n";
        $insertedCount++;
        
    } catch (\Exception $e) {
        $error = "✗ ERROR: {$subjectData['code']} - " . $e->getMessage();
        echo $error . "\n";
        $errors[] = $error;
    }
}

echo "=== Inserting SPECIALIZED Subjects ===\n";
foreach ($specializedSubjects as $subject) {
    insertSpecializedSubject($subject, $allStrands);
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "✓ Inserted: {$insertedCount} specialized subjects\n";
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
