<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AcademicYearStrandSubject;
use App\Models\AcademicYearStrandSection;

// Find the Filipino subject assignment for MARCH section taught by Toledo, clyde
$assignment = AcademicYearStrandSubject::with(['academicYear', 'strand', 'subject', 'teacher'])
    ->whereHas('subject', function($q) {
        $q->where('name', 'LIKE', '%Filipino%');
    })
    ->whereHas('teacher', function($q) {
        $q->where('last_name', 'Toledo')->where('first_name', 'clyde');
    })
    ->first();

if (!$assignment) {
    echo "Assignment not found!\n";
    exit(1);
}

echo "Subject Assignment Found:\n";
echo "  - Subject: " . $assignment->subject->name . "\n";
echo "  - Teacher: " . $assignment->teacher->last_name . ", " . $assignment->teacher->first_name . "\n";
echo "  - Academic Year: " . $assignment->academicYear->name . "\n";
echo "  - Strand: " . $assignment->strand->name . "\n\n";

// Now simulate what the controller does to get the adviser
$sectionQuery = AcademicYearStrandSection::with('section', 'adviserTeacher')
    ->where('academic_year_id', $assignment->academic_year_id)
    ->where('strand_id', $assignment->strand_id);

$preferred = (clone $sectionQuery)->where('adviser_teacher_id', $assignment->teacher_id)->first();
$sectionAssignment = $preferred ?: $sectionQuery->first();

if ($sectionAssignment) {
    $sectionName = optional($sectionAssignment->section)->name;
    $grade = optional($sectionAssignment->section)->grade;
    $adviserName = optional($sectionAssignment->adviserTeacher)->last_name
        ? ($sectionAssignment->adviserTeacher->last_name . ', ' . $sectionAssignment->adviserTeacher->first_name)
        : null;
    
    echo "What would be displayed in Class Record:\n";
    echo "  - Section: " . ($sectionName ?: '—') . "\n";
    echo "  - Grade: " . ($grade ?: '—') . "\n";
    echo "  - Adviser: " . ($adviserName ?: '—') . "\n";
} else {
    echo "No section assignment found!\n";
}
