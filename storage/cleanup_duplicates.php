<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SubjectEnrollment;

$student = Student::where('student_number', '2025-00021')->first();
if (!$student) {
    echo "Student not found\n";
    exit(1);
}

$enrollment = StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', 5)
    ->first();

if (!$enrollment) {
    echo "Enrollment not found\n";
    exit(1);
}

echo "Student: {$student->student_number} ({$student->first_name} {$student->last_name})\n";
echo "Enrollment ID: {$enrollment->id}\n\n";

// Find all subject enrollments
$subjectEnrollments = SubjectEnrollment::with('academicYearStrandSubject.subject')
    ->where('student_enrollment_id', $enrollment->id)
    ->get();

echo "Current Subject Enrollments ({$subjectEnrollments->count()}):\n";
foreach ($subjectEnrollments as $se) {
    $subject = $se->academicYearStrandSubject->subject;
    echo "  SE ID: {$se->id} | Subject: {$subject->name} ({$subject->code}) | Created: {$se->created_at}\n";
}

// Find duplicates by grouping by academic_year_strand_subject_id
$duplicates = $subjectEnrollments->groupBy('academic_year_strand_subject_id')
    ->filter(function($group) {
        return $group->count() > 1;
    });

if ($duplicates->isEmpty()) {
    echo "\nNo duplicates found!\n";
} else {
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "Found duplicates for " . $duplicates->count() . " subject(s):\n";
    
    foreach ($duplicates as $aysId => $group) {
        $subject = $group->first()->academicYearStrandSubject->subject;
        echo "\nSubject: {$subject->name} ({$subject->code}) - {$group->count()} copies\n";
        
        // Keep the oldest one, delete the rest
        $sorted = $group->sortBy('created_at');
        $toKeep = $sorted->first();
        $toDelete = $sorted->skip(1);
        
        echo "  Keeping: SE ID {$toKeep->id} (created {$toKeep->created_at})\n";
        echo "  Deleting:\n";
        
        foreach ($toDelete as $se) {
            echo "    - SE ID {$se->id} (created {$se->created_at})\n";
            $se->delete();
        }
    }
    
    echo "\nDuplicates cleaned up successfully!\n";
}

// Show final state
echo "\n" . str_repeat('=', 80) . "\n";
$finalEnrollments = SubjectEnrollment::with('academicYearStrandSubject.subject')
    ->where('student_enrollment_id', $enrollment->id)
    ->get();

echo "Final Subject Enrollments ({$finalEnrollments->count()}):\n";
foreach ($finalEnrollments as $se) {
    $subject = $se->academicYearStrandSubject->subject;
    echo "  - {$subject->name} ({$subject->code})\n";
}
