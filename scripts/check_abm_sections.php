<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicYearStrandSection;
use App\Models\Section;
use App\Models\Strand;
use App\Models\AcademicYear;

echo "=== Checking Sections for ABM ===\n\n";

$activeYear = AcademicYear::where('is_active', true)->first();
$abmStrand = Strand::where('code', 'ABM')->first();

if (!$activeYear || !$abmStrand) {
    echo "❌ Active year or ABM strand not found!\n";
    exit(1);
}

echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n";
echo "Strand: {$abmStrand->name} (ID: {$abmStrand->id})\n\n";

// Get all sections for ABM
$sections = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $abmStrand->id)
    ->with(['section', 'adviserTeacher'])
    ->get();

echo "ABM Sections in {$activeYear->name}: " . $sections->count() . "\n\n";

foreach ($sections as $sectionAssignment) {
    $sectionName = $sectionAssignment->section->name ?? 'Unknown';
    $grade = $sectionAssignment->section->grade ?? 'N/A';
    $adviser = $sectionAssignment->adviserTeacher 
        ? ($sectionAssignment->adviserTeacher->first_name . ' ' . $sectionAssignment->adviserTeacher->last_name)
        : 'No adviser';
    
    echo sprintf(
        "Section: %-15s | Grade: %-8s | Adviser: %s | AYS Section ID: %d\n",
        $sectionName,
        $grade,
        $adviser,
        $sectionAssignment->id
    );
}

// Check for Jupiter section specifically
echo "\n=== Looking for Jupiter Section ===\n";
$jupiter = Section::where('name', 'like', '%Jupiter%')->first();
if ($jupiter) {
    echo "Found Jupiter: {$jupiter->name} (ID: {$jupiter->id}) - Grade {$jupiter->grade}\n";
    
    // Check if it's linked to ABM
    $jupiterABM = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $abmStrand->id)
        ->where('section_id', $jupiter->id)
        ->with('adviserTeacher')
        ->first();
    
    if ($jupiterABM) {
        echo "✅ Jupiter is linked to ABM (AYS Section ID: {$jupiterABM->id})\n";
        $adviser = $jupiterABM->adviserTeacher 
            ? ($jupiterABM->adviserTeacher->first_name . ' ' . $jupiterABM->adviserTeacher->last_name)
            : 'No adviser';
        echo "   Adviser: {$adviser}\n";
    } else {
        echo "❌ Jupiter is NOT linked to ABM in academic_year_strand_sections\n";
    }
} else {
    echo "❌ Jupiter section not found!\n";
}
