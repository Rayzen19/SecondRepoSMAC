<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicYearStrandSubject;
use App\Models\Teacher;

echo "=== Checking NULL Section Assignments ===\n\n";

// Find John Raymond Barrogo
$teacher = Teacher::where('first_name', 'John')
    ->where('last_name', 'Barrogo')
    ->orWhere('last_name', 'Raymond')
    ->first();

if (!$teacher) {
    echo "Teacher not found. Searching all John...\n";
    $teacher = Teacher::where('first_name', 'like', '%John%')->first();
}

if ($teacher) {
    echo "Found teacher: {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->id})\n\n";
    
    // Get all assignments for this teacher
    $assignments = AcademicYearStrandSubject::where('teacher_id', $teacher->id)
        ->with(['subject', 'strand', 'academicYear', 'sectionAssignment.section'])
        ->get();
    
    echo "Total assignments: " . $assignments->count() . "\n\n";
    
    foreach ($assignments as $assignment) {
        $sectionInfo = $assignment->academic_year_strand_section_id 
            ? ($assignment->sectionAssignment->section->name ?? 'Unknown Section')
            : 'NULL (All Sections)';
        
        echo sprintf(
            "Subject: %-25s | Strand: %-6s | Section: %-15s | Section ID: %s\n",
            $assignment->subject->name ?? 'N/A',
            $assignment->strand->code ?? 'N/A',
            $sectionInfo,
            $assignment->academic_year_strand_section_id ?? 'NULL'
        );
    }
    
    echo "\n--- NULL Assignments Only ---\n";
    $nullAssignments = $assignments->filter(fn($a) => is_null($a->academic_year_strand_section_id));
    echo "Count: " . $nullAssignments->count() . "\n\n";
    
    foreach ($nullAssignments as $assignment) {
        echo sprintf(
            "ID: %d | Subject: %s | Strand: %s\n",
            $assignment->id,
            $assignment->subject->name ?? 'N/A',
            $assignment->strand->code ?? 'N/A'
        );
        
        // Check if specific assignment exists
        $specificExists = AcademicYearStrandSubject::where('academic_year_id', $assignment->academic_year_id)
            ->where('strand_id', $assignment->strand_id)
            ->where('subject_id', $assignment->subject_id)
            ->where('teacher_id', $assignment->teacher_id)
            ->whereNotNull('academic_year_strand_section_id')
            ->exists();
        
        echo "  Has specific section assignment: " . ($specificExists ? 'YES (Should be deleted!)' : 'NO (Keep)') . "\n\n";
    }
} else {
    echo "❌ Teacher not found!\n";
}

echo "\n=== All NULL Section Assignments (All Teachers) ===\n";
$allNulls = AcademicYearStrandSubject::whereNull('academic_year_strand_section_id')
    ->with(['teacher', 'subject', 'strand'])
    ->get();

echo "Total: " . $allNulls->count() . "\n\n";

foreach ($allNulls as $assignment) {
    $teacherName = $assignment->teacher 
        ? ($assignment->teacher->first_name . ' ' . $assignment->teacher->last_name)
        : 'Unknown';
    
    echo sprintf(
        "ID: %-4d | Teacher: %-25s | Subject: %-25s | Strand: %s\n",
        $assignment->id,
        $teacherName,
        $assignment->subject->name ?? 'N/A',
        $assignment->strand->code ?? 'N/A'
    );
}
