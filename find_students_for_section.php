<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;

echo "=== Finding Students Assigned to ABM Section A - JUDE ===\n\n";

$activeYear = AcademicYear::where('is_active', true)->first();

// Find the section assignment for ABM, Section A (JUDE), Grade 11
$sectionAssignment = AcademicYearStrandSection::with(['section', 'strand', 'adviserTeacher'])
    ->whereHas('strand', fn($q) => $q->where('code', 'ABM'))
    ->whereHas('section', fn($q) => $q->where('name', 'A - JUDE'))
    ->where('academic_year_id', $activeYear->id)
    ->first();

if (!$sectionAssignment) {
    echo "❌ Section assignment not found!\n";
    exit;
}

echo "✅ Section Assignment:\n";
echo "   ID: {$sectionAssignment->id}\n";
echo "   Strand: {$sectionAssignment->strand->code}\n";
echo "   Section: {$sectionAssignment->section->name}\n";
echo "   Adviser: " . ($sectionAssignment->adviserTeacher ? "{$sectionAssignment->adviserTeacher->first_name} {$sectionAssignment->adviserTeacher->last_name}" : "None") . "\n\n";

// Look for students who might be assigned to this section
echo "=== Looking for John Raymond in database ===\n";
$students = Student::where(function($q) {
    $q->where('first_name', 'LIKE', '%John%')
      ->orWhere('last_name', 'LIKE', '%Raymond%')
      ->orWhere('first_name', 'LIKE', '%Raymond%');
})->get();

if ($students->isEmpty()) {
    echo "❌ No students found matching 'John Raymond'\n\n";
} else {
    echo "✅ Found {$students->count()} student(s):\n";
    foreach ($students as $student) {
        echo "   - ID: {$student->id}, Name: {$student->first_name} {$student->last_name}, Number: {$student->student_number}\n";
        
        // Check enrollment
        $enrollment = StudentEnrollment::with(['academicYearStrandSection.section', 'academicYearStrandSection.strand'])
            ->where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->first();
        
        if ($enrollment) {
            $section = $enrollment->academicYearStrandSection->section ?? null;
            $strand = $enrollment->academicYearStrandSection->strand ?? null;
            
            echo "     Enrolled: YES\n";
            echo "     Section Assignment ID: {$enrollment->academic_year_strand_section_id}\n";
            echo "     Strand: " . ($strand ? $strand->code : 'N/A') . "\n";
            echo "     Section: " . ($section ? $section->name : 'N/A') . "\n";
            
            if ($enrollment->academic_year_strand_section_id == $sectionAssignment->id) {
                echo "     ✅ IS in ABM Section A - JUDE\n";
            } else {
                echo "     ❌ NOT in ABM Section A - JUDE\n";
            }
        } else {
            echo "     Enrolled: NO\n";
        }
        echo "\n";
    }
}

// Show ALL students in the current academic year
echo "\n=== All Students Enrolled in 2025-2026 ===\n";
$allEnrollments = StudentEnrollment::with(['student', 'academicYearStrandSection.section', 'academicYearStrandSection.strand'])
    ->where('academic_year_id', $activeYear->id)
    ->get();

echo "Total enrollments: {$allEnrollments->count()}\n\n";

foreach ($allEnrollments as $enrollment) {
    $student = $enrollment->student;
    $section = $enrollment->academicYearStrandSection->section ?? null;
    $strand = $enrollment->academicYearStrandSection->strand ?? null;
    
    echo "{$student->student_number}: {$student->first_name} {$student->last_name}\n";
    echo "  Strand: " . ($strand ? $strand->code : 'N/A') . "\n";
    echo "  Section: " . ($section ? $section->name : 'N/A') . "\n";
    echo "  Section Assignment ID: {$enrollment->academic_year_strand_section_id}\n\n";
}

echo "\n=== Recommendation ===\n";
echo "To assign students to ABM Section A - JUDE:\n";
echo "1. Go to Admin Panel → Student Enrollments\n";
echo "2. Create enrollment for each student\n";
echo "3. Select Academic Year: 2025-2026\n";
echo "4. Select Strand: ABM\n";
echo "5. Select Section: A - JUDE\n";
echo "6. Click Save\n";
