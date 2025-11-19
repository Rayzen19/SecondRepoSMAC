<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Pre-Enrollment Status ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

if ($activeYear) {
    echo "Active Academic Year Found:\n";
    echo "ID: " . $activeYear->id . "\n";
    echo "Name: " . $activeYear->name . "\n";
    echo "Semester: " . $activeYear->semester . "\n";
    echo "Pre-enrollment Enabled: " . ($activeYear->pre_enrollment_enabled ? 'YES ✓' : 'NO ✗') . "\n";
    echo "Is Active: " . ($activeYear->is_active ? 'YES' : 'NO') . "\n";
    echo "\n";
} else {
    echo "❌ No active academic year found!\n\n";
}

// Check if there's a logged-in student (simulating)
echo "=== Checking Student View Composer Logic ===\n";

// Get a sample student to test with
$student = \App\Models\Student::first();

if ($student) {
    echo "Testing with Student: " . $student->first_name . " " . $student->last_name . "\n";
    
    // Get the student's current enrollment
    $enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
        ->where('status', 'active')
        ->latest()
        ->first();
    
    if ($enrollment) {
        echo "Active Enrollment Found:\n";
        echo "Student ID: " . $enrollment->student_id . "\n";
        echo "Academic Year ID: " . $enrollment->academic_year_id . "\n";
        echo "Section ID: " . $enrollment->academic_year_strand_section_id . "\n";
        
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
            echo "Pre-enrollment Enabled for this enrollment: " . ($preEnrollmentEnabled ? 'YES ✓' : 'NO ✗') . "\n";
        }
    } else {
        echo "❌ No active enrollment found for this student\n";
    }
} else {
    echo "❌ No students found in database\n";
}

echo "\n=== Done ===\n";
