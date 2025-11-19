<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\AcademicYearStrandSection;
use App\Models\StudentEnrollment;

$activeYear = AcademicYear::where('is_active', true)->first();
$section = Section::where('name', 'A - JUDE')->where('grade', 'G-11')->first();

if ($section && $activeYear && $section->strand) {
    echo "Section found: {$section->grade} {$section->name}\n";
    echo "Strand: {$section->strand->code}\n";
    echo "Active Year: {$activeYear->year}\n\n";
    
    $ayss = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('section_id', $section->id)
        ->where('strand_id', $section->strand->id)
        ->first();
    
    if ($ayss) {
        $count = StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
        echo "AYSS ID: {$ayss->id}\n";
        echo "Student Count in Database: {$count}\n";
    } else {
        echo "No AcademicYearStrandSection record found\n";
    }
} else {
    echo "Section, ActiveYear, or Strand not found\n";
}
