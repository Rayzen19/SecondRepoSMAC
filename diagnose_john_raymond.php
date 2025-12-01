<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;

echo "=== Diagnosing John Raymond Barrogo's Enrollment ===\n\n";

// Find the student by user email
$user = \App\Models\User::where('email', 'johnraymond.barrogo@cvsu.edu.ph')
    ->where('type', 'student')
    ->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit;
}

$student = Student::find($user->user_pk_id);

if (!$student) {
    echo "❌ Student not found!\n";
    exit;
}

echo "✅ Student Found:\n";
echo "   Name: {$student->first_name} {$student->last_name}\n";
echo "   Student ID: {$student->id}\n";
echo "   Student Number: {$student->student_number}\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
echo "📅 Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Get all enrollments for this student
$enrollments = StudentEnrollment::where('student_id', $student->id)
    ->with(['academicYearStrandSection.section', 'academicYearStrandSection.strand', 'academicYear'])
    ->get();

echo "📚 All Enrollments for this student:\n";
echo "   Total: {$enrollments->count()}\n\n";

foreach ($enrollments as $enrollment) {
    echo "   Enrollment ID: {$enrollment->id}\n";
    echo "   Academic Year: {$enrollment->academicYear->name} (ID: {$enrollment->academic_year_id})\n";
    
    if ($enrollment->academicYearStrandSection) {
        $ayss = $enrollment->academicYearStrandSection;
        echo "   Section: " . ($ayss->section->name ?? 'N/A') . "\n";
        echo "   Grade: " . ($ayss->section->grade ?? 'N/A') . "\n";
        echo "   Strand: " . ($ayss->strand->code ?? 'N/A') . "\n";
        echo "   AYSS ID: {$enrollment->academic_year_strand_section_id}\n";
    } else {
        echo "   ⚠️ No AcademicYearStrandSection found!\n";
    }
    
    echo "   ---\n\n";
}

// Check which sections show this student
echo "\n=== Checking AcademicYearStrandSection Records ===\n\n";

$ayssRecords = \App\Models\AcademicYearStrandSection::with(['section', 'strand'])
    ->where('academic_year_id', $activeYear->id)
    ->whereIn('id', $enrollments->pluck('academic_year_strand_section_id'))
    ->get();

foreach ($ayssRecords as $ayss) {
    echo "AYSS ID: {$ayss->id}\n";
    echo "  Section: " . ($ayss->section->name ?? 'N/A') . "\n";
    echo "  Grade: " . ($ayss->section->grade ?? 'N/A') . "\n";
    echo "  Strand: " . ($ayss->strand->code ?? 'N/A') . "\n";
    
    // Count students in this section
    $studentCount = StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
    echo "  Total students in this section: {$studentCount}\n\n";
}

echo "\n=== Summary ===\n";
echo "The issue is likely that the student has multiple enrollment records,\n";
echo "or is enrolled in a section with a different grade level than expected.\n";
