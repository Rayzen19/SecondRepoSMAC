<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSubject;

echo "=== Populating Second Semester Scores ===\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit(1);
}

echo "Active Year: {$activeYear->display_name}\n\n";

// First, let's check what subject records exist
$allRecords = SubjectRecord::with(['assignment'])->get();
echo "Total subject records in database: " . $allRecords->count() . "\n";

// Check for records with term 'finals' or similar
$finalsRecords = SubjectRecord::where('term', 'like', '%final%')
    ->orWhere('term', 'like', '%2nd%')
    ->orWhere('term', 'like', '%second%')
    ->get();

echo "Records with 'finals/2nd/second' term: " . $finalsRecords->count() . "\n";

// If no finals records exist, let's create sample assessments for second semester
if ($finalsRecords->isEmpty()) {
    echo "\n⚠️  No second semester records found. Creating sample assessments...\n\n";
    
    // Get all assignments (academic_year_strand_subjects)
    $assignments = AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)->get();
    
    if ($assignments->isEmpty()) {
        echo "❌ No assignments found!\n";
        exit(1);
    }
    
    foreach ($assignments as $assignment) {
        echo "Creating assessments for Assignment ID: {$assignment->id}\n";
        
        // Create Written Works for second semester
        for ($i = 1; $i <= 3; $i++) {
            SubjectRecord::create([
                'academic_year_strand_subject_id' => $assignment->id,
                'name' => "Written Work {$i} - 2nd Sem",
                'description' => "Second semester written work assessment {$i}",
                'max_score' => 100,
                'type' => 'written work',
                'quarter' => '2nd',
                'term' => 'finals',
                'date_given' => now()->subDays(rand(1, 30)),
            ]);
        }
        
        // Create Performance Tasks for second semester
        for ($i = 1; $i <= 2; $i++) {
            SubjectRecord::create([
                'academic_year_strand_subject_id' => $assignment->id,
                'name' => "Performance Task {$i} - 2nd Sem",
                'description' => "Second semester performance task {$i}",
                'max_score' => 100,
                'type' => 'performance task',
                'quarter' => '2nd',
                'term' => 'finals',
                'date_given' => now()->subDays(rand(1, 30)),
            ]);
        }
        
        // Create Quarterly Assessment for second semester
        SubjectRecord::create([
            'academic_year_strand_subject_id' => $assignment->id,
            'name' => "Quarterly Assessment - 2nd Sem",
            'description' => "Second semester quarterly assessment",
            'max_score' => 100,
            'type' => 'quarterly assessment',
            'quarter' => '2nd',
            'term' => 'finals',
            'date_given' => now()->subDays(rand(1, 15)),
        ]);
        
        echo "  ✅ Created 6 assessments\n";
    }
    
    echo "\n✅ Second semester assessments created!\n\n";
    
    // Refresh the records
    $finalsRecords = SubjectRecord::where('term', 'finals')->with(['assignment'])->get();
}

echo "\nProcessing " . $finalsRecords->count() . " second semester assessments...\n\n";

$totalScoresCreated = 0;
$totalScoresUpdated = 0;

// Get all enrolled students
$enrollments = StudentEnrollment::with(['student'])
    ->where('academic_year_id', $activeYear->id)
    ->get();

echo "Found " . $enrollments->count() . " enrolled students\n\n";

// For each second semester record, create scores for all students
foreach ($finalsRecords as $record) {
    $assignment = $record->assignment;
    
    if (!$assignment) {
        echo "⚠️  Skipping record #{$record->id} - no assignment found\n";
        continue;
    }

    echo "Processing: {$record->name} (Type: {$record->type}, Max: {$record->max_score})\n";
    echo "  Assignment: Strand ID {$assignment->strand_id}, Subject ID {$assignment->subject_id}\n";

    // Get students enrolled in this strand
    $studentsInStrand = StudentEnrollment::with(['student'])
        ->where('academic_year_id', $activeYear->id)
        ->where('strand_id', $assignment->strand_id)
        ->get();

    echo "  Students in this strand: " . $studentsInStrand->count() . "\n";

    foreach ($studentsInStrand as $enrollment) {
        $student = $enrollment->student;
        
        if (!$student) {
            continue;
        }

        // Generate a random score between 75% and 100% of max score
        $maxScoreValue = (float)$record->max_score;
        $minScore = (int)round($maxScoreValue * 0.75); // 75% minimum
        $maxScoreInt = (int)round($maxScoreValue);
        $rawScore = rand($minScore, $maxScoreInt);

        // Check if score already exists
        $existingScore = SubjectRecordResult::where('subject_record_id', $record->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingScore) {
            // Update existing score
            $existingScore->raw_score = $rawScore;
            $existingScore->save();
            $totalScoresUpdated++;
        } else {
            // Create new score
            SubjectRecordResult::create([
                'subject_record_id' => $record->id,
                'student_id' => $student->id,
                'raw_score' => $rawScore,
            ]);
            $totalScoresCreated++;
        }
    }
    
    echo "  ✅ Processed {$studentsInStrand->count()} students\n\n";
}

echo "\n=== Summary ===\n";
echo "✅ Total scores created: {$totalScoresCreated}\n";
echo "✅ Total scores updated: {$totalScoresUpdated}\n";
echo "✅ Grand total: " . ($totalScoresCreated + $totalScoresUpdated) . "\n";
echo "\n=== Complete! ===\n";
echo "\nNow refresh your Second Semester tab to see all the scores!\n";
