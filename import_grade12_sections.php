<?php

/**
 * Import all Grade 12 sections for each strand
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Section;
use App\Models\Strand;

// Get strand IDs
$strands = Strand::all()->keyBy('code');

if ($strands->isEmpty()) {
    echo "Error: No strands found in database. Please run import_5_strands.php first.\n";
    exit(1);
}

echo "Found strands:\n";
foreach ($strands as $code => $strand) {
    echo "  {$code} (ID: {$strand->id})\n";
}
echo "\n";

$sections = [
    // ABM Sections (Grade 12)
    ['name' => 'A - JOB', 'grade' => 'G-12', 'strand_code' => 'ABM'],
    ['name' => 'B - JOEL', 'grade' => 'G-12', 'strand_code' => 'ABM'],
    ['name' => 'C - JONAS', 'grade' => 'G-12', 'strand_code' => 'ABM'],
    ['name' => 'D - EZEKIEL', 'grade' => 'G-12', 'strand_code' => 'ABM'],
    
    // HUMSS Sections (Grade 12)
    ['name' => 'A - HOSEA', 'grade' => 'G-12', 'strand_code' => 'HUMSS'],
    ['name' => 'B - GABRIEL', 'grade' => 'G-12', 'strand_code' => 'HUMSS'],
    ['name' => 'C - AMOS', 'grade' => 'G-12', 'strand_code' => 'HUMSS'],
    ['name' => 'D - CALEB', 'grade' => 'G-12', 'strand_code' => 'HUMSS'],
    
    // STEM Sections (Grade 12)
    ['name' => 'A - DANIEL', 'grade' => 'G-12', 'strand_code' => 'STEM'],
    ['name' => 'B - PAUL', 'grade' => 'G-12', 'strand_code' => 'STEM'],
    ['name' => 'C - SAMUEL', 'grade' => 'G-12', 'strand_code' => 'STEM'],
    
    // TVL HE (Bread and Pastry) Sections (Grade 12)
    ['name' => 'A - PSALM', 'grade' => 'G-12', 'strand_code' => 'TVL-BP'],
    ['name' => 'B - PROVERBS', 'grade' => 'G-12', 'strand_code' => 'TVL-BP'],
    ['name' => 'C - ECCLESIASTES', 'grade' => 'G-12', 'strand_code' => 'TVL-BP'],
    ['name' => 'D - KINGS', 'grade' => 'G-12', 'strand_code' => 'TVL-BP'],
    ['name' => 'E - JUDGES', 'grade' => 'G-12', 'strand_code' => 'TVL-BP'],
    
    // TVL ICT (Computer Programming) Sections (Grade 12)
    ['name' => 'A - EZRA', 'grade' => 'G-12', 'strand_code' => 'TVL-CP'],
    ['name' => 'B - GALATIANS', 'grade' => 'G-12', 'strand_code' => 'TVL-CP'],
];

echo "Starting Grade 12 section import...\n\n";

$created = 0;
$existing = 0;
$errors = 0;

foreach ($sections as $sectionData) {
    try {
        $strandCode = $sectionData['strand_code'];
        
        if (!isset($strands[$strandCode])) {
            echo "✗ Error: Strand '{$strandCode}' not found for section '{$sectionData['name']}'\n";
            $errors++;
            continue;
        }
        
        $strandId = $strands[$strandCode]->id;
        $strandName = $strands[$strandCode]->name;
        
        // Check if section already exists
        $existingSection = Section::where('name', $sectionData['name'])
            ->where('grade', $sectionData['grade'])
            ->where('strand_id', $strandId)
            ->first();
        
        if ($existingSection) {
            echo "✓ Section '{$strandCode} {$sectionData['grade']} {$sectionData['name']}' already exists (ID: {$existingSection->id})\n";
            $existing++;
        } else {
            // Create new section
            $section = Section::create([
                'name' => $sectionData['name'],
                'grade' => $sectionData['grade'],
                'strand_id' => $strandId,
            ]);
            echo "✓ Created section '{$strandCode} {$sectionData['grade']} {$sectionData['name']}' (ID: {$section->id})\n";
            $created++;
        }
    } catch (Exception $e) {
        echo "✗ Error creating section '{$sectionData['name']}': " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=================================\n";
echo "Import Summary\n";
echo "=================================\n";
echo "Created: {$created}\n";
echo "Already existing: {$existing}\n";
echo "Errors: {$errors}\n";
echo "Total processed: " . ($created + $existing + $errors) . "\n\n";

// Display sections by strand
echo "Grade 12 Sections by Strand:\n";
echo "=================================\n";
foreach ($strands as $code => $strand) {
    $strandSections = Section::where('strand_id', $strand->id)
        ->where('grade', 'G-12')
        ->orderBy('name')
        ->get();
    
    if ($strandSections->count() > 0) {
        echo "\n{$code} - Grade 12 ({$strandSections->count()} sections):\n";
        foreach ($strandSections as $section) {
            echo "  • {$section->name}\n";
        }
    }
}

// Display total count
$totalG11 = Section::where('grade', 'G-11')->count();
$totalG12 = Section::where('grade', 'G-12')->count();
$totalAll = Section::count();

echo "\n=================================\n";
echo "Overall Statistics\n";
echo "=================================\n";
echo "Total Grade 11 sections: {$totalG11}\n";
echo "Total Grade 12 sections: {$totalG12}\n";
echo "Total all sections: {$totalAll}\n";

echo "\n✓ Import process completed!\n";
echo "You can now view all sections at: http://127.0.0.1:8000/admin/sections\n";
