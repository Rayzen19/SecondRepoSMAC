<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debugging Student Dashboard Issue ===\n\n";

$student = \App\Models\Student::where('student_number', '2025-00005')->first();
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

echo "Student: {$student->first_name} {$student->last_name}\n";
echo "Student ID: {$student->id}\n";
echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Check what login stores in session
echo "=== What Login Would Store ===\n";
echo "academic_year_id from student record: " . ($student->academic_year_id ?? 'NULL') . "\n";
echo "program from student record: " . ($student->program ?? 'NULL') . "\n";
echo "academic_year from student record: " . ($student->academic_year ?? 'NULL') . "\n\n";

// Check enrollment
$enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

echo "=== Enrollment Check ===\n";
if ($enrollment) {
    echo "✅ Has enrollment for active year\n";
    echo "   Enrollment ID: {$enrollment->id}\n";
    echo "   Status: {$enrollment->status}\n";
} else {
    echo "❌ NO enrollment for active year\n";
}

$anyEnrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)->count();
echo "   Total enrollments: {$anyEnrollment}\n\n";

// Check if the issue is with the student record itself
echo "=== Student Record Check ===\n";
$user = \App\Models\User::where('email', $student->email)->where('user_type', 'student')->first();
if ($user) {
    echo "✅ User record exists\n";
    echo "   User ID: {$user->id}\n";
    echo "   User PK ID: {$user->user_pk_id}\n";
    echo "   Expected Student ID: {$student->id}\n";
    
    if ($user->user_pk_id != $student->id) {
        echo "   ⚠️ WARNING: user_pk_id doesn't match student ID!\n";
    }
} else {
    echo "❌ No user record found\n";
}

echo "\n=== DIAGNOSIS ===\n";
if (!$enrollment) {
    echo "❌ PROBLEM: Student has no enrollment for the active academic year\n";
    echo "   This causes the 'not enrolled' error when accessing pre-enrollment\n";
    echo "   The enrollment exists but might be for a different year\n\n";
    
    $allEnrollments = \App\Models\StudentEnrollment::where('student_id', $student->id)
        ->with('academicYear')
        ->get();
    
    echo "All enrollments for this student:\n";
    foreach ($allEnrollments as $e) {
        $active = $e->academic_year_id == $activeYear->id ? ' ← CURRENT' : '';
        echo "  - Year: {$e->academicYear->name}, Status: {$e->status}{$active}\n";
    }
} else {
    echo "✅ Student should be able to access dashboard normally\n";
    echo "   If seeing error, it might be a caching or session issue\n";
}

echo "\n=== Done ===\n";
