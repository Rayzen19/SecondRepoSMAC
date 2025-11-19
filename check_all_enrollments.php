<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking All Student Enrollments ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

// Get all enrollments for active year
$enrollments = \App\Models\StudentEnrollment::where('academic_year_id', $activeYear->id)
    ->with(['student', 'strand', 'academicYearStrandSection.section'])
    ->get();

echo "Total enrollments for {$activeYear->name}: " . $enrollments->count() . "\n\n";

echo "ABM Students:\n";
foreach ($enrollments as $enrollment) {
    if ($enrollment->strand && $enrollment->strand->code === 'ABM') {
        $sectionName = 'No section';
        if ($enrollment->academicYearStrandSection && $enrollment->academicYearStrandSection->section) {
            $sectionName = $enrollment->academicYearStrandSection->section->name;
        } elseif ($enrollment->academic_year_strand_section_id) {
            $sectionName = "AYSS ID: {$enrollment->academic_year_strand_section_id} (section not loaded)";
        }
        
        echo "  - {$enrollment->student->student_number}: {$enrollment->student->first_name} {$enrollment->student->last_name}\n";
        echo "    Section: {$sectionName}\n";
        echo "    AYSS ID: " . ($enrollment->academic_year_strand_section_id ?? 'NULL') . "\n";
        echo "    Enrollment ID: {$enrollment->id}\n\n";
    }
}
