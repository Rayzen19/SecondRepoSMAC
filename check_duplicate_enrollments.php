<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking for Duplicate Student Enrollments ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "No active academic year found!\n";
    exit;
}

echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Find students with multiple enrollments in the same academic year
$duplicates = \Illuminate\Support\Facades\DB::select("
    SELECT student_id, academic_year_id, academic_year_strand_section_id, COUNT(*) as count
    FROM student_enrollments
    WHERE academic_year_id = ?
    AND deleted_at IS NULL
    GROUP BY student_id, academic_year_id, academic_year_strand_section_id
    HAVING COUNT(*) > 1
", [$activeYear->id]);

if (empty($duplicates)) {
    echo "✓ No duplicate enrollments found with same section!\n\n";
} else {
    echo "⚠ Found duplicate enrollments:\n";
    foreach ($duplicates as $dup) {
        echo "  Student ID: {$dup->student_id}, Section ID: {$dup->academic_year_strand_section_id}, Count: {$dup->count}\n";
    }
    echo "\n";
}

// Check for students enrolled in multiple sections
$multiSection = \Illuminate\Support\Facades\DB::select("
    SELECT student_id, COUNT(DISTINCT academic_year_strand_section_id) as section_count, 
           GROUP_CONCAT(academic_year_strand_section_id) as section_ids
    FROM student_enrollments
    WHERE academic_year_id = ?
    AND deleted_at IS NULL
    GROUP BY student_id
    HAVING COUNT(DISTINCT academic_year_strand_section_id) > 1
", [$activeYear->id]);

if (empty($multiSection)) {
    echo "✓ No students enrolled in multiple sections!\n\n";
} else {
    echo "⚠ Students enrolled in multiple sections:\n";
    foreach ($multiSection as $ms) {
        $student = \App\Models\Student::find($ms->student_id);
        $studentName = $student ? "{$student->first_name} {$student->last_name}" : "Unknown";
        echo "  Student: {$studentName} (ID: {$ms->student_id})\n";
        echo "    Enrolled in {$ms->section_count} sections: {$ms->section_ids}\n";
    }
    echo "\n";
}

// Get section counts
echo "=== Section Counts ===\n";
$sections = \App\Models\AcademicYearStrandSection::with(['section', 'strand'])
    ->where('academic_year_id', $activeYear->id)
    ->get();

foreach ($sections as $ayss) {
    $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
        ->where('academic_year_id', $activeYear->id)
        ->count();
    
    $distinctCount = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
        ->where('academic_year_id', $activeYear->id)
        ->distinct('student_id')
        ->count('student_id');
    
    $sectionName = $ayss->section ? "{$ayss->section->grade} {$ayss->section->name}" : "Unknown";
    $strandCode = $ayss->strand ? $ayss->strand->code : "N/A";
    
    if ($count !== $distinctCount) {
        echo "⚠ {$sectionName} ({$strandCode}): Total={$count}, Distinct Students={$distinctCount} [DUPLICATES!]\n";
    } else {
        echo "✓ {$sectionName} ({$strandCode}): {$count} students\n";
    }
}

echo "\nDone!\n";
