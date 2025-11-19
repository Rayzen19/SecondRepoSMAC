<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Pre-Enrollment Redirect Issue ===\n\n";

$student = \App\Models\Student::first();
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

echo "Student: {$student->first_name} {$student->last_name}\n";
echo "Student ID: {$student->id}\n\n";

if (!$activeYear) {
    echo "❌ ISSUE: No active academic year found\n";
    echo "This will redirect to dashboard with error: 'No active academic year found.'\n";
    exit;
}

echo "✓ Active Year: {$activeYear->name}\n";
echo "  Pre-enrollment enabled: " . ($activeYear->pre_enrollment_enabled ? "YES" : "NO") . "\n\n";

if (!$activeYear->pre_enrollment_enabled) {
    echo "❌ ISSUE: Pre-enrollment is NOT enabled\n";
    echo "This will redirect to dashboard with error: 'Pre-enrollment is not currently available.'\n";
    echo "\nTo fix: Run this SQL or enable in admin panel:\n";
    echo "UPDATE academic_years SET pre_enrollment_enabled = 1 WHERE is_active = 1;\n";
    exit;
}

$enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if (!$enrollment) {
    echo "❌ ISSUE: Student has no enrollment for current academic year\n";
    echo "This will redirect to dashboard with error: 'You must be enrolled in the current academic year to pre-enroll.'\n";
    exit;
}

echo "✓ Student has active enrollment\n";
echo "  Enrollment ID: {$enrollment->id}\n";
echo "  Strand: " . ($enrollment->strand->code ?? 'N/A') . "\n";
echo "  Section: " . ($enrollment->academicYearStrandSection->section->name ?? 'N/A') . "\n\n";

echo "✅ ALL CHECKS PASSED - Pre-enrollment should work!\n";
echo "\nIf still redirecting to dashboard, check the browser session messages.\n";

echo "\n=== Done ===\n";
