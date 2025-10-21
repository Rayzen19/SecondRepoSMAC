<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicYearStrandSection;
use App\Models\Section;
use App\Models\Strand;
use App\Models\AcademicYear;

echo "=== Linking Jupiter Section to ABM ===\n\n";

$activeYear = AcademicYear::where('is_active', true)->first();
$abmStrand = Strand::where('code', 'ABM')->first();
$jupiterSection = Section::where('name', 'Jupiter')->where('grade', 'G-11')->first();

if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit(1);
}

if (!$abmStrand) {
    echo "❌ ABM strand not found!\n";
    exit(1);
}

if (!$jupiterSection) {
    echo "❌ Jupiter section not found!\n";
    exit(1);
}

echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n";
echo "Strand: {$abmStrand->name} (ID: {$abmStrand->id})\n";
echo "Section: {$jupiterSection->name} (ID: {$jupiterSection->id})\n\n";

// Check if already exists
$existing = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $abmStrand->id)
    ->where('section_id', $jupiterSection->id)
    ->first();

if ($existing) {
    echo "✅ Link already exists! (ID: {$existing->id})\n";
    echo "   Adviser: " . ($existing->adviserTeacher ? $existing->adviserTeacher->first_name . ' ' . $existing->adviserTeacher->last_name : 'Not assigned') . "\n";
} else {
    echo "Creating new link...\n";
    
    $link = AcademicYearStrandSection::create([
        'academic_year_id' => $activeYear->id,
        'strand_id' => $abmStrand->id,
        'section_id' => $jupiterSection->id,
        'is_active' => true,
    ]);
    
    echo "✅ Successfully created link! (ID: {$link->id})\n";
    echo "\nNext steps:\n";
    echo "1. Go to Admin → Section & Advisers\n";
    echo "2. Assign an adviser to ABM - Jupiter\n";
    echo "3. Then assign John Raymond Barrogo to subjects for that section\n";
}
