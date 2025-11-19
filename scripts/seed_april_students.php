<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\StudentEnrollment;

// Get first 10 students from the database
$students = Student::limit(10)->get();

if ($students->isEmpty()) {
    echo "No students found in database. Please add students first.\n";
    exit(1);
}

$count = 0;
foreach ($students as $student) {
    // Check if already enrolled
    $existing = StudentEnrollment::where('student_id', $student->id)
        ->where('academic_year_id', 5)
        ->first();
    
    if ($existing) {
        echo "Student {$student->student_number} already enrolled, skipping...\n";
        continue;
    }
    
    try {
        StudentEnrollment::create([
            'student_id' => $student->id,
            'strand_id' => 2, // STEM strand
            'academic_year_id' => 5, // Active academic year
            'academic_year_strand_section_id' => 2, // APRIL section
            'registration_number' => 'REG-2025-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
            'status' => 'enrolled'
        ]);
        
        echo "✓ Enrolled: {$student->first_name} {$student->last_name} ({$student->student_number})\n";
        $count++;
    } catch (Exception $e) {
        echo "✗ Error enrolling {$student->student_number}: {$e->getMessage()}\n";
    }
}

echo "\n";
echo "========================================\n";
echo "Successfully enrolled {$count} students in APRIL section!\n";
echo "========================================\n";
echo "\nNow refresh the Section & Advisers page to see the count updated.\n";
