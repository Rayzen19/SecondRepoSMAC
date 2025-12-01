<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\AcademicYear;

echo "=== Checking Pre-Enrollment Data for John Raymond Barrogo ===\n\n";

// Find the student
$user = \App\Models\User::where('email', 'johnraymond.barrogo@cvsu.edu.ph')
    ->where('type', 'student')
    ->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit;
}

$student = Student::find($user->user_pk_id);

echo "✅ Student Found: {$student->first_name} {$student->last_name} (ID: {$student->id})\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Check for pre-enrollment records
$preEnrollments = \App\Models\PreEnrollment::where('student_id', $student->id)->get();

echo "📋 Pre-Enrollment Records:\n";
echo "   Total: {$preEnrollments->count()}\n\n";

foreach ($preEnrollments as $pre) {
    echo "   Pre-Enrollment ID: {$pre->id}\n";
    echo "   Status: {$pre->status}\n";
    echo "   Grade Level: {$pre->grade_level}\n";
    echo "   Current Academic Year ID: {$pre->current_academic_year_id}\n";
    echo "   Target Academic Year ID: " . ($pre->target_academic_year_id ?? 'NULL') . "\n";
    echo "   Strand ID: {$pre->strand_id}\n";
    echo "   Section ID: " . ($pre->section_id ?? 'NULL') . "\n";
    
    if ($pre->section) {
        echo "   Section Name: {$pre->section->name}\n";
        echo "   Section Grade: {$pre->section->grade}\n";
    }
    
    echo "   Created At: {$pre->created_at}\n";
    echo "   ---\n\n";
}

// Check which section the pre-enrollment points to
if ($preEnrollments->count() > 0) {
    foreach ($preEnrollments as $pre) {
        if ($pre->section_id) {
            $section = \App\Models\Section::find($pre->section_id);
            if ($section) {
                echo "Section {$section->name} (ID: {$section->id}) - Grade: {$section->grade}\n";
            }
        }
    }
}
