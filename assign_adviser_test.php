<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Section;
use App\Models\AcademicYearStrandSection;
use App\Models\Teacher;
use App\Models\Strand;
use App\Models\AcademicYear;

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "No active academic year found!\n";
    exit(1);
}
echo "Active Academic Year: " . $activeYear->name . " (ID: " . $activeYear->id . ")\n\n";

// Find MARCH section
$section = Section::where('name', 'MARCH')->first();
if (!$section) {
    echo "MARCH section not found!\n";
    exit(1);
}
echo "Section: " . $section->name . " (ID: " . $section->id . ")\n";

// Find the strand
$strand = Strand::find($section->strand_id);
if (!$strand) {
    echo "Strand not found!\n";
    exit(1);
}
echo "Strand: " . $strand->name . " (ID: " . $strand->id . ")\n\n";

// Find a teacher to assign (we'll use Toledo, clyde - the subject teacher)
$teacher = Teacher::where('last_name', 'Toledo')
    ->where('first_name', 'clyde')
    ->first();

if (!$teacher) {
    // If not found, get the first active teacher
    $teacher = Teacher::where('status', 'active')->first();
}

if (!$teacher) {
    echo "No teacher found!\n";
    exit(1);
}
echo "Teacher to assign: " . $teacher->last_name . ", " . $teacher->first_name . " (ID: " . $teacher->id . ")\n\n";

// Find or create the academic_year_strand_section record
$academicYearStrandSection = AcademicYearStrandSection::firstOrCreate(
    [
        'academic_year_id' => $activeYear->id,
        'strand_id' => $strand->id,
        'section_id' => $section->id,
    ],
    [
        'is_active' => true,
    ]
);

echo "Academic Year Strand Section Record:\n";
echo "  - ID: " . $academicYearStrandSection->id . "\n";
echo "  - Academic Year: " . $academicYearStrandSection->academic_year_id . "\n";
echo "  - Strand: " . $academicYearStrandSection->strand_id . "\n";
echo "  - Section: " . $academicYearStrandSection->section_id . "\n";
echo "  - Current Adviser ID: " . ($academicYearStrandSection->adviser_teacher_id ?: 'NULL') . "\n\n";

// Update the adviser_teacher_id
$academicYearStrandSection->update([
    'adviser_teacher_id' => $teacher->id,
]);

echo "✓ Adviser assigned successfully!\n";
echo "  - New Adviser ID: " . $academicYearStrandSection->adviser_teacher_id . "\n";
echo "  - Adviser: " . $teacher->last_name . ", " . $teacher->first_name . "\n\n";

// Verify the assignment
$academicYearStrandSection->load('adviserTeacher');
if ($academicYearStrandSection->adviserTeacher) {
    echo "✓ Verification: Adviser is now " . $academicYearStrandSection->adviserTeacher->last_name . ", " . $academicYearStrandSection->adviserTeacher->first_name . "\n";
} else {
    echo "✗ Verification failed: Adviser not found!\n";
}
