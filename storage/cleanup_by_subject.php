<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SubjectEnrollment;

$student = Student::where('student_number', '2025-00021')->first();
$enrollment = StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', 5)
    ->first();

echo "Student: {$student->student_number}\n";
echo "Enrollment ID: {$enrollment->id}\n\n";

// Find all subject enrollments grouped by subject ID (not assignment ID)
$subjectEnrollments = SubjectEnrollment::with('academicYearStrandSubject.subject')
    ->where('student_enrollment_id', $enrollment->id)
    ->get();

echo "Current Subject Enrollments:\n";
foreach ($subjectEnrollments as $se) {
    $subject = $se->academicYearStrandSubject->subject;
    $assignment = $se->academicYearStrandSubject;
    echo "  SE ID: {$se->id} | Assignment ID: {$assignment->id} | Subject: {$subject->name} ({$subject->code}) | Subject ID: {$subject->id} | Created: {$se->created_at}\n";
}

// Group by subject_id to find duplicates
$bySubject = $subjectEnrollments->groupBy(function($se) {
    return $se->academicYearStrandSubject->subject->id;
});

$duplicateSubjects = $bySubject->filter(function($group) {
    return $group->count() > 1;
});

if ($duplicateSubjects->isNotEmpty()) {
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "Found {$duplicateSubjects->count()} subject(s) with multiple enrollments:\n\n";
    
    foreach ($duplicateSubjects as $subjectId => $group) {
        $subject = $group->first()->academicYearStrandSubject->subject;
        echo "Subject: {$subject->name} ({$subject->code}) - {$group->count()} enrollments\n";
        
        // Keep the oldest, delete newer ones
        $sorted = $group->sortBy('created_at');
        $toKeep = $sorted->first();
        $toDelete = $sorted->skip(1);
        
        echo "  ✓ Keeping: SE ID {$toKeep->id} (Assignment {$toKeep->academic_year_strand_subject_id}, created {$toKeep->created_at})\n";
        
        foreach ($toDelete as $se) {
            echo "  ✗ Deleting: SE ID {$se->id} (Assignment {$se->academic_year_strand_subject_id}, created {$se->created_at})\n";
            
            // Also delete the test assignment we created if it's the one from our test
            $assignment = $se->academicYearStrandSubject;
            if ($assignment->id == 21 && $assignment->created_at > '2025-10-21 20:00:00') {
                echo "    → Also deleting test assignment ID {$assignment->id}\n";
                $assignment->delete();
            }
            
            $se->delete();
        }
        echo "\n";
    }
    
    echo "Cleanup complete!\n";
}

// Show final result
echo "\n" . str_repeat('=', 80) . "\n";
$final = SubjectEnrollment::with('academicYearStrandSubject.subject')
    ->where('student_enrollment_id', $enrollment->id)
    ->get();

echo "Final Subject Enrollments ({$final->count()}):\n";
foreach ($final as $se) {
    $subject = $se->academicYearStrandSubject->subject;
    echo "  - {$subject->name} ({$subject->code})\n";
}
