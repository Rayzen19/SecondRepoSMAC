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

if ($section && $activeYear) {
    echo "=== G-11 A - JUDE Section Analysis ===\n\n";
    echo "Section ID: {$section->id}\n";
    echo "Section Name: {$section->grade} {$section->name}\n";
    if ($section->strand) {
        echo "Strand: {$section->strand->code} (ID: {$section->strand->id})\n";
    }
    echo "Active Year: {$activeYear->year} (ID: {$activeYear->id})\n\n";
    
    // Check ALL AYSS records for this section
    echo "=== All AcademicYearStrandSection records for this section ===\n";
    $allAyss = AcademicYearStrandSection::where('section_id', $section->id)->get();
    foreach ($allAyss as $ayss) {
        $count = StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
        $ay = AcademicYear::find($ayss->academic_year_id);
        echo "AYSS ID: {$ayss->id} | Academic Year: {$ay->year} (Active: " . ($ay->is_active ? 'Yes' : 'No') . ") | Students: {$count}\n";
    }
    
    echo "\n=== Count using Assigning List logic ===\n";
    $sectionAssignment = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('section_id', $section->id)
        ->first();
    if ($sectionAssignment) {
        $studentCount = StudentEnrollment::where('academic_year_strand_section_id', $sectionAssignment->id)
            ->where('academic_year_id', $activeYear->id)
            ->count();
        echo "Count from Assigning List logic: {$studentCount}\n";
    } else {
        echo "No section assignment found for active year\n";
    }
    
    echo "\n=== Count using Section Advisers logic ===\n";
    if ($section->strand) {
        $ayss = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $section->strand->id)
            ->where('section_id', $section->id)
            ->first();
        if ($ayss) {
            $count = StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
            echo "Count from Section Advisers logic: {$count}\n";
        }
    }
}
