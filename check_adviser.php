<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Section;
use App\Models\AcademicYearStrandSection;
use App\Models\Teacher;
use App\Models\Strand;

// Find MARCH section
$section = Section::where('name', 'MARCH')->first();
echo "Section: " . ($section ? $section->name . " (ID: " . $section->id . ")" : "Not found") . PHP_EOL;

if ($section) {
    // Find academic year strand sections
    $ays = AcademicYearStrandSection::with(['adviserTeacher', 'strand', 'academicYear'])
        ->where('section_id', $section->id)
        ->get();
    
    echo "\nAcademic Year Strand Sections for MARCH:" . PHP_EOL;
    foreach ($ays as $a) {
        echo "  - AY: " . ($a->academicYear ? $a->academicYear->name : 'N/A');
        echo ", Strand: " . ($a->strand ? $a->strand->name : 'N/A');
        echo ", Section: " . ($a->section_id);
        echo ", Adviser ID: " . ($a->adviser_teacher_id ?: 'NULL');
        echo ", Adviser: " . ($a->adviserTeacher ? $a->adviserTeacher->last_name . ', ' . $a->adviserTeacher->first_name : 'Not assigned');
        echo PHP_EOL;
    }
}
