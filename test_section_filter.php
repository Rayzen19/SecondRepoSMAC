<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Section Filter (ABM + Grade 11)\n";
echo "========================================\n\n";

// Simulate the controller query
$strandCode = 'ABM';
$gradeLevel = '11';

$sectionsQuery = App\Models\Section::with('strand')
    ->orderBy('grade')
    ->orderBy('name');

// Filter by strand
$sectionsQuery->whereHas('strand', function($q) use ($strandCode) {
    $q->where('code', $strandCode);
});

// Filter by grade level (with the fix)
$sectionsQuery->where(function($q) use ($gradeLevel) {
    $q->where('grade', 'G-' . $gradeLevel)
      ->orWhere('grade', 'Grade ' . $gradeLevel)
      ->orWhere('grade', $gradeLevel);
});

$sections = $sectionsQuery->get();

echo "Found {$sections->count()} sections for ABM Grade 11:\n\n";

foreach ($sections as $section) {
    echo "✅ Section: {$section->name}\n";
    echo "   - ID: {$section->id}\n";
    echo "   - Grade: {$section->grade}\n";
    echo "   - Strand: {$section->strand->code} ({$section->strand->name})\n";
    echo "\n";
}

if ($sections->isEmpty()) {
    echo "❌ NO SECTIONS FOUND! This is the problem.\n";
} else {
    echo "🎉 SUCCESS! The filter is now working correctly!\n";
}
