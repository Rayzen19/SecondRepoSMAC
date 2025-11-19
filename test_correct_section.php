<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Correct Section ID 10 for STEM ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
$strand = \App\Models\Strand::where('code', 'STEM')->first();

echo "Looking for AYSS with:\n";
echo "  Academic Year ID: {$activeYear->id}\n";
echo "  Strand ID: {$strand->id}\n";
echo "  Section ID: 10\n\n";

$ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('section_id', 10)
    ->first();

if (!$ayss) {
    echo "❌ AYSS not found!\n";
    exit(1);
}

echo "✓ AYSS Found: ID {$ayss->id}\n\n";

echo "Querying enrollments for AYSS ID {$ayss->id}...\n";
$enrollments = \App\Models\StudentEnrollment::with('student')
    ->where('academic_year_strand_section_id', $ayss->id)
    ->get();

echo "Total enrollments: {$enrollments->count()}\n\n";

if ($enrollments->isEmpty()) {
    echo "❌ NO STUDENTS FOUND!\n";
    echo "This is the problem - students should be appearing here.\n";
} else {
    echo "✓ Students found:\n";
    foreach ($enrollments as $enrollment) {
        echo "  - {$enrollment->student->student_number}: {$enrollment->student->first_name} {$enrollment->student->last_name}\n";
    }
}

echo "\n=== Done ===\n";
