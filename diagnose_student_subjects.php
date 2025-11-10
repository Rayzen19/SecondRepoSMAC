<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Diagnosing Student Subject Display Issue\n";
echo "=========================================\n\n";

// Find John Raymond as a student
$student = App\Models\Student::where('student_number', '2025-00021')->first();

if (!$student) {
    echo "❌ Student not found!\n";
    exit(1);
}

echo "✅ Student: {$student->first_name} {$student->last_name}\n";
echo "   ID: {$student->id}\n";
echo "   Student No: {$student->student_number}\n\n";

// Get active academic year
$activeYear = App\Models\AcademicYear::where('is_active', true)->first();

if (!$activeYear) {
    echo "❌ No active academic year!\n";
    exit(1);
}

echo "✅ Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Check student enrollment
$studentEnrollment = App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if (!$studentEnrollment) {
    echo "❌ No student enrollment found for current academic year!\n";
    echo "   Student needs to be enrolled first.\n";
    exit(1);
}

echo "✅ Student Enrollment:\n";
echo "   Enrollment ID: {$studentEnrollment->id}\n";
echo "   Status: {$studentEnrollment->status}\n";
echo "   Section Assignment ID: {$studentEnrollment->academic_year_strand_section_id}\n\n";

// Check subject enrollments
$subjectEnrollments = App\Models\SubjectEnrollment::with([
    'academicYearStrandSubject.subject',
    'academicYearStrandSubject.teacher',
    'studentEnrollment'
])
->whereHas('studentEnrollment', function ($q) use ($student, $activeYear) {
    $q->where('student_id', $student->id)
      ->where('academic_year_id', $activeYear->id);
})
->get();

echo "📚 Subject Enrollments: {$subjectEnrollments->count()} found\n\n";

if ($subjectEnrollments->isEmpty()) {
    echo "❌ NO SUBJECT ENROLLMENTS FOUND!\n\n";
    
    echo "Checking what subjects should be available:\n";
    echo "--------------------------------------------\n";
    
    if ($studentEnrollment->academic_year_strand_section_id) {
        $sectionAssignment = App\Models\AcademicYearStrandSection::with(['strand', 'section'])
            ->find($studentEnrollment->academic_year_strand_section_id);
        
        if ($sectionAssignment) {
            echo "Student's Section: {$sectionAssignment->section->name} ({$sectionAssignment->section->grade})\n";
            echo "Student's Strand: {$sectionAssignment->strand->code}\n\n";
            
            // Check available subjects for this strand
            $availableSubjects = App\Models\AcademicYearStrandSubject::with(['subject', 'teacher'])
                ->where('academic_year_id', $activeYear->id)
                ->where('strand_id', $sectionAssignment->strand_id)
                ->get();
            
            echo "Available subjects for {$sectionAssignment->strand->code}:\n";
            echo "  Found: {$availableSubjects->count()} subject(s)\n\n";
            
            foreach ($availableSubjects as $subj) {
                $teacher = $subj->teacher ? "{$subj->teacher->last_name}, {$subj->teacher->first_name}" : 'No teacher';
                echo "  - {$subj->subject->name} ({$subj->subject->code})\n";
                echo "    Teacher: {$teacher}\n";
                echo "    AYS ID: {$subj->id}\n";
            }
            
            echo "\n💡 Solution: Need to create SubjectEnrollment records!\n";
            echo "   This should happen automatically via StudentEnrollment->syncSubjectEnrollments()\n";
        }
    }
} else {
    echo "✅ Subject Enrollments Found:\n\n";
    
    foreach ($subjectEnrollments as $se) {
        $ays = $se->academicYearStrandSubject;
        $subject = $ays->subject;
        $teacher = $ays->teacher;
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Subject: {$subject->name} ({$subject->code})\n";
        echo "  Subject Enrollment ID: {$se->id}\n";
        echo "  AYS ID: {$ays->id}\n";
        echo "  Teacher: " . ($teacher ? "{$teacher->last_name}, {$teacher->first_name}" : 'None') . "\n";
        echo "  Grades:\n";
        echo "    1st Quarter: " . ($se->fq_grade ?? 'N/A') . "\n";
        echo "    2nd Quarter: " . ($se->sq_grade ?? 'N/A') . "\n";
        echo "    Average: " . ($se->a_grade ?? 'N/A') . "\n";
        echo "    Final: " . ($se->f_grade ?? 'N/A') . "\n";
        echo "    Remarks: " . ($se->remarks ?? 'N/A') . "\n";
    }
    
    echo "\n✅ Subjects will display correctly!\n";
}

echo "\n🔍 Diagnosis complete!\n";
