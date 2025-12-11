<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Section;
use App\Models\Subject;
use App\Models\Strand;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSubject;
use App\Models\StrandSubject;
use App\Models\AcademicYearStrandSection;

echo "=== Debugging JOB Section - Practical Research 1 Assignment Issue ===\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
echo "Active Academic Year: {$activeYear->year_start}-{$activeYear->year_end} (ID: {$activeYear->id})\n\n";

// Find the JOB section
$jobSection = Section::where('name', 'like', '%JOB%')
    ->where('grade', 'G-12')
    ->first();
echo "Section: {$jobSection->name} (ID: {$jobSection->id})\n";

// Find the strand
$strand = $jobSection->strand;
echo "Strand: {$strand->name} ({$strand->code}, ID: {$strand->id})\n\n";

// Find AcademicYearStrandSection
$aysSection = AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('section_id', $jobSection->id)
    ->first();

if ($aysSection) {
    echo "AcademicYearStrandSection found: ID {$aysSection->id}\n\n";
} else {
    echo "⚠️ NO AcademicYearStrandSection found!\n\n";
}

// Find Practical Research 1 subject
$practicalResearch1 = Subject::where('name', 'like', '%Practical Research 1%')->first();
echo "Subject: {$practicalResearch1->name} (ID: {$practicalResearch1->id}, Code: {$practicalResearch1->code})\n\n";

// Check StrandSubject
echo "=== Checking StrandSubject ===\n";
$strandSubject = StrandSubject::where('strand_id', $strand->id)
    ->where('subject_id', $practicalResearch1->id)
    ->first();

if ($strandSubject) {
    echo "✓ StrandSubject exists: ID {$strandSubject->id}\n";
    echo "  Grade Level: " . ($strandSubject->grade_level ?? 'NULL') . "\n\n";
} else {
    echo "✗ NO StrandSubject found for this combination!\n\n";
}

// Check ALL AcademicYearStrandSubject assignments for this subject
echo "=== All AcademicYearStrandSubject assignments for Practical Research 1 ===\n";
$allAssignments = AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('subject_id', $practicalResearch1->id)
    ->with(['teacher', 'academicYearStrandSection'])
    ->get();

if ($allAssignments->isEmpty()) {
    echo "No assignments found!\n";
} else {
    echo "Found " . $allAssignments->count() . " assignment(s):\n";
    foreach ($allAssignments as $assignment) {
        $teacherName = $assignment->teacher ? 
            "{$assignment->teacher->last_name}, {$assignment->teacher->first_name}" : 
            "NULL";
        $sectionId = $assignment->academic_year_strand_section_id ?? 'NULL';
        
        echo "\n  Assignment ID: {$assignment->id}\n";
        echo "  Teacher: {$teacherName} (ID: " . ($assignment->teacher_id ?? 'NULL') . ")\n";
        echo "  academic_year_strand_section_id: {$sectionId}\n";
        
        if ($assignment->academicYearStrandSection) {
            echo "  Linked Section: {$assignment->academicYearStrandSection->section->name}\n";
        } else {
            echo "  Linked Section: NULL (applies to all sections)\n";
        }
    }
}
echo "\n";

// Simulate what the getSubjects controller method would return
echo "=== Simulating getSubjects API Response for JOB Section ===\n";
$aysSectionId = $aysSection?->id;
echo "Using aysSectionId: " . ($aysSectionId ?? 'NULL') . "\n\n";

$query = AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('subject_id', $practicalResearch1->id)
    ->with('teacher');

if (!is_null($aysSectionId)) {
    echo "Filtering WHERE academic_year_strand_section_id = {$aysSectionId}\n";
    $query->where('academic_year_strand_section_id', $aysSectionId);
} else {
    echo "Filtering WHERE academic_year_strand_section_id IS NULL\n";
    $query->whereNull('academic_year_strand_section_id');
}

$assignment = $query->first();

if ($assignment && $assignment->teacher) {
    echo "✓ Assignment found!\n";
    echo "  Teacher: {$assignment->teacher->last_name}, {$assignment->teacher->first_name} (ID: {$assignment->teacher->id})\n";
} else {
    if ($assignment) {
        echo "✗ Assignment found but NO TEACHER assigned!\n";
        echo "  Assignment ID: {$assignment->id}\n";
        echo "  teacher_id: " . ($assignment->teacher_id ?? 'NULL') . "\n";
    } else {
        echo "✗ NO assignment found for this query!\n";
    }
}

echo "\n=== Done ===\n";
