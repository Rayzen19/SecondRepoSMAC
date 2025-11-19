<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Student ID 325 ===\n\n";

$student = \App\Models\Student::find(325);

if (!$student) {
    echo "❌ Student ID 325 not found\n";
    exit(1);
}

echo "Student ID: 325\n";
echo "Student Number: {$student->student_number}\n";
echo "Name: {$student->first_name} {$student->last_name}\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Check enrollments
$enrollments = \App\Models\StudentEnrollment::where('student_id', 325)
    ->with(['academicYear', 'strand', 'academicYearStrandSection.section'])
    ->get();

echo "Total enrollments: {$enrollments->count()}\n\n";

if ($enrollments->isEmpty()) {
    echo "❌ NO ENROLLMENTS FOUND\n";
    echo "This student needs to be enrolled in {$activeYear->name}\n";
    exit;
}

foreach ($enrollments as $enrollment) {
    $isActive = $enrollment->academic_year_id == $activeYear->id;
    echo ($isActive ? "✓✓✓" : "   ") . " Enrollment ID: {$enrollment->id}\n";
    echo "     Academic Year: {$enrollment->academicYear->name} (ID: {$enrollment->academic_year_id})\n";
    echo "     Strand: " . ($enrollment->strand->code ?? 'N/A') . "\n";
    echo "     Section: " . ($enrollment->academicYearStrandSection->section->name ?? 'N/A') . "\n";
    echo "     Status: {$enrollment->status}\n";
    if ($isActive) {
        echo "     >>> MATCHES ACTIVE YEAR <<<\n";
    }
    echo "\n";
}

// Check specifically for active year enrollment
$currentEnrollment = \App\Models\StudentEnrollment::where('student_id', 325)
    ->where('academic_year_id', $activeYear->id)
    ->first();

echo "=== Result ===\n";
if ($currentEnrollment) {
    echo "✅ HAS enrollment for {$activeYear->name}\n";
} else {
    echo "❌ NO enrollment for {$activeYear->name}\n";
    echo "\nThis student is enrolled in:\n";
    foreach ($enrollments as $e) {
        echo "  - {$e->academicYear->name}\n";
    }
    echo "\nTo fix: Create enrollment for {$activeYear->name}\n";
}

echo "\n=== Done ===\n";
