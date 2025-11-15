<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Student and Pre-Enrollment Status ===\n\n";

// Find students with active enrollments
$enrollments = \App\Models\StudentEnrollment::where('status', 'active')->get();

echo "Total Active Enrollments: " . $enrollments->count() . "\n\n";

if ($enrollments->count() > 0) {
    $enrollment = $enrollments->first();
    $student = \App\Models\Student::find($enrollment->student_id);
    
    if ($student) {
        echo "Sample Student with Active Enrollment:\n";
        echo "Name: " . $student->first_name . " " . $student->last_name . "\n";
        echo "Student Number: " . $student->student_number . "\n";
        echo "Status: " . $enrollment->status . "\n";
        echo "Academic Year ID: " . $enrollment->academic_year_id . "\n\n";
        
        // Check the academic year
        $academicYear = \App\Models\AcademicYear::find($enrollment->academic_year_id);
        
        if ($academicYear) {
            echo "Academic Year Details:\n";
            echo "Name: " . $academicYear->name . "\n";
            echo "Semester: " . $academicYear->semester . "\n";
            echo "Is Active: " . ($academicYear->is_active ? 'YES' : 'NO') . "\n";
            echo "Pre-enrollment Enabled: " . ($academicYear->pre_enrollment_enabled ? 'YES ✓' : 'NO ✗') . "\n\n";
            
            if ($academicYear->pre_enrollment_enabled) {
                echo "✅ For this student, Pre-Enrollment button SHOULD be ENABLED\n";
            } else {
                echo "❌ For this student, Pre-Enrollment button will be DISABLED\n";
                echo "\nTo fix: The academic year needs pre_enrollment_enabled = 1\n";
            }
        } else {
            echo "❌ Academic year not found\n";
        }
    }
} else {
    echo "❌ No active enrollments found\n";
    echo "Students need active enrollments to see the pre-enrollment button\n";
}

echo "\n=== Done ===\n";
