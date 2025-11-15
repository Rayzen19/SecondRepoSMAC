<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SubjectEnrollment;
use App\Models\StudentEnrollment;

echo "=== Checking John Raymond Barrogo's Subjects ===\n\n";

// Find the student user
$studentUser = User::where('email', 'johnraymond.barrogo@cvsu.edu.ph')
    ->where('type', 'student')
    ->first();

if (!$studentUser) {
    echo "❌ Student user not found!\n";
    exit;
}

echo "✅ Student User Found:\n";
echo "   User ID: {$studentUser->id}\n";
echo "   User PK ID: {$studentUser->user_pk_id}\n";
echo "   Email: {$studentUser->email}\n\n";

// Find the actual student record
$studentId = $studentUser->user_pk_id;
$student = Student::find($studentId);

if (!$student) {
    echo "❌ Student record not found!\n";
    exit;
}

echo "✅ Student Record Found:\n";
echo "   Student ID: {$student->id}\n";
echo "   Student Number: {$student->student_number}\n";
echo "   Name: {$student->first_name} {$student->last_name}\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();

if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit;
}

echo "✅ Active Academic Year:\n";
echo "   ID: {$activeYear->id}\n";
echo "   Name: {$activeYear->name}\n";
echo "   Semester: {$activeYear->semester}\n\n";

// Check student enrollment
$studentEnrollment = StudentEnrollment::where('student_id', $studentId)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if (!$studentEnrollment) {
    echo "❌ Student is NOT enrolled in the active academic year!\n";
    echo "   Looking for: student_id={$studentId}, academic_year_id={$activeYear->id}\n\n";
    
    // Check all enrollments for this student
    $allEnrollments = StudentEnrollment::where('student_id', $studentId)->get();
    echo "All enrollments for this student:\n";
    foreach ($allEnrollments as $enr) {
        echo "   - Academic Year ID: {$enr->academic_year_id}, Section ID: {$enr->section_id}\n";
    }
    exit;
}

echo "✅ Student Enrollment Found:\n";
echo "   ID: {$studentEnrollment->id}\n";
echo "   Section ID: {$studentEnrollment->section_id}\n";
echo "   Section: {$studentEnrollment->section->name}\n\n";

// Check subject enrollments
$subjectEnrollments = SubjectEnrollment::where('student_enrollment_id', $studentEnrollment->id)
    ->with(['academicYearStrandSubject.subject', 'academicYearStrandSubject.teacher'])
    ->get();

echo "📚 Subject Enrollments:\n";
if ($subjectEnrollments->isEmpty()) {
    echo "   ❌ No subject enrollments found!\n";
} else {
    echo "   ✅ Found {$subjectEnrollments->count()} subject enrollments:\n\n";
    foreach ($subjectEnrollments as $se) {
        $ays = $se->academicYearStrandSubject;
        $subject = $ays->subject;
        echo "   - {$subject->code}: {$subject->name}\n";
        echo "     Type: {$subject->type}\n";
        echo "     Teacher: " . ($ays->teacher ? $ays->teacher->first_name . ' ' . $ays->teacher->last_name : 'None') . "\n";
        echo "     AYSS ID: {$ays->id}\n\n";
    }
}

echo "\n=== Testing Controller Logic ===\n\n";

// Simulate what the controller does
$enrollments = SubjectEnrollment::with([
        'academicYearStrandSubject.subject',
        'academicYearStrandSubject.teacher',
        'academicYearStrandSubject.strand',
    ])
    ->whereHas('studentEnrollment', function ($q) use ($studentId, $activeYear) {
        $q->where('student_id', $studentId)
          ->where('academic_year_id', $activeYear->id);
    })
    ->get();

echo "Query Results: {$enrollments->count()} records\n";

if ($enrollments->count() > 0) {
    echo "\nSubjects grouped by type:\n";
    
    $subjects = $enrollments->map(function ($se) {
        $ays = $se->academicYearStrandSubject;
        $subject = $ays->subject;
        return [
            'subject_name' => $subject?->name,
            'subject_code' => $subject?->code,
            'subject_type' => $subject?->type,
        ];
    });
    
    $coreSubjects = $subjects->where('subject_type', 'core');
    $appliedSubjects = $subjects->where('subject_type', 'applied');
    $specializedSubjects = $subjects->where('subject_type', 'specialized');
    
    echo "  - Core: " . count($coreSubjects) . "\n";
    echo "  - Applied: " . count($appliedSubjects) . "\n";
    echo "  - Specialized: " . count($specializedSubjects) . "\n";
}
