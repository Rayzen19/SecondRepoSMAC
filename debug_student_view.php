<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debugging Student View Logic ===\n\n";

// Simulate being logged in as a student
$student = \App\Models\Auth\StudentUser::first();

if (!$student) {
    echo "❌ No student users found\n";
    exit;
}

echo "Testing with Student User:\n";
echo "ID: " . $student->id . "\n";
echo "Username: " . $student->username . "\n";
echo "Student ID: " . $student->student_id . "\n\n";

// Get the actual student record
$studentRecord = \App\Models\Student::find($student->student_id);

if (!$studentRecord) {
    echo "❌ Student record not found\n";
    exit;
}

echo "Student Record:\n";
echo "Name: " . $studentRecord->first_name . " " . $studentRecord->last_name . "\n\n";

// Get the student's current enrollment
$enrollment = \App\Models\StudentEnrollment::where('student_id', $studentRecord->id)
    ->where('status', 'active')
    ->latest()
    ->first();

if (!$enrollment) {
    echo "❌ No active enrollment found for this student\n";
    echo "This is why the button might be disabled!\n\n";
    
    // Check all enrollments
    $allEnrollments = \App\Models\StudentEnrollment::where('student_id', $studentRecord->id)->get();
    echo "All enrollments for this student: " . $allEnrollments->count() . "\n";
    foreach ($allEnrollments as $enroll) {
        echo "  - Status: {$enroll->status}, Academic Year: {$enroll->academic_year_id}\n";
    }
    exit;
}

echo "✓ Active Enrollment Found:\n";
echo "Student ID: " . $enrollment->student_id . "\n";
echo "Academic Year ID: " . $enrollment->academic_year_id . "\n";
echo "Section ID: " . $enrollment->academic_year_strand_section_id . "\n";
echo "Status: " . $enrollment->status . "\n\n";

// Check if school year ended
$schoolYearEnded = \App\Models\AcademicYearStrandSubject::where('academic_year_id', $enrollment->academic_year_id)
    ->where('academic_year_strand_section_id', $enrollment->academic_year_strand_section_id)
    ->where('school_year_ended', true)
    ->exists();

echo "School Year Ended: " . ($schoolYearEnded ? 'YES' : 'NO') . "\n";

// Check if pre-enrollment is enabled
$academicYear = \App\Models\AcademicYear::find($enrollment->academic_year_id);
if ($academicYear) {
    $preEnrollmentEnabled = $academicYear->pre_enrollment_enabled;
    echo "Pre-enrollment Enabled: " . ($preEnrollmentEnabled ? 'YES ✓' : 'NO ✗') . "\n";
    echo "Academic Year: " . $academicYear->name . " " . $academicYear->semester . "\n";
    echo "Is Active: " . ($academicYear->is_active ? 'YES' : 'NO') . "\n\n";
    
    if ($preEnrollmentEnabled) {
        echo "✅ RESULT: Pre-Enrollment button SHOULD be ENABLED\n";
    } else {
        echo "❌ RESULT: Pre-Enrollment button will be DISABLED\n";
    }
} else {
    echo "❌ Academic year not found\n";
}

echo "\n=== Done ===\n";
