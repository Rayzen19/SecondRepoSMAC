<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSING STUDENT DATA ISSUE ===\n\n";

// Get student
$student = App\Models\Student::where('student_number', '2025-00022')->first();
echo "Student: {$student->name} (ID: {$student->id})\n";
echo "Student Number: {$student->student_number}\n\n";

// Check enrollments
$enrollments = App\Models\StudentEnrollment::where('student_id', $student->id)->get();
echo "Total Student Enrollments: " . $enrollments->count() . "\n";

if ($enrollments->count() > 0) {
    foreach ($enrollments as $enrollment) {
        $year = $enrollment->academicYear;
        $subjects = $enrollment->subjectEnrollments()->count();
        echo "  - {$year->name} ({$year->semester} Sem): {$subjects} subjects\n";
    }
} else {
    echo "  ❌ NO ENROLLMENTS FOUND!\n";
}

echo "\n";

// Check available academic years
echo "Available Academic Years:\n";
$years = App\Models\AcademicYear::orderBy('name', 'desc')->get();
foreach ($years as $year) {
    $active = $year->is_active ? '✓ ACTIVE' : '';
    echo "  - {$year->name} ({$year->semester} Semester) {$active}\n";
}

echo "\n";

// Check guardian link
$guardian = App\Models\Guardian::whereHas('students', function($q) use ($student) {
    $q->where('students.id', $student->id);
})->first();

if ($guardian) {
    echo "Guardian: {$guardian->name}\n";
    echo "Guardian has " . $guardian->students()->count() . " student(s) linked\n";
} else {
    echo "❌ NO GUARDIAN LINKED!\n";
}

echo "\n=== DIAGNOSIS COMPLETE ===\n";
