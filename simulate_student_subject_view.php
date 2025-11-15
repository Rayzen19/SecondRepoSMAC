<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;

echo "=== Simulating Student Subject View ===\n\n";

// Simulate the student login
$studentNumber = '2025-00001';
$student = Student::where('student_number', $studentNumber)->first();

if (!$student) {
    echo "Student not found!\n";
    exit(1);
}

// Find the user account
$user = User::where('type', 'student')
    ->where('user_pk_id', $student->id)
    ->first();

if (!$user) {
    echo "User account not found for student!\n";
    exit(1);
}

echo "Logged in as: {$student->first_name} {$student->last_name} ({$student->student_number})\n";
echo "Email: {$user->email}\n\n";

// Simulate the controller logic
$controller = new \App\Http\Controllers\Student\SubjectController();

// Use reflection to call the index method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('index');
$method->setAccessible(true);

// Mock the request
$request = new \Illuminate\Http\Request();

// Mock Auth
\Illuminate\Support\Facades\Auth::shouldReceive('guard')
    ->with('student')
    ->andReturnSelf();
\Illuminate\Support\Facades\Auth::shouldReceive('user')
    ->andReturn($user);

try {
    // This would normally return a view, but we'll just check the data
    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
    $studentId = $user->user_pk_id;
    
    echo "=== Subject List Preview ===\n\n";
    echo "Academic Year: {$activeYear->display_name}\n";
    echo "Semester: {$activeYear->semester}\n\n";
    
    // Get student enrollment
    $studentEnrollment = \App\Models\StudentEnrollment::with(['strand', 'academicYearStrandSection.section'])
        ->where('student_id', $studentId)
        ->where('academic_year_id', $activeYear->id)
        ->first();
    
    if (!$studentEnrollment) {
        echo "No enrollment found!\n";
        exit(1);
    }
    
    $strandId = $studentEnrollment->strand_id;
    $gradeLevel = $studentEnrollment->academicYearStrandSection?->section?->grade;
    $numericGradeLevel = str_replace(['G-', 'Grade ', 'Grade-'], '', $gradeLevel);
    
    // Get subjects
    $strandSubjects = \App\Models\StrandSubject::with(['subject'])
        ->where('strand_id', $strandId)
        ->where(function ($q) use ($numericGradeLevel) {
            $q->where('grade_level', $numericGradeLevel)
              ->orWhereNull('grade_level');
        })
        ->where('is_active', true)
        ->get();
    
    $subjects = $strandSubjects->map(function ($strandSubject) use ($activeYear, $strandId, $studentEnrollment) {
        $subject = $strandSubject->subject;
        
        $academicYearStrandSubject = \App\Models\AcademicYearStrandSubject::with(['teacher'])
            ->where('academic_year_id', $activeYear->id)
            ->where('strand_id', $strandId)
            ->where('subject_id', $subject->id)
            ->first();
        
        $teacher = $academicYearStrandSubject?->teacher;
        
        return [
            'subject_code' => $subject->code,
            'subject_name' => $subject->name,
            'subject_type' => $subject->type,
            'semester' => $subject->semester,
            'units' => $subject->units,
            'strand' => $studentEnrollment->strand->code,
            'teacher' => $teacher && $teacher->last_name
                ? ($teacher->last_name . ', ' . $teacher->first_name)
                : 'Not assigned yet',
        ];
    });
    
    $coreSubjects = $subjects->where('subject_type', 'core');
    $appliedSubjects = $subjects->where('subject_type', 'applied');
    $specializedSubjects = $subjects->where('subject_type', 'specialized');
    
    echo "Total Subjects: " . $subjects->count() . "\n\n";
    
    if ($coreSubjects->count() > 0) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📘 Senior High School Core Curriculum Subjects ({$coreSubjects->count()})\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        foreach ($coreSubjects as $subject) {
            echo "[{$subject['subject_code']}] {$subject['subject_name']}\n";
            echo "  Teacher: {$subject['teacher']}\n";
            echo "  Semester: {$subject['semester']} | Units: {$subject['units']}\n\n";
        }
    }
    
    if ($appliedSubjects->count() > 0) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "💼 Senior High School Applied Track Subjects ({$appliedSubjects->count()})\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        foreach ($appliedSubjects as $subject) {
            echo "[{$subject['subject_code']}] {$subject['subject_name']}\n";
            echo "  Teacher: {$subject['teacher']}\n";
            echo "  Semester: {$subject['semester']} | Units: {$subject['units']}\n\n";
        }
    }
    
    if ($specializedSubjects->count() > 0) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "⭐ Senior High School Specialized Subjects ({$specializedSubjects->count()})\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        foreach ($specializedSubjects as $subject) {
            echo "[{$subject['subject_code']}] {$subject['subject_name']}\n";
            echo "  Teacher: {$subject['teacher']}\n";
            echo "  Semester: {$subject['semester']} | Units: {$subject['units']}\n\n";
        }
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ Preview Complete\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
