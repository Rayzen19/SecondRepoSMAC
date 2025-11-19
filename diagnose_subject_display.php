<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SubjectEnrollment;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSubject;

echo "=== Subject Display Diagnostic ===\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit(1);
}

echo "✅ Active Academic Year: {$activeYear->display_name} (ID: {$activeYear->id})\n";
echo "   Semester: {$activeYear->semester}\n\n";

// Get all student enrollments for active year
$studentEnrollments = StudentEnrollment::with(['student', 'strand', 'academicYearStrandSection.section'])
    ->where('academic_year_id', $activeYear->id)
    ->get();

echo "📊 Total Student Enrollments: {$studentEnrollments->count()}\n\n";

foreach ($studentEnrollments as $enrollment) {
    $student = $enrollment->student;
    echo "Student: {$student->student_number} - {$student->first_name} {$student->last_name}\n";
    echo "  Enrollment ID: {$enrollment->id}\n";
    echo "  Strand: {$enrollment->strand?->code}\n";
    echo "  Section: {$enrollment->academicYearStrandSection?->section?->name}\n";
    echo "  Section Assignment ID: {$enrollment->academic_year_strand_section_id}\n";
    
    // Check subject enrollments
    $subjectEnrollments = SubjectEnrollment::with([
        'academicYearStrandSubject.subject',
        'academicYearStrandSubject.teacher'
    ])
    ->where('student_enrollment_id', $enrollment->id)
    ->get();
    
    echo "  Subject Enrollments: {$subjectEnrollments->count()}\n";
    
    if ($subjectEnrollments->isEmpty()) {
        echo "  ⚠️ NO SUBJECTS ENROLLED!\n";
        
        // Check available subjects for this section
        $availableSubjects = AcademicYearStrandSubject::with(['subject', 'teacher'])
            ->where('academic_year_id', $activeYear->id)
            ->where('academic_year_strand_section_id', $enrollment->academic_year_strand_section_id)
            ->get();
        
        echo "  Available subjects for this section: {$availableSubjects->count()}\n";
        
        if ($availableSubjects->count() > 0) {
            echo "  📋 Subjects that SHOULD be visible:\n";
            foreach ($availableSubjects as $ays) {
                $subject = $ays->subject;
                $teacher = $ays->teacher;
                $teacherName = $teacher ? "{$teacher->last_name}, {$teacher->first_name}" : "No teacher assigned";
                echo "     - [{$subject->code}] {$subject->name} | Teacher: {$teacherName}\n";
            }
            echo "  ⚡ These subjects need to be synced to SubjectEnrollment!\n";
        }
    } else {
        foreach ($subjectEnrollments as $se) {
            $subject = $se->academicYearStrandSubject?->subject;
            $teacher = $se->academicYearStrandSubject?->teacher;
            $teacherName = $teacher ? "{$teacher->last_name}, {$teacher->first_name}" : "No teacher assigned";
            echo "     - [{$subject?->code}] {$subject?->name} | Teacher: {$teacherName}\n";
        }
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
$totalWithoutSubjects = $studentEnrollments->filter(function($enrollment) {
    return SubjectEnrollment::where('student_enrollment_id', $enrollment->id)->count() === 0;
})->count();

echo "Students without any subject enrollments: {$totalWithoutSubjects}\n";

if ($totalWithoutSubjects > 0) {
    echo "\n⚠️ ACTION NEEDED: Run sync to create missing SubjectEnrollment records\n";
    echo "Would you like to sync now? (This will create SubjectEnrollment records)\n";
}
