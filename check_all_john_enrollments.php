<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;

echo "=== Checking ALL Historical Enrollments for John Raymond Barrogo ===\n\n";

// Find the student
$user = \App\Models\User::where('email', 'johnraymond.barrogo@cvsu.edu.ph')
    ->where('type', 'student')
    ->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit;
}

$student = Student::find($user->user_pk_id);

echo "Student: {$student->first_name} {$student->last_name} (ID: {$student->id})\n\n";

// Get ALL enrollments ever created for this student
$allEnrollments = StudentEnrollment::withTrashed() // Include soft-deleted records
    ->where('student_id', $student->id)
    ->with(['academicYearStrandSection.section', 'academicYearStrandSection.strand', 'academicYear'])
    ->orderBy('id')
    ->get();

echo "Total Enrollments (including deleted): {$allEnrollments->count()}\n\n";

foreach ($allEnrollments as $enrollment) {
    echo "Enrollment ID: {$enrollment->id}\n";
    echo "  Academic Year: " . ($enrollment->academicYear ? $enrollment->academicYear->name : 'N/A') . " (ID: {$enrollment->academic_year_id})\n";
    echo "  AYSS ID: {$enrollment->academic_year_strand_section_id}\n";
    
    if ($enrollment->academicYearStrandSection) {
        $ayss = $enrollment->academicYearStrandSection;
        echo "  Section: " . ($ayss->section ? $ayss->section->name : 'N/A') . "\n";
        echo "  Grade: " . ($ayss->section ? $ayss->section->grade : 'N/A') . "\n";
        echo "  Strand: " . ($ayss->strand ? $ayss->strand->code : 'N/A') . "\n";
    }
    
    echo "  Registration Number: " . ($enrollment->registration_number ?? 'NULL') . "\n";
    echo "  Status: " . ($enrollment->status ?? 'N/A') . "\n";
    echo "  Deleted: " . ($enrollment->deleted_at ? 'Yes (' . $enrollment->deleted_at . ')' : 'No') . "\n";
    echo "  Created: {$enrollment->created_at}\n";
    echo "  ---\n\n";
}

// Check for any AYSS records that might reference G-11 sections
echo "\n=== Checking if student was EVER associated with G-11 ===\n\n";

$g11Enrollments = StudentEnrollment::withTrashed()
    ->where('student_id', $student->id)
    ->whereHas('academicYearStrandSection.section', function($q) {
        $q->where('grade', 'G-11');
    })
    ->count();

echo "G-11 Enrollments: {$g11Enrollments}\n";

$g12Enrollments = StudentEnrollment::withTrashed()
    ->where('student_id', $student->id)
    ->whereHas('academicYearStrandSection.section', function($q) {
        $q->where('grade', 'G-12');
    })
    ->count();

echo "G-12 Enrollments: {$g12Enrollments}\n";
