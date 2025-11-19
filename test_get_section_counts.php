<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing getSectionCounts API ===\n\n";

// This simulates what the frontend calls
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found\n";
    exit(1);
}

echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Get all sections with their student counts
$sections = \App\Models\Section::with('strand')->get();
$counts = [];

foreach ($sections as $section) {
    if (!$section->strand) continue;

    $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $section->strand->id)
        ->where('section_id', $section->id)
        ->first();

    // Fallback: Use latest mapping when none exists for the active year
    if (!$academicYearStrandSection) {
        $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('strand_id', $section->strand->id)
            ->where('section_id', $section->id)
            ->orderByDesc('id')
            ->first();
    }

    $count = 0;
    if ($academicYearStrandSection) {
        // Count all enrollments linked to the section
        $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $academicYearStrandSection->id)
            ->count();
    }

    $key = $section->strand->code . '-' . $section->id;
    $counts[$key] = $count;
    
    // Only show sections with students
    if ($count > 0) {
        echo "✓ {$key} ({$section->name}): {$count} student(s)\n";
    }
}

echo "\n=== Summary ===\n";
$totalWithStudents = count(array_filter($counts, fn($c) => $c > 0));
echo "Sections with students: {$totalWithStudents}\n";
echo "Total sections: " . count($counts) . "\n";

echo "\n=== Done ===\n";
