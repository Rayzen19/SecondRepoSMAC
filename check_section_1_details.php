<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Section;
use App\Models\AcademicYearStrandSection;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;

echo "=== Investigating Section ID 1 (from URL) ===\n\n";

// The URL shows /teacher/students/section/1
// This is an AcademicYearStrandSection ID

$sectionAssignment = AcademicYearStrandSection::with(['section', 'strand', 'academicYear', 'adviserTeacher'])
    ->find(1);

if (!$sectionAssignment) {
    echo "❌ Section Assignment ID 1 not found!\n";
    exit;
}

echo "✅ Section Assignment Found:\n";
echo "   ID: {$sectionAssignment->id}\n";
echo "   Academic Year: {$sectionAssignment->academicYear->name}\n";
echo "   Strand: {$sectionAssignment->strand->code} - {$sectionAssignment->strand->name}\n";
echo "   Section: {$sectionAssignment->section->name} (Grade {$sectionAssignment->section->grade})\n";

if ($sectionAssignment->adviserTeacher) {
    echo "   Adviser: {$sectionAssignment->adviserTeacher->first_name} {$sectionAssignment->adviserTeacher->last_name} (ID: {$sectionAssignment->adviserTeacher->id})\n";
} else {
    echo "   Adviser: None assigned\n";
}

echo "\n📊 Looking for Enrolled Students...\n\n";

// Check if academic year is active
$isActive = $sectionAssignment->academicYear->is_active ? "✅ Active" : "❌ Inactive";
echo "Academic Year Status: {$isActive}\n\n";

// Find students enrolled in this section
$enrollments = StudentEnrollment::with('student')
    ->where('academic_year_id', $sectionAssignment->academic_year_id)
    ->where('academic_year_strand_section_id', $sectionAssignment->id)
    ->get();

echo "Total Enrolled Students: {$enrollments->count()}\n\n";

if ($enrollments->isEmpty()) {
    echo "❌ NO STUDENTS ENROLLED IN THIS SECTION!\n\n";
    
    echo "Possible Reasons:\n";
    echo "1. Students haven't been enrolled yet\n";
    echo "2. Students are enrolled in different sections\n";
    echo "3. The academic_year_strand_section_id doesn't match\n\n";
    
    // Check if there are ANY enrollments for this academic year
    $totalEnrollments = StudentEnrollment::where('academic_year_id', $sectionAssignment->academic_year_id)->count();
    echo "Total enrollments in {$sectionAssignment->academicYear->name}: {$totalEnrollments}\n";
    
    if ($totalEnrollments > 0) {
        echo "\nShowing some enrollments from this academic year:\n";
        $sampleEnrollments = StudentEnrollment::with(['student', 'academicYearStrandSection.section', 'academicYearStrandSection.strand'])
            ->where('academic_year_id', $sectionAssignment->academic_year_id)
            ->limit(5)
            ->get();
        
        foreach ($sampleEnrollments as $enr) {
            $student = $enr->student;
            $section = $enr->academicYearStrandSection->section ?? null;
            $strand = $enr->academicYearStrandSection->strand ?? null;
            
            echo "   - {$student->student_number}: {$student->first_name} {$student->last_name}\n";
            echo "     Section Assignment ID: {$enr->academic_year_strand_section_id}\n";
            if ($section && $strand) {
                echo "     Section: {$section->name}, Strand: {$strand->code}\n";
            }
            echo "\n";
        }
    }
} else {
    echo "✅ Students Enrolled:\n";
    foreach ($enrollments as $enrollment) {
        $student = $enrollment->student;
        echo "   - {$student->student_number}: {$student->first_name} {$student->last_name}\n";
        echo "     Gender: {$student->gender}\n";
        echo "     Registration: {$enrollment->registration_number}\n";
        echo "     Status: {$enrollment->status}\n\n";
    }
}
