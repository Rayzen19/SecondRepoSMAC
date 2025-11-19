<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Student STU-000027 (Diego Ortega) Enrollment ===\n\n";

$student = \App\Models\Student::where('student_number', 'STU-000027')->first();

echo "Student: {$student->first_name} {$student->last_name}\n";
echo "Student ID: {$student->id}\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n";
echo "Pre-enrollment enabled: " . ($activeYear->pre_enrollment_enabled ? "YES" : "NO") . "\n\n";

// Check all enrollments for this student
$allEnrollments = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->with(['academicYear', 'strand', 'academicYearStrandSection.section'])
    ->get();

echo "Total enrollments found: {$allEnrollments->count()}\n\n";

if ($allEnrollments->isEmpty()) {
    echo "❌ NO ENROLLMENTS FOUND FOR THIS STUDENT\n";
    echo "This student needs to be enrolled first.\n";
    exit;
}

foreach ($allEnrollments as $enrollment) {
    $isActive = $enrollment->academic_year_id == $activeYear->id;
    echo ($isActive ? "✓✓✓" : "   ") . " Enrollment ID: {$enrollment->id}\n";
    echo "     Academic Year: {$enrollment->academicYear->name} (ID: {$enrollment->academic_year_id})\n";
    echo "     Strand: " . ($enrollment->strand->code ?? 'N/A') . "\n";
    echo "     Section: " . ($enrollment->academicYearStrandSection->section->name ?? 'N/A') . "\n";
    echo "     Status: {$enrollment->status}\n";
    if ($isActive) {
        echo "     >>> THIS MATCHES THE ACTIVE YEAR <<<\n";
    }
    echo "\n";
}

// Check for current year enrollment specifically
$currentEnrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

echo "=== Result ===\n";
if ($currentEnrollment) {
    echo "✅ Student HAS enrollment for current academic year ({$activeYear->name})\n";
    echo "Pre-enrollment should work. Issue might be elsewhere.\n";
} else {
    echo "❌ Student DOES NOT have enrollment for current academic year ({$activeYear->name})\n";
    echo "Available enrollments are for other academic years.\n";
    echo "Solution: Create an enrollment for this student in {$activeYear->name}\n";
}

echo "\n=== Done ===\n";
