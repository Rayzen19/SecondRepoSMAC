<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing getSectionCounts Logic ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

$sections = \App\Models\Section::with('strand')->get();
echo "Total sections: " . $sections->count() . "\n\n";

$counts = [];

foreach ($sections as $section) {
    if (!$section->strand) continue;

    $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $section->strand->id)
        ->where('section_id', $section->id)
        ->first();

    if (!$academicYearStrandSection) {
        $academicYearStrandSection = \App\Models\AcademicYearStrandSection::where('strand_id', $section->strand->id)
            ->where('section_id', $section->id)
            ->orderByDesc('id')
            ->first();
    }

    $count = 0;
    if ($academicYearStrandSection) {
        // Count using the model (respects soft deletes)
        $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $academicYearStrandSection->id)
            ->count();
        
        echo "Section: {$section->grade} {$section->name} (Strand: {$section->strand->code}, AYSS ID: {$academicYearStrandSection->id})\n";
        echo "  Count: {$count}\n";
    }

    $key = $section->strand->code . '-' . $section->id;
    $counts[$key] = $count;
}

echo "\n=== Final Counts Array ===\n";
print_r($counts);

echo "\n=== Specifically checking ABM-1 (JUDE) ===\n";
$judeCount = $counts['ABM-1'] ?? 'NOT FOUND';
echo "ABM-1 count: {$judeCount}\n";

echo "\n✅ Test complete!\n";
