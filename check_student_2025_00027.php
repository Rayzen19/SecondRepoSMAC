<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Student 2025-00027 Enrollment ===\n\n";

$student = \App\Models\Student::where('student_number', '2025-00027')->first();

if (!$student) {
    echo "❌ Student not found with number 2025-00027\n";
    exit(1);
}

echo "Student: {$student->first_name} {$student->last_name}\n";
echo "Student ID: {$student->id}\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Check all enrollments for this student
$allEnrollments = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->with(['academicYear', 'strand', 'academicYearStrandSection.section'])
    ->get();

echo "Total enrollments found: {$allEnrollments->count()}\n\n";

foreach ($allEnrollments as $enrollment) {
    $isActive = $enrollment->academic_year_id == $activeYear->id;
    echo ($isActive ? "✓" : "  ") . " Enrollment ID: {$enrollment->id}\n";
    echo "   Academic Year: {$enrollment->academicYear->name} (ID: {$enrollment->academic_year_id})\n";
    echo "   Strand: " . ($enrollment->strand->code ?? 'N/A') . "\n";
    echo "   Section: " . ($enrollment->academicYearStrandSection->section->name ?? 'N/A') . "\n";
    echo "   Status: {$enrollment->status}\n";
    if ($isActive) {
        echo "   >>> THIS IS THE ACTIVE YEAR ENROLLMENT <<<\n";
    }
    echo "\n";
}

// Check for current year enrollment specifically
$currentEnrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if ($currentEnrollment) {
    echo "✅ Student HAS enrollment for current academic year\n";
    echo "This should allow pre-enrollment access.\n\n";
    echo "ISSUE: The controller should be working. Let me check logs...\n";
} else {
    echo "❌ Student DOES NOT have enrollment for current academic year\n";
    echo "Student needs to be enrolled in {$activeYear->name} first.\n\n";
    echo "To fix, you need to create an enrollment for this student in the current academic year.\n";
}

echo "\n=== Done ===\n";
