<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use App\Models\StrandSubject;
use App\Models\AcademicYearStrandSubject;

echo "=== Testing New Subject Display Logic ===\n\n";

// Test with the TVL-CP student (John Raymond Barrogo)
$student = Student::where('student_number', '2025-00001')->first();
if (!$student) {
    echo "Student not found!\n";
    exit(1);
}

echo "Testing for: {$student->first_name} {$student->last_name} ({$student->student_number})\n\n";

$activeYear = AcademicYear::where('is_active', true)->first();
echo "Active Year: {$activeYear->display_name}\n\n";

// Get student enrollment
$studentEnrollment = StudentEnrollment::with(['strand', 'academicYearStrandSection.section'])
    ->where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if (!$studentEnrollment) {
    echo "No enrollment found!\n";
    exit(1);
}

echo "Enrollment Info:\n";
echo "  Strand: {$studentEnrollment->strand->code} ({$studentEnrollment->strand->name})\n";
echo "  Section: {$studentEnrollment->academicYearStrandSection->section->name}\n";
echo "  Grade: {$studentEnrollment->academicYearStrandSection->section->grade}\n\n";

$strandId = $studentEnrollment->strand_id;
$gradeLevel = $studentEnrollment->academicYearStrandSection->section->grade;
echo "  Grade raw: {$gradeLevel}\n";

// Extract numeric grade level (e.g., "G-11" -> "11")
$numericGradeLevel = str_replace(['G-', 'Grade ', 'Grade-'], '', $gradeLevel);
echo "  Grade numeric: {$numericGradeLevel}\n\n";

// Get subjects from StrandSubject
echo "📚 Subjects available for this strand (from StrandSubject):\n";
$strandSubjects = StrandSubject::with(['subject'])
    ->where('strand_id', $strandId)
    ->where(function ($q) use ($numericGradeLevel) {
        $q->where('grade_level', $numericGradeLevel)
          ->orWhereNull('grade_level');
    })
    ->where('is_active', true)
    ->get();

echo "Total: {$strandSubjects->count()} subjects\n\n";

foreach ($strandSubjects as $strandSubject) {
    $subject = $strandSubject->subject;
    echo "[{$subject->code}] {$subject->name}\n";
    echo "  Type: {$subject->type} | Semester: {$subject->semester} | Units: {$subject->units}\n";
    
    // Check if teacher assigned
    $academicYearStrandSubject = AcademicYearStrandSubject::with(['teacher'])
        ->where('academic_year_id', $activeYear->id)
        ->where('strand_id', $strandId)
        ->where('subject_id', $subject->id)
        ->first();
    
    if ($academicYearStrandSubject && $academicYearStrandSubject->teacher) {
        $teacher = $academicYearStrandSubject->teacher;
        echo "  ✅ Teacher: {$teacher->last_name}, {$teacher->first_name}\n";
    } else {
        echo "  ⚠️ Teacher: Not assigned yet\n";
    }
    
    echo "\n";
}

echo "=== Test Complete ===\n";
