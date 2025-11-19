<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Section Students Query (Same as getSectionStudents API) ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Test for ABM - G-12 A - JOB
$testCases = [
    ['strand_code' => 'ABM', 'section_id' => 2, 'section_name' => 'G-12 A - JOB'],
    ['strand_code' => 'STEM', 'section_id' => 7, 'section_name' => 'G-11 A - LUKE'], // John Raymond should be here
];

foreach ($testCases as $test) {
    echo "========================================\n";
    echo "Testing: {$test['strand_code']} - Section ID {$test['section_id']} ({$test['section_name']})\n";
    echo "========================================\n\n";
    
    // Find the strand
    $strand = \App\Models\Strand::where('code', $test['strand_code'])->first();
    if (!$strand) {
        echo "❌ Strand not found!\n\n";
        continue;
    }
    
    echo "Strand: {$strand->code} (ID: {$strand->id})\n";
    echo "Section ID: {$test['section_id']}\n\n";
    
    // Find the academic_year_strand_section record for the active year
    $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $strand->id)
        ->where('section_id', $test['section_id'])
        ->first();
    
    if (!$academicYearStrandSection) {
        echo "❌ No AcademicYearStrandSection found for active year.\n";
        echo "Looking for fallback (most recent)...\n";
        
        $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('strand_id', $strand->id)
            ->where('section_id', $test['section_id'])
            ->orderByDesc('id')
            ->first();
        
        if (!$academicYearStrandSection) {
            echo "❌ No fallback found either!\n\n";
            continue;
        }
        
        echo "✓ Found fallback AYSS ID: {$academicYearStrandSection->id}\n";
        echo "  Academic Year: {$academicYearStrandSection->academic_year_id}\n\n";
    } else {
        echo "✓ Found AYSS ID: {$academicYearStrandSection->id}\n\n";
    }
    
    // Get enrolled students (same query as getSectionStudents)
    $enrollments = \App\Models\StudentEnrollment::with('student')
        ->where('academic_year_strand_section_id', $academicYearStrandSection->id)
        ->get();
    
    echo "Total enrollments found: {$enrollments->count()}\n\n";
    
    if ($enrollments->isEmpty()) {
        echo "⚠️ NO STUDENTS ENROLLED\n\n";
    } else {
        echo "Students:\n";
        foreach ($enrollments as $enrollment) {
            echo "  - {$enrollment->student->student_number}: {$enrollment->student->first_name} {$enrollment->student->last_name}\n";
            echo "    Status: {$enrollment->status}, Registration: {$enrollment->registration_number}\n";
        }
        echo "\n";
    }
}

echo "=== Done ===\n";
