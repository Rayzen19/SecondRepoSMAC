<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentEnrollment;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Models\Section;

echo "=== Investigating Section and Subjects ===\n\n";

$enrollment = StudentEnrollment::with('section')->find(455);

echo "Student Enrollment:\n";
echo "   Academic Year ID: {$enrollment->academic_year_id}\n";
echo "   Section ID (academic_year_strand_section_id): {$enrollment->academic_year_strand_section_id}\n";

if ($enrollment->academic_year_strand_section_id) {
    $ayss = AcademicYearStrandSection::with(['section', 'strand'])->find($enrollment->academic_year_strand_section_id);
    
    if ($ayss) {
        echo "\nAcademic Year Strand Section:\n";
        echo "   ID: {$ayss->id}\n";
        echo "   Section: {$ayss->section->name}\n";
        echo "   Strand: {$ayss->strand->code}\n";
        echo "   Grade Level: {$ayss->grade_level}\n";
    }
}

echo "\n\n--- Searching for AcademicYearStrandSubjects ---\n";
echo "Looking for records where:\n";
echo "   academic_year_id = {$enrollment->academic_year_id}\n";
echo "   academic_year_strand_section_id = {$enrollment->academic_year_strand_section_id}\n\n";

$subjects = AcademicYearStrandSubject::where('academic_year_id', $enrollment->academic_year_id)
    ->where('academic_year_strand_section_id', $enrollment->academic_year_strand_section_id)
    ->with(['subject', 'teacher', 'strand'])
    ->get();

echo "Found: {$subjects->count()} matching subjects\n\n";

if ($subjects->count() > 0) {
    echo "Subjects:\n";
    foreach ($subjects as $ays) {
        echo "   - [{$ays->subject->code}] {$ays->subject->name}\n";
        echo "     Strand: {$ays->strand->code}, Teacher: " . ($ays->teacher ? $ays->teacher->first_name . ' ' . $ays->teacher->last_name : 'None') . "\n";
    }
} else {
    echo "❌ No subjects found for this section!\n\n";
    
    // Check if there are ANY subjects for this academic year
    $anySubjects = AcademicYearStrandSubject::where('academic_year_id', $enrollment->academic_year_id)
        ->count();
    
    echo "Total subjects in Academic Year {$enrollment->academic_year_id}: {$anySubjects}\n\n";
    
    if ($anySubjects > 0) {
        echo "Sample subjects in this academic year:\n";
        $samples = AcademicYearStrandSubject::where('academic_year_id', $enrollment->academic_year_id)
            ->with(['subject', 'strand'])
            ->limit(5)
            ->get();
        
        foreach ($samples as $s) {
            echo "   - {$s->subject->code} for Strand {$s->strand->code} (ayss_id: {$s->academic_year_strand_section_id})\n";
        }
    }
}
