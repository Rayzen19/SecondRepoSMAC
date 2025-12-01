<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Section;
use App\Models\Strand;

echo "=== Checking ABM Sections ===\n\n";

// Get ABM strand
$strand = Strand::where('code', 'ABM')->first();
echo "Strand: {$strand->code} (ID: {$strand->id})\n\n";

// Get all ABM sections
$sections = Section::where('strand_id', $strand->id)
    ->orderBy('grade')
    ->orderBy('name')
    ->get();

echo "ABM Sections:\n";
foreach ($sections as $section) {
    echo "   - ID: {$section->id} | Name: {$section->name} | Grade: {$section->grade} | Strand ID: {$section->strand_id}\n";
}

echo "\n\n=== Checking AYSS Records ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

$ayssRecords = \App\Models\AcademicYearStrandSection::with(['section', 'strand'])
    ->where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->orderBy('id')
    ->get();

echo "AYSS Records for ABM in active year:\n";
foreach ($ayssRecords as $ayss) {
    $studentCount = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
    echo "   - AYSS ID: {$ayss->id} | Section: {$ayss->section->name} ({$ayss->section->grade}) | Section ID: {$ayss->section_id} | Students: {$studentCount}\n";
}
