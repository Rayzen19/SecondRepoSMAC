<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Student 2025-00005 (John Raymond Barrogo) ===\n\n";

$student = \App\Models\Student::where('student_number', '2025-00005')->first();

if (!$student) {
    echo "❌ Student not found with number 2025-00005\n";
    exit(1);
}

echo "Student: {$student->first_name} {$student->last_name}\n";
echo "Student ID: {$student->id}\n";
echo "Student Number: {$student->student_number}\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n";
echo "Pre-enrollment enabled: " . ($activeYear->pre_enrollment_enabled ? "YES" : "NO") . "\n\n";

// Check all enrollments
$enrollments = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->with(['academicYear', 'strand', 'academicYearStrandSection.section'])
    ->get();

echo "Total enrollments: {$enrollments->count()}\n\n";

if ($enrollments->isEmpty()) {
    echo "❌ NO ENROLLMENTS FOUND\n";
    echo "This student needs to be enrolled.\n";
    exit;
}

foreach ($enrollments as $enrollment) {
    $isActive = $enrollment->academic_year_id == $activeYear->id;
    echo ($isActive ? "✓✓✓ " : "    ") . "Enrollment ID: {$enrollment->id}\n";
    echo "      Academic Year: {$enrollment->academicYear->name} (ID: {$enrollment->academic_year_id})\n";
    echo "      Strand: " . ($enrollment->strand->code ?? 'N/A') . "\n";
    echo "      Section: " . ($enrollment->academicYearStrandSection->section->name ?? 'N/A') . "\n";
    echo "      Status: {$enrollment->status}\n";
    if ($isActive) {
        echo "      >>> THIS IS FOR THE ACTIVE YEAR <<<\n";
    }
    echo "\n";
}

// Check specifically for active year
$currentEnrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

echo "=== RESULT ===\n";
if ($currentEnrollment) {
    echo "✅ Student HAS enrollment for {$activeYear->name}\n";
    echo "Pre-enrollment SHOULD work.\n\n";
    echo "If it doesn't work, please log out and log back in to refresh the session.\n";
} else {
    echo "❌ Student DOES NOT have enrollment for {$activeYear->name}\n";
    echo "The profile shows academic year 2025-2026, but no enrollment exists.\n";
    echo "Need to create enrollment for this student in the current academic year.\n";
}

echo "\n=== Done ===\n";
