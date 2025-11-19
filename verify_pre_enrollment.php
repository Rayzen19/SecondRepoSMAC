<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verifying Pre-Enrollment Status ===\n\n";

$email = 'johnraymond.barrogo@cvsu.edu.ph';
$student = \App\Models\Student::where('email', $email)->first();

if (!$student) {
    echo "Student not found!\n";
    exit;
}

$currentAcademicYear = \App\Models\AcademicYear::where('is_active', true)->first();

$existingPreEnrollment = \App\Models\PreEnrollment::with(['strand', 'section'])
    ->where('student_id', $student->id)
    ->where('current_academic_year_id', $currentAcademicYear->id)
    ->whereIn('status', ['pending', 'approved'])
    ->first();

echo "Student: {$student->full_name} (ID: {$student->id})\n";
echo "Academic Year: {$currentAcademicYear->name} (ID: {$currentAcademicYear->id})\n\n";

if ($existingPreEnrollment) {
    echo "✅ PRE-ENROLLMENT EXISTS - FORM SHOULD BE HIDDEN\n\n";
    echo "Details:\n";
    echo "  Status: {$existingPreEnrollment->status}\n";
    echo "  Grade Level: {$existingPreEnrollment->grade_level}\n";
    echo "  Strand: {$existingPreEnrollment->strand->code}\n";
    echo "  Section: " . ($existingPreEnrollment->section->name ?? 'No preference') . "\n";
    echo "  Submitted: {$existingPreEnrollment->submitted_at}\n";
} else {
    echo "❌ NO PRE-ENROLLMENT FOUND - FORM SHOULD BE VISIBLE\n";
}
