<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Section Assignment ID: 7\n";
echo "=====================================\n\n";

$section = App\Models\AcademicYearStrandSection::with(['section', 'strand', 'adviserTeacher', 'academicYear'])
    ->find(7);

if ($section) {
    echo "✓ Section Assignment Found!\n\n";
    echo "ID: " . $section->id . "\n";
    echo "Section Name: " . ($section->section->name ?? 'N/A') . "\n";
    echo "Grade: " . ($section->section->grade ?? 'N/A') . "\n";
    echo "Strand: " . ($section->strand->name ?? 'N/A') . " (" . ($section->strand->code ?? 'N/A') . ")\n";
    echo "Adviser Teacher ID: " . $section->adviser_teacher_id . "\n";
    if ($section->adviserTeacher) {
        echo "Adviser Name: " . $section->adviserTeacher->first_name . " " . $section->adviserTeacher->last_name . "\n";
    }
    echo "Academic Year: " . ($section->academicYear->name ?? 'N/A') . "\n";
    echo "Is Active: " . ($section->is_active ? 'Yes' : 'No') . "\n";
    
    echo "\n\nStudent Count:\n";
    $studentCount = App\Models\StudentEnrollment::where('academic_year_strand_section_id', 7)->count();
    echo "Total Students: " . $studentCount . "\n";
} else {
    echo "✗ Section Assignment ID 7 NOT FOUND in database\n";
}

echo "\n\nAll Active Section Assignments:\n";
echo "=====================================\n";
$allSections = App\Models\AcademicYearStrandSection::with(['section', 'strand'])
    ->where('is_active', true)
    ->get();

foreach ($allSections as $s) {
    echo "ID: " . $s->id . " - ";
    echo "Grade " . ($s->section->grade ?? '?') . " ";
    echo "Section " . ($s->section->name ?? '?') . " - ";
    echo ($s->strand->code ?? '?') . " - ";
    echo "Adviser: " . $s->adviser_teacher_id . "\n";
}
