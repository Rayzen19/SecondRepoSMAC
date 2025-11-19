<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Pre-Enrollment Setup ===\n\n";

// Check if table exists
try {
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('pre_enrollments');
    echo "✓ pre_enrollments table exists: " . ($tableExists ? "YES" : "NO") . "\n";
} catch (\Exception $e) {
    echo "✗ Error checking table: " . $e->getMessage() . "\n";
}

// Check active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
if ($activeYear) {
    echo "✓ Active academic year found: {$activeYear->name}\n";
    echo "  Pre-enrollment enabled: " . ($activeYear->pre_enrollment_enabled ? "YES" : "NO") . "\n";
} else {
    echo "✗ No active academic year found\n";
}

// Check if there's a logged-in student (we'll check the first student)
$student = \App\Models\Student::first();
if ($student) {
    echo "\n✓ Sample student found: {$student->first_name} {$student->last_name}\n";
    echo "  Student ID: {$student->id}\n";
    
    // Check if student has current enrollment
    if ($activeYear) {
        $enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->first();
        
        if ($enrollment) {
            echo "✓ Student has active enrollment\n";
            echo "  Strand: " . ($enrollment->strand->code ?? 'N/A') . "\n";
            echo "  Section: " . ($enrollment->academicYearStrandSection->section->name ?? 'N/A') . "\n";
        } else {
            echo "✗ Student has NO active enrollment\n";
        }
    }
} else {
    echo "✗ No students found in database\n";
}

// Check strands
$strands = \App\Models\Strand::where('is_active', true)->count();
echo "\n✓ Active strands available: {$strands}\n";

// Check sections
$sections = \App\Models\Section::count();
echo "✓ Total sections: {$sections}\n";

echo "\n=== Checking Controller ===\n";
if (class_exists('App\Http\Controllers\Student\PreEnrollmentController')) {
    echo "✓ PreEnrollmentController exists\n";
} else {
    echo "✗ PreEnrollmentController NOT FOUND\n";
}

echo "\n=== Checking Model ===\n";
if (class_exists('App\Models\PreEnrollment')) {
    echo "✓ PreEnrollment model exists\n";
} else {
    echo "✗ PreEnrollment model NOT FOUND\n";
}

echo "\n=== Checking View ===\n";
$viewPath = resource_path('views/student/pre_enrollment/index.blade.php');
if (file_exists($viewPath)) {
    echo "✓ View file exists: {$viewPath}\n";
} else {
    echo "✗ View file NOT FOUND: {$viewPath}\n";
}

echo "\n=== Done ===\n";
