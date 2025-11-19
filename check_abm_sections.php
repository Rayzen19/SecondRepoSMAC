<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking ABM Grade 11 Sections ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Year: {$activeYear->name}\n\n";

// Get ABM strand
$strand = \App\Models\Strand::where('code', 'ABM')->first();
if (!$strand) {
    die("❌ ABM strand not found!\n");
}
echo "Strand: {$strand->code} - {$strand->name}\n\n";

// Get all Grade 11 sections for ABM
$sections = \App\Models\Section::where('strand_id', $strand->id)
    ->where('grade', 'G-11')
    ->get();

echo "Found " . $sections->count() . " Grade 11 ABM sections:\n\n";

foreach ($sections as $section) {
    echo "Section ID: {$section->id}\n";
    echo "Name: {$section->name}\n";
    echo "Grade Level: {$section->grade}\n";
    echo "Max Students: " . ($section->max_students ?? 'Not set') . "\n";
    
    // Check AYSS
    $ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $strand->id)
        ->where('section_id', $section->id)
        ->first();
    
    if ($ayss) {
        echo "AYSS ID: {$ayss->id}\n";
        
        // Count current enrollments
        $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
            ->where('academic_year_id', $activeYear->id)
            ->count();
        echo "Current Enrollments: {$count}\n";
    } else {
        echo "⚠️ No AYSS record found for this section!\n";
    }
    
    echo str_repeat("-", 50) . "\n\n";
}

// Check if there's a section named ABM-A or similar
echo "\nLooking for ABM-A section specifically...\n";
$abmASection = \App\Models\Section::where('strand_id', $strand->id)
    ->where('grade', 'G-11')
    ->where('name', 'LIKE', '%ABM-A%')
    ->orWhere(function($q) use ($strand) {
        $q->where('name', 'LIKE', '%A%')
          ->where('strand_id', $strand->id)
          ->where('grade', 'G-11');
    })
    ->first();

if ($abmASection) {
    echo "✅ Found section: {$abmASection->name}\n";
} else {
    echo "❌ No ABM-A section found. Available sections need to be created.\n";
}
