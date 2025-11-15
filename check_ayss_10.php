<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking AYSS ID 10 Details ===\n\n";

$ayss = \App\Models\AcademicYearStrandSection::with(['academicYear', 'strand', 'section'])
    ->find(10);

if (!$ayss) {
    echo "❌ AYSS ID 10 not found!\n";
    exit(1);
}

echo "AYSS ID: {$ayss->id}\n";
echo "Academic Year: {$ayss->academicYear->name} (ID: {$ayss->academic_year_id})\n";
echo "Strand: {$ayss->strand->code} (ID: {$ayss->strand_id})\n";
echo "Section: {$ayss->section->name} (ID: {$ayss->section_id})\n";
echo "Section Grade: {$ayss->section->grade}\n\n";

echo "This is the correct section assignment for John Raymond.\n";
echo "The section ID is: {$ayss->section_id}\n\n";

// Now check all STEM sections
echo "=== All STEM Sections ===\n";
$stemSections = \App\Models\Section::where('strand_id', 3)->get();
foreach ($stemSections as $section) {
    echo "  - ID: {$section->id}, Name: {$section->name}, Grade: {$section->grade}\n";
}

echo "\n=== All AcademicYearStrandSection for STEM in Active Year ===\n";
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
$stemAYSS = \App\Models\AcademicYearStrandSection::with('section')
    ->where('academic_year_id', $activeYear->id)
    ->where('strand_id', 3)
    ->get();

foreach ($stemAYSS as $a) {
    echo "  - AYSS ID: {$a->id}, Section ID: {$a->section_id}, Section: {$a->section->name}\n";
}

echo "\n=== Done ===\n";
