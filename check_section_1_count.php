<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Year: {$activeYear->name}\n\n";

// Check section ID 1 (G-11 A - JUDE)
$section = \App\Models\Section::find(1);
echo "Section: {$section->grade} {$section->name}\n";

$ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('section_id', 1)
    ->first();

if ($ayss) {
    $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
        ->where('academic_year_id', $activeYear->id)
        ->count();
    
    echo "Student count: {$count}\n\n";
    
    $students = \App\Models\StudentEnrollment::with('student')
        ->where('academic_year_strand_section_id', $ayss->id)
        ->where('academic_year_id', $activeYear->id)
        ->get();
    
    echo "Students enrolled:\n";
    foreach ($students as $enrollment) {
        if ($enrollment->student) {
            echo "  - {$enrollment->student->first_name} {$enrollment->student->last_name} (ID: {$enrollment->student_id})\n";
        }
    }
} else {
    echo "No AYSS record found\n";
}
