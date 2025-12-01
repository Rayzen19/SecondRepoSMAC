<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Strand;

echo "=== Checking G-11 A - JUDE Section ===\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Find the section "A - JUDE"
$section = Section::where('name', 'A - JUDE')->first();
if (!$section) {
    echo "❌ Section 'A - JUDE' not found!\n";
    exit;
}

echo "✅ Section Found:\n";
echo "   Section ID: {$section->id}\n";
echo "   Section Name: {$section->name}\n";
echo "   Grade: {$section->grade}\n";
echo "   Strand ID: {$section->strand_id}\n\n";

// Find the strand (ABM)
$strand = Strand::find($section->strand_id);
echo "✅ Strand: {$strand->code}\n\n";

// Find the AcademicYearStrandSection record
$ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('section_id', $section->id)
    ->first();

if (!$ayss) {
    echo "❌ No AcademicYearStrandSection record found for this section!\n";
    echo "   Looking for: academic_year_id={$activeYear->id}, strand_id={$strand->id}, section_id={$section->id}\n";
    exit;
}

echo "✅ AYSS Record Found:\n";
echo "   AYSS ID: {$ayss->id}\n";
echo "   Academic Year ID: {$ayss->academic_year_id}\n";
echo "   Strand ID: {$ayss->strand_id}\n";
echo "   Section ID: {$ayss->section_id}\n\n";

// Get all students enrolled in this section
$enrollments = StudentEnrollment::with('student')
    ->where('academic_year_strand_section_id', $ayss->id)
    ->get();

echo "📚 Students Enrolled in G-11 A - JUDE:\n";
echo "   Total: {$enrollments->count()}\n\n";

foreach ($enrollments as $enrollment) {
    if ($enrollment->student) {
        $student = $enrollment->student;
        echo "   - {$student->first_name} {$student->last_name}\n";
        echo "     Student ID: {$student->id}\n";
        echo "     Student Number: {$student->student_number}\n";
        echo "     Enrollment ID: {$enrollment->id}\n";
        echo "     AYSS ID: {$enrollment->academic_year_strand_section_id}\n\n";
    }
}
