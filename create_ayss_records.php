<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating Missing AYSS Records ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

if (!$activeYear) {
    echo "❌ ERROR: No active academic year found!\n";
    exit(1);
}

echo "✅ Active Academic Year: {$activeYear->name}\n\n";

// Get all sections with their strands
$sections = \App\Models\Section::with('strand')->whereNotNull('strand_id')->get();

echo "Found {$sections->count()} sections with strands\n\n";

$created = 0;
$existing = 0;

foreach ($sections as $section) {
    if (!$section->strand) {
        echo "⚠️  Skipping section {$section->name} - no strand assigned\n";
        continue;
    }
    
    // Check if AYSS record exists
    $ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $section->strand->id)
        ->where('section_id', $section->id)
        ->first();
    
    if ($ayss) {
        $existing++;
        echo "✓ {$section->grade} {$section->name} ({$section->strand->code}) - Already exists\n";
    } else {
        // Create AYSS record
        $ayss = \App\Models\AcademicYearStrandSection::create([
            'academic_year_id' => $activeYear->id,
            'strand_id' => $section->strand->id,
            'section_id' => $section->id,
            'is_active' => true,
        ]);
        $created++;
        echo "✨ Created AYSS for {$section->grade} {$section->name} ({$section->strand->code}) - ID: {$ayss->id}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Summary:\n";
echo "  Created: $created\n";
echo "  Existing: $existing\n";
echo "  Total: " . ($created + $existing) . "\n";
echo str_repeat("=", 60) . "\n\n";

if ($created > 0) {
    echo "✅ SUCCESS: Created $created AYSS records for the active academic year!\n";
    echo "You can now assign students to sections and they will be properly saved.\n";
} else {
    echo "ℹ️  All AYSS records already exist.\n";
}

echo "\nDone!\n";
