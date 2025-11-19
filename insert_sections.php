<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Section;
use App\Models\Strand;

echo "=== Inserting Sections ===\n\n";

// Define all sections from the image
$sectionsData = [
    // ABM Sections
    ['strand_code' => 'ABM', 'grade' => 'G-11', 'name' => 'A - JUDE'],
    ['strand_code' => 'ABM', 'grade' => 'G-11', 'name' => 'B - PETER'],
    ['strand_code' => 'ABM', 'grade' => 'G-11', 'name' => 'C - JAMES'],
    
    // HUMSS Sections
    ['strand_code' => 'HUMSS', 'grade' => 'G-11', 'name' => 'A - ROMANS'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-11', 'name' => 'B - HEBREWS'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-11', 'name' => 'C - LEVITICUS'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-11', 'name' => 'D - HAGGAI'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-11', 'name' => 'E - HABAKKUK'],
    ['strand_code' => 'HUMSS', 'grade' => 'G-11', 'name' => 'F - MALACHI'],
    
    // STEM Sections
    ['strand_code' => 'STEM', 'grade' => 'G-11', 'name' => 'A - LUKE'],
    ['strand_code' => 'STEM', 'grade' => 'G-11', 'name' => 'B - WISDOM'],
    ['strand_code' => 'STEM', 'grade' => 'G-11', 'name' => 'C - NOAH'],
    ['strand_code' => 'STEM', 'grade' => 'G-11', 'name' => 'D - ELIJAH'],
    
    // TVL HE Sections
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'A - CHRONICLES'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'B - PHILEMON'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'C - DEUTERONOMY'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'D - EXODUS'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'E - GENESIS'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'F - ISRAEL'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'G - ISAIAH'],
    ['strand_code' => 'TVL-HE', 'grade' => 'G-11', 'name' => 'H - AARON'],
    
    // TVL ICT Sections
    ['strand_code' => 'TVL-ICT', 'grade' => 'G-11', 'name' => 'A - TITUS'],
    ['strand_code' => 'TVL-ICT', 'grade' => 'G-11', 'name' => 'B - COLOSSIANS'],
];

echo "Processing " . count($sectionsData) . " sections...\n\n";

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

echo "\n✅ Section insertion completed!\n";
