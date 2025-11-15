<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Enrollments and AYSS ===\n\n";

$enrollmentCount = \App\Models\StudentEnrollment::count();
$ayssCount = \App\Models\AcademicYearStrandSection::count();

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
$ayssActiveCount = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)->count();

echo "Total Student Enrollments: $enrollmentCount\n";
echo "Total AYSS Records: $ayssCount\n";
echo "AYSS for Active Year ({$activeYear->name}): $ayssActiveCount\n\n";

if ($enrollmentCount > 0) {
    echo "=== Sample Enrollments ===\n";
    $enrollments = \App\Models\StudentEnrollment::with(['student', 'academicYearStrandSection.section', 'academicYearStrandSection.strand'])
        ->take(10)
        ->get();
    
    foreach ($enrollments as $e) {
        $studentName = $e->student ? "{$e->student->first_name} {$e->student->last_name}" : 'Unknown';
        $sectionName = $e->academicYearStrandSection && $e->academicYearStrandSection->section 
            ? $e->academicYearStrandSection->section->name 
            : 'Unknown Section';
        $strandCode = $e->academicYearStrandSection && $e->academicYearStrandSection->strand 
            ? $e->academicYearStrandSection->strand->code 
            : 'Unknown Strand';
        
        echo "Student: $studentName | Section: $sectionName | Strand: $strandCode | AYSS ID: {$e->academic_year_strand_section_id}\n";
    }
}

if ($ayssCount > 0 && $ayssActiveCount == 0) {
    echo "\n⚠️  WARNING: AYSS records exist but NONE are for the active academic year!\n";
    echo "This means students cannot be enrolled in sections for the current year.\n\n";
    
    echo "=== AYSS Records by Academic Year ===\n";
    $aysByYear = \App\Models\AcademicYearStrandSection::selectRaw('academic_year_id, count(*) as count')
        ->groupBy('academic_year_id')
        ->with('academicYear')
        ->get();
    
    foreach ($aysByYear as $record) {
        $yearName = $record->academicYear ? $record->academicYear->name : "Unknown (ID: {$record->academic_year_id})";
        echo "  Academic Year: $yearName | Count: {$record->count}\n";
    }
}

echo "\nDone!\n";
