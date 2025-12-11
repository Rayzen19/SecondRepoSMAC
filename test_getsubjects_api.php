<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Strand;
use App\Models\Subject;
use App\Models\StrandSubject;
use App\Models\AcademicYear;
use App\Models\Section;
use Illuminate\Support\Facades\Schema;

echo "=== Testing getSubjects API Logic ===\n\n";

$strandCode = 'ABM';
$gradeLevel = '12';
$sectionId = 24; // JOB section

echo "Parameters:\n";
echo "  Strand: $strandCode\n";
echo "  Grade Level: $gradeLevel\n";
echo "  Section ID: $sectionId\n\n";

$strand = Strand::where('code', $strandCode)->first();
if (!$strand) {
    echo "Strand not found!\n";
    exit;
}
echo "Strand Found: {$strand->name} (ID: {$strand->id})\n\n";

$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "No active academic year!\n";
    exit;
}
echo "Active Academic Year: {$activeYear->year_start}-{$activeYear->year_end} (ID: {$activeYear->id})\n\n";

// Resolve AYS section
$aysSectionId = null;
if (!empty($sectionId) && $activeYear) {
    $aysSection = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $strand->id)
        ->where('section_id', $sectionId)
        ->first();
    $aysSectionId = $aysSection?->id;
    echo "AcademicYearStrandSection ID: " . ($aysSectionId ?? 'NULL') . "\n\n";
}

// Get strand subjects
$strandSubjects = StrandSubject::with('subject', 'strand')
    ->where('strand_id', $strand->id)
    ->when(Schema::hasColumn('strand_subjects', 'grade_level'), function ($q) use ($gradeLevel) {
        $q->where('grade_level', $gradeLevel);
    })
    ->orderBy('id', 'asc')
    ->get();

echo "Found " . $strandSubjects->count() . " subjects\n\n";

// Map subjects with assignments
$subjects = $strandSubjects->map(function ($ss) use ($activeYear, $strand, $aysSectionId) {
    $assigned = null;
    if ($activeYear) {
        $query = \App\Models\AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $strand->id)
            ->where('subject_id', $ss->subject->id)
            ->with('teacher');
        
        if (!is_null($aysSectionId)) {
            $query->where('academic_year_strand_section_id', $aysSectionId);
        } else {
            $query->whereNull('academic_year_strand_section_id');
        }
        
        $assignment = $query->first();
        
        if ($assignment && $assignment->teacher) {
            $assigned = [
                'id' => $assignment->teacher->id,
                'name' => $assignment->teacher->first_name . ' ' . $assignment->teacher->last_name
            ];
            
            echo "✓ Subject: {$ss->subject->name} (ID: {$ss->subject->id})\n";
            echo "  Assignment ID: {$assignment->id}\n";
            echo "  Teacher ID: {$assignment->teacher->id}\n";
            echo "  Teacher First Name: {$assignment->teacher->first_name}\n";
            echo "  Teacher Last Name: {$assignment->teacher->last_name}\n";
            echo "  Formatted Name: {$assigned['name']}\n";
            echo "  academic_year_strand_section_id: " . ($assignment->academic_year_strand_section_id ?? 'NULL') . "\n\n";
        } else {
            echo "✗ Subject: {$ss->subject->name} (ID: {$ss->subject->id}) - No assignment\n";
            if ($assignment) {
                echo "  Assignment exists but no teacher: Assignment ID {$assignment->id}\n";
            }
            echo "\n";
        }
    }
    
    return [
        'id' => $ss->subject->id ?? null,
        'code' => $ss->subject->code ?? '',
        'name' => $ss->subject->name ?? '',
        'type' => $ss->subject->type ?? null,
        'semester' => $ss->subject->semester ?? null,
        'grade_level' => $ss->grade_level ?? null,
        'assigned_teacher' => $assigned,
    ];
})->filter(fn($s) => $s['id'] !== null)->values();

echo "\n=== Final API Response (JSON) ===\n";
echo json_encode([
    'success' => true,
    'strand' => $strand->code,
    'grade_level' => $gradeLevel,
    'count' => $subjects->count(),
    'subjects' => $subjects
], JSON_PRETTY_PRINT);

echo "\n\n=== Done ===\n";
