<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ALL SECTIONS WITH ENROLLED STUDENTS ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Academic Year: {$activeYear->name}\n\n";

// Get all AYSS with enrollments
$sectionsWithStudents = \App\Models\AcademicYearStrandSection::with(['strand', 'section'])
    ->where('academic_year_id', $activeYear->id)
    ->get()
    ->filter(function($ayss) {
        return \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count() > 0;
    })
    ->sortBy(function($ayss) {
        return $ayss->strand->code . $ayss->section->grade . $ayss->section->name;
    });

if ($sectionsWithStudents->isEmpty()) {
    echo "❌ No sections have enrolled students yet.\n";
    exit;
}

echo "Sections with students:\n";
echo "======================\n\n";

foreach ($sectionsWithStudents as $ayss) {
    $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
    $students = \App\Models\StudentEnrollment::with('student')
        ->where('academic_year_strand_section_id', $ayss->id)
        ->get();
    
    echo "📌 {$ayss->strand->code} - {$ayss->section->grade} {$ayss->section->name}\n";
    echo "   Frontend Key: {$ayss->strand->code}-{$ayss->section_id}\n";
    echo "   Total: {$count} student(s)\n";
    echo "   Students:\n";
    
    foreach ($students as $enrollment) {
        $s = $enrollment->student;
        echo "      • {$s->student_number}: {$s->first_name} {$s->last_name}\n";
    }
    echo "\n";
}

echo "=== Done ===\n";
