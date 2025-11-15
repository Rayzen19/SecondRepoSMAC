<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Section;
use App\Models\Strand;

echo "=== Inserting Grade 12 Sections ===\n\n";

// Define all Grade 12 sections from the image
$sectionsData = [
    // ABM Grade 12 Sections
    ['strand_code' => 'ABM', 'grade' => 'G-12', 'name' => 'A - JOB'],
    ['strand_code' => 'ABM', 'grade' => 'G-12', 'name' => 'B - JOEL'],
    ['strand_code' => 'ABM', 'grade' => 'G-12', 'name' => 'C - JONAS'],
    ['strand_code' => 'ABM', 'grade' => 'G-12', 'name' => 'D - EZEKIEL'],
    
    // HUMSS Grade 12 Sections
    ['strand_code' => 'HUMSS', 'grade' => 'G-12', 'name' => 'A - HOSEA'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-12', 'name' => 'B - GABRIEL'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-12', 'name' => 'C - AMOS'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-12', 'name' => 'D - CALEB'],
    
    // STEM Grade 12 Sections
    ['strand_code' => 'STEM', 'grade' => 'G-12', 'name' => 'A - DANIEL'],
    ['strand_code' => 'STEM', 'grade' => 'G-12', 'name' => 'B - PAUL'],
    ['strand_code' => 'STEM', 'grade' => 'G-12', 'name' => 'C - SAMUEL'],
    
    // TVL HE Grade 12 Sections
    ['strand_code' => 'TVL-HE', 'grade' => 'G-12', 'name' => 'A - PSALM'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-12', 'name' => 'B - PROVERBS'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-12', 'name' => 'C - ECCLESIASTES'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-12', 'name' => 'D - KINGS'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-12', 'name' => 'E - JUDGES'],
    
    // TVL ICT Grade 12 Sections
    ['strand_code' => 'TVL-ICT', 'grade' => 'G-12', 'name' => 'A - EZRA'],
    ['strand_code' => 'TVL-ICT', 'grade' => 'G-12', 'name' => 'B - GALATIANS'],
];

echo "Processing " . count($sectionsData) . " Grade 12 sections...\n\n";

$created = 0;
$updated = 0;
$errors = 0;
$strandErrors = [];

foreach ($sectionsData as $sectionData) {
    try {
        // Find the strand
        $strand = Strand::where('code', $sectionData['strand_code'])->first();
        
        if (!$strand) {
            if (!in_array($sectionData['strand_code'], $strandErrors)) {
                echo "⚠️  Strand '{$sectionData['strand_code']}' not found. Skipping sections for this strand.\n";
                $strandErrors[] = $sectionData['strand_code'];
            }
            $errors++;
            continue;
        }
        
        // Create or update section
        $section = Section::updateOrCreate(
            [
                'strand_id' => $strand->id,
                'grade' => $sectionData['grade'],
                'name' => $sectionData['name']
            ],
            [
                'is_active' => true
            ]
        );
        
        if ($section->wasRecentlyCreated) {
            $created++;
            echo "✓ Created: {$sectionData['strand_code']} {$sectionData['grade']} {$sectionData['name']}\n";
        } else {
            $updated++;
            echo "✓ Updated: {$sectionData['strand_code']} {$sectionData['grade']} {$sectionData['name']}\n";
        }
        
    } catch (\Exception $e) {
        $errors++;
        echo "❌ Error with {$sectionData['strand_code']} {$sectionData['grade']} {$sectionData['name']}: {$e->getMessage()}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Sections created: {$created}\n";
echo "Sections updated: {$updated}\n";
echo "Errors: {$errors}\n";

if (!empty($strandErrors)) {
    echo "\n⚠️  Missing Strands:\n";
    foreach ($strandErrors as $strandCode) {
        echo "   - {$strandCode}\n";
    }
    echo "\nYou may need to create these strands first.\n";
}

echo "\n✅ Grade 12 section insertion completed!\n";
