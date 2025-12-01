<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AcademicYear;
use App\Models\Strand;
use App\Models\Section;

echo "=== Testing getSectionStudents Logic ===\n\n";

// Simulate the controller method
$validated = [
    'strand_code' => 'ABM',
    'section_id' => 1, // G-11 A - JUDE
];

echo "Input Parameters:\n";
echo "  Strand Code: {$validated['strand_code']}\n";
echo "  Section ID: {$validated['section_id']}\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found\n";
    exit;
}

echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Find the strand
$strand = Strand::where('code', $validated['strand_code'])->first();
if (!$strand) {
    echo "❌ Strand not found\n";
    exit;
}

echo "Strand: {$strand->code} (ID: {$strand->id})\n\n";

// Find the academic_year_strand_section record for the active year
$academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('section_id', $validated['section_id'])
    ->first();

if (!$academicYearStrandSection) {
    echo "❌ No AYSS record found for active year\n";
    echo "   Looking for: academic_year_id={$activeYear->id}, strand_id={$strand->id}, section_id={$validated['section_id']}\n\n";
    
    // Fallback
    $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('strand_id', $strand->id)
        ->where('section_id', $validated['section_id'])
        ->orderByDesc('id')
        ->first();
    
    if ($academicYearStrandSection) {
        echo "✅ Using fallback AYSS record (most recent)\n";
        echo "   AYSS ID: {$academicYearStrandSection->id}\n";
        echo "   Academic Year ID: {$academicYearStrandSection->academic_year_id}\n\n";
    }
} else {
    echo "✅ AYSS Record Found:\n";
    echo "   AYSS ID: {$academicYearStrandSection->id}\n";
    echo "   Academic Year ID: {$academicYearStrandSection->academic_year_id}\n\n";
}

$studentsArray = [];
$studentIds = [];

// Get enrolled students from database
if ($academicYearStrandSection) {
    echo "Fetching enrollments for AYSS ID: {$academicYearStrandSection->id}\n\n";
    
    $enrollments = \App\Models\StudentEnrollment::with('student')
        ->where('academic_year_strand_section_id', $academicYearStrandSection->id)
        ->get();

    echo "Found {$enrollments->count()} enrollments\n\n";

    foreach ($enrollments as $enrollment) {
        if ($enrollment->student) {
            $studentIds[] = $enrollment->student->id;
            $studentsArray[] = [
                'id' => $enrollment->student->id,
                'student_number' => $enrollment->student->student_number,
                'first_name' => $enrollment->student->first_name,
                'last_name' => $enrollment->student->last_name,
                'middle_name' => $enrollment->student->middle_name,
                'program' => $enrollment->student->program,
                'academic_year' => $enrollment->student->academic_year,
                'registration_number' => $enrollment->registration_number,
                'source' => 'database'
            ];
            
            echo "   - {$enrollment->student->first_name} {$enrollment->student->last_name} (ID: {$enrollment->student->id})\n";
        }
    }
}

echo "\n\n=== Result ===\n";
echo "Total Students: " . count($studentsArray) . "\n";

if (count($studentsArray) > 0) {
    echo "\nStudents:\n";
    foreach ($studentsArray as $student) {
        echo "   - {$student['first_name']} {$student['last_name']} ({$student['student_number']})\n";
    }
}

// Now test for G-12 A - JOB
echo "\n\n=== Testing G-12 A - JOB (Section ID 24) ===\n\n";

$validated2 = [
    'strand_code' => 'ABM',
    'section_id' => 24, // G-12 A - JOB
];

$academicYearStrandSection2 = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('section_id', $validated2['section_id'])
    ->first();

if ($academicYearStrandSection2) {
    echo "AYSS ID: {$academicYearStrandSection2->id}\n";
    
    $enrollments2 = \App\Models\StudentEnrollment::with('student')
        ->where('academic_year_strand_section_id', $academicYearStrandSection2->id)
        ->get();

    echo "Found {$enrollments2->count()} enrollments\n\n";

    foreach ($enrollments2 as $enrollment) {
        if ($enrollment->student) {
            echo "   - {$enrollment->student->first_name} {$enrollment->student->last_name} (ID: {$enrollment->student->id})\n";
        }
    }
}
