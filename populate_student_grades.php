<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use App\Models\SubjectRecord;
use App\Models\SubjectRecordResult;
use App\Models\AcademicYearStrandSubject;
use Illuminate\Support\Facades\DB;

echo "=== Populating Student Grades ===\n\n";

$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "No active academic year found!\n";
    exit(1);
}

echo "Active Year: {$activeYear->display_name}\n\n";

// Get all enrolled students for the active year
$enrollments = StudentEnrollment::with(['student', 'academicYearStrandSection.section'])
    ->where('academic_year_id', $activeYear->id)
    ->get();

echo "Found {$enrollments->count()} enrolled students\n\n";

$studentsProcessed = 0;
$assessmentsCreated = 0;
$scoresAdded = 0;

foreach ($enrollments as $enrollment) {
    $student = $enrollment->student;
    if (!$student) continue;
    
    echo "Processing: {$student->first_name} {$student->last_name} ({$student->student_number})\n";
    
    // Get the strand and grade level
    $strandId = $enrollment->strand_id;
    $gradeLevel = $enrollment->academicYearStrandSection->section->grade;
    $numericGradeLevel = str_replace(['G-', 'Grade ', 'Grade-'], '', $gradeLevel);
    
    // Get all subject assignments for this student's strand
    $assignments = AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $strandId)
        ->whereNotNull('teacher_id')
        ->get();
    
    foreach ($assignments as $assignment) {
        // For each assessment type (Written Work, Performance Task, Quarterly Assessment)
        $assessmentTypes = [
            ['type' => 'written work', 'name' => 'Written Work', 'count' => 3],
            ['type' => 'performance task', 'name' => 'Performance Task', 'count' => 2],
            ['type' => 'quarterly assessment', 'name' => 'Quarterly Assessment', 'count' => 1],
        ];
        
        foreach ($assessmentTypes as $assessmentType) {
            for ($i = 1; $i <= $assessmentType['count']; $i++) {
                // Check if this assessment already exists
                $existingRecord = SubjectRecord::where('academic_year_strand_subject_id', $assignment->id)
                    ->where('type', $assessmentType['type'])
                    ->where('name', 'LIKE', "{$assessmentType['name']} {$i}%")
                    ->first();
                
                if (!$existingRecord) {
                    // Create the subject record (assessment)
                    $subjectRecord = SubjectRecord::create([
                        'academic_year_strand_subject_id' => $assignment->id,
                        'name' => "{$assessmentType['name']} {$i}",
                        'description' => "Sample {$assessmentType['name']} {$i}",
                        'max_score' => 100,
                        'type' => $assessmentType['type'],
                        'quarter' => '1st',
                        'date_given' => now(),
                        'remarks' => null,
                    ]);
                    $assessmentsCreated++;
                } else {
                    $subjectRecord = $existingRecord;
                }
                
                // Check if student already has a score for this record
                $existingResult = SubjectRecordResult::where('subject_record_id', $subjectRecord->id)
                    ->where('student_id', $student->id)
                    ->first();
                
                if (!$existingResult) {
                    // Generate random scores (80-100 range for good performance)
                    $rawScore = rand(80, 100);
                    $baseScore = 100;
                    $finalScore = ($rawScore / $baseScore) * 100;
                    
                    // Create the result
                    SubjectRecordResult::create([
                        'subject_record_id' => $subjectRecord->id,
                        'student_id' => $student->id,
                        'raw_score' => $rawScore,
                        'base_score' => $baseScore,
                        'final_score' => $finalScore,
                        'remarks' => null,
                        'description' => null,
                        'date_submitted' => now(),
                    ]);
                    $scoresAdded++;
                }
            }
        }
    }
    
    $studentsProcessed++;
    echo "  ✓ Completed\n";
}

echo "\n=== Summary ===\n";
echo "Students processed: {$studentsProcessed}\n";
echo "Assessments created: {$assessmentsCreated}\n";
echo "Scores added: {$scoresAdded}\n";
echo "\n=== Done ===\n";
