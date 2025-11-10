<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating ABM Grade 11 sections...\n\n";

$abmStrand = App\Models\Strand::where('code', 'ABM')->first();

if (!$abmStrand) {
    echo "❌ ABM Strand not found!\n";
    exit(1);
}

$sectionsToCreate = [
    ['name' => 'Venus', 'grade' => 'G-11'],
    ['name' => 'Mars', 'grade' => 'G-11'],
    ['name' => 'Saturn', 'grade' => 'G-12'],
    ['name' => 'Neptune', 'grade' => 'G-12'],
];

foreach ($sectionsToCreate as $sectionData) {
    $exists = App\Models\Section::where('name', $sectionData['name'])
        ->where('strand_id', $abmStrand->id)
        ->where('grade', $sectionData['grade'])
        ->exists();
    
    if (!$exists) {
        App\Models\Section::create([
            'name' => $sectionData['name'],
            'grade' => $sectionData['grade'],
            'strand_id' => $abmStrand->id,
        ]);
        echo "✅ Created: {$sectionData['name']} ({$sectionData['grade']})\n";
    } else {
        echo "ℹ️  Already exists: {$sectionData['name']} ({$sectionData['grade']})\n";
    }
}

echo "\n📊 Current ABM sections:\n";
echo "Grade 11:\n";
$g11 = App\Models\Section::where('strand_id', $abmStrand->id)
    ->whereIn('grade', ['G-11', 'Grade 11', '11'])
    ->get();
foreach ($g11 as $section) {
    echo "  - {$section->name} (ID: {$section->id})\n";
}

echo "\nGrade 12:\n";
$g12 = App\Models\Section::where('strand_id', $abmStrand->id)
    ->whereIn('grade', ['G-12', 'Grade 12', '12'])
    ->get();
foreach ($g12 as $section) {
    echo "  - {$section->name} (ID: {$section->id})\n";
}

echo "\n✅ Done! Now refresh your browser to see the sections.\n";
