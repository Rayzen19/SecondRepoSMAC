<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYear;

echo "=== Investigating Teacher Jemelee Joy Barrogo's Section ===\n\n";

// Find the teacher
$teacher = Teacher::where('last_name', 'LIKE', '%Barrogo%')
    ->where('first_name', 'LIKE', '%Jemelee%')
    ->first();

if (!$teacher) {
    echo "❌ Teacher not found. Looking for all Barrogo teachers...\n";
    $teachers = Teacher::where('last_name', 'LIKE', '%Barrogo%')->get();
    foreach ($teachers as $t) {
        echo "   - ID: {$t->id} - {$t->first_name} {$t->last_name}\n";
    }
    exit;
}

echo "✅ Teacher Found:\n";
echo "   ID: {$teacher->id}\n";
echo "   Name: {$teacher->first_name} {$teacher->last_name}\n\n";

// Check if this teacher is an adviser
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit;
}

echo "📅 Active Academic Year: {$activeYear->name}\n\n";

// Find sections where this teacher is adviser
$advisedSections = AcademicYearStrandSection::with(['section', 'strand'])
    ->where('academic_year_id', $activeYear->id)
    ->where('adviser_teacher_id', $teacher->id)
    ->get();

echo "🏫 Sections Advised by this Teacher: {$advisedSections->count()}\n\n";

if ($advisedSections->isEmpty()) {
    echo "❌ This teacher is not assigned as adviser to any section!\n\n";
} else {
    foreach ($advisedSections as $sectionAssignment) {
        $section = $sectionAssignment->section;
        $strand = $sectionAssignment->strand;
        
        echo "Section: {$section->name} (Grade {$section->grade})\n";
        echo "Strand: {$strand->code} - {$strand->name}\n";
        echo "Section Assignment ID: {$sectionAssignment->id}\n";
        
        // Count enrolled students in this section
        $enrolledStudents = StudentEnrollment::with('student')
            ->where('academic_year_id', $activeYear->id)
            ->where('academic_year_strand_section_id', $sectionAssignment->id)
            ->get();
        
        echo "📊 Enrolled Students: {$enrolledStudents->count()}\n";
        
        if ($enrolledStudents->isEmpty()) {
            echo "   ❌ NO STUDENTS ENROLLED IN THIS SECTION!\n";
        } else {
            echo "   Students:\n";
            foreach ($enrolledStudents as $enrollment) {
                $student = $enrollment->student;
                echo "   - {$student->student_number}: {$student->first_name} {$student->last_name}\n";
            }
        }
        echo "\n";
    }
}

// Check John Raymond student
echo "=== Checking John Raymond Student ===\n";
$johnRaymond = Student::where('first_name', 'LIKE', '%John%')
    ->where('last_name', 'LIKE', '%Raymond%')
    ->first();

if ($johnRaymond) {
    echo "✅ John Raymond Found:\n";
    echo "   ID: {$johnRaymond->id}\n";
    echo "   Student Number: {$johnRaymond->student_number}\n";
    echo "   Name: {$johnRaymond->first_name} {$johnRaymond->last_name}\n\n";
    
    // Check enrollment
    $enrollment = StudentEnrollment::with(['academicYearStrandSection.section', 'academicYearStrandSection.strand'])
        ->where('student_id', $johnRaymond->id)
        ->where('academic_year_id', $activeYear->id)
        ->first();
    
    if ($enrollment) {
        echo "✅ John Raymond's Enrollment:\n";
        echo "   Enrollment ID: {$enrollment->id}\n";
        echo "   Status: {$enrollment->status}\n";
        echo "   Section Assignment ID: {$enrollment->academic_year_strand_section_id}\n";
        
        if ($enrollment->academicYearStrandSection) {
            $section = $enrollment->academicYearStrandSection->section;
            $strand = $enrollment->academicYearStrandSection->strand;
            echo "   Section: {$section->name} (Grade {$section->grade})\n";
            echo "   Strand: {$strand->code}\n";
            
            // Check if this section's adviser is Jemelee Joy
            $sectionAdviser = $enrollment->academicYearStrandSection->adviserTeacher;
            if ($sectionAdviser) {
                echo "   Section Adviser: {$sectionAdviser->first_name} {$sectionAdviser->last_name} (ID: {$sectionAdviser->id})\n";
                
                if ($sectionAdviser->id === $teacher->id) {
                    echo "   ✅ John Raymond IS assigned to Jemelee Joy's section!\n";
                } else {
                    echo "   ❌ John Raymond is NOT in Jemelee Joy's section!\n";
                    echo "   He is in a different teacher's section.\n";
                }
            } else {
                echo "   ⚠️ This section has NO adviser assigned!\n";
            }
        } else {
            echo "   ❌ Section assignment not found!\n";
        }
    } else {
        echo "❌ John Raymond has NO enrollment for active year!\n";
    }
} else {
    echo "❌ John Raymond student not found!\n";
}

echo "\n=== Analysis Complete ===\n";
