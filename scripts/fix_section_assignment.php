<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYearStrandSubject;

echo "=== Fixing Section Assignment ===\n\n";

// Find the assignment
$assignment = AcademicYearStrandSubject::find(3);
if (!$assignment) {
    echo "❌ Assignment not found\n";
    exit(1);
}

echo "Current assignment:\n";
echo "  - ID: {$assignment->id}\n";
echo "  - Teacher ID: {$assignment->teacher_id}\n";
echo "  - Subject ID: {$assignment->subject_id}\n";
echo "  - Strand ID: {$assignment->strand_id}\n";
echo "  - Academic Year ID: {$assignment->academic_year_id}\n";
echo "  - Section ID (AYS): " . ($assignment->academic_year_strand_section_id ?? 'NULL') . "\n\n";

// Update with section
$assignment->academic_year_strand_section_id = 1; // AcademicYearStrandSection ID for STEM Section MARCH
$assignment->save();

echo "✅ Updated assignment with section!\n\n";

echo "New assignment:\n";
echo "  - ID: {$assignment->id}\n";
echo "  - Teacher ID: {$assignment->teacher_id}\n";
echo "  - Subject ID: {$assignment->subject_id}\n";
echo "  - Strand ID: {$assignment->strand_id}\n";
echo "  - Academic Year ID: {$assignment->academic_year_id}\n";
echo "  - Section ID (AYS): {$assignment->academic_year_strand_section_id}\n\n";

// Verify
$assignment->load('sectionAssignment.section');
if ($assignment->sectionAssignment && $assignment->sectionAssignment->section) {
    $section = $assignment->sectionAssignment->section;
    echo "✅ Section verified: Grade {$section->grade}, {$section->name}\n\n";
    echo "🎉 SUCCESS! Now refresh the teacher profile page to see the section!\n";
} else {
    echo "❌ Could not verify section\n";
}

echo "\n=== Complete ===\n";
