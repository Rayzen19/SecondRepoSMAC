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

echo "=== Checking JOB Section - Practical Research 1 Assignment ===\n\n";

// Get active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "No active academic year found!\n";
    exit;
}
echo "Active Academic Year: {$activeYear->year_start}-{$activeYear->year_end}\n\n";

// Find the JOB section
$jobSection = Section::where('name', 'like', '%JOB%')
    ->where('grade', 'G-12')
    ->first();

if (!$jobSection) {
    echo "JOB section not found!\n";
    exit;
}
echo "Section Found: {$jobSection->name} (ID: {$jobSection->id})\n";
echo "Grade: {$jobSection->grade}\n";

// Find the strand for JOB (should be ABM)
$strand = $jobSection->strand;
if ($strand) {
    echo "Strand: {$strand->name} ({$strand->code})\n\n";
} else {
    echo "No strand assigned to this section!\n\n";
}

// Find Practical Research 1 subject
echo "=== Searching for Practical Research 1 Subject ===\n";
$practicalResearch1 = Subject::where('name', 'like', '%Practical Research 1%')->get();

if ($practicalResearch1->isEmpty()) {
    echo "No Practical Research 1 subject found!\n\n";
} else {
    echo "Found " . $practicalResearch1->count() . " subject(s):\n";
    foreach ($practicalResearch1 as $subject) {
        echo "  - ID: {$subject->id}, Code: {$subject->code}, Name: {$subject->name}\n";
    }
    echo "\n";
}

// Check teacher Pam Toledo Murillo
echo "=== Checking Teacher: Pam Toledo Murillo ===\n";
$teacher = Teacher::where('last_name', 'like', '%Murillo%')
    ->orWhere('last_name', 'like', '%Toledo%')
    ->get();

if ($teacher->isEmpty()) {
    echo "Teacher not found with Murillo or Toledo in last name!\n";
    // Try searching in first name
    $teacher = Teacher::where('first_name', 'like', '%Pam%')->get();
    if (!$teacher->isEmpty()) {
        echo "Found teacher(s) with Pam in first name:\n";
        foreach ($teacher as $t) {
            echo "  - ID: {$t->id}, Name: {$t->last_name}, {$t->first_name}\n";
        }
    }
} else {
    echo "Found " . $teacher->count() . " teacher(s):\n";
    foreach ($teacher as $t) {
        echo "  - ID: {$t->id}, Name: {$t->last_name}, {$t->first_name} {$t->middle_name}\n";
    }
}
echo "\n";

// If we have the strand and subject, check the assignments
if ($strand && !$practicalResearch1->isEmpty()) {
    echo "=== Checking Subject Teacher Assignments ===\n";
    
    foreach ($practicalResearch1 as $subject) {
        echo "\nFor Subject: {$subject->name} (ID: {$subject->id})\n";
        
        // Check assignments for this subject in the strand
        $assignments = AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $strand->id)
            ->where('subject_id', $subject->id)
            ->with(['teacher', 'academicYearStrandSection.section'])
            ->get();
        
        if ($assignments->isEmpty()) {
            echo "  ❌ No assignments found for this subject in {$strand->code} strand\n";
        } else {
            echo "  Found " . $assignments->count() . " assignment(s):\n";
            foreach ($assignments as $assignment) {
                $teacherName = $assignment->teacher ? 
                    "{$assignment->teacher->last_name}, {$assignment->teacher->first_name}" : 
                    "Not assigned";
                $sectionName = $assignment->academicYearStrandSection && $assignment->academicYearStrandSection->section ? 
                    $assignment->academicYearStrandSection->section->name : 
                    "All sections";
                
                echo "    - Teacher: {$teacherName}\n";
                echo "      Section: {$sectionName}\n";
                echo "      Assignment ID: {$assignment->id}\n";
            }
        }
    }
}

echo "\n=== Checking All Grade 12 ABM Subject Assignments ===\n";
if ($strand) {
    $allAssignments = AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $strand->id)
        ->with(['teacher', 'subject', 'academicYearStrandSection.section'])
        ->get();
    
    if ($allAssignments->isEmpty()) {
        echo "No subject assignments found for {$strand->code}\n";
    } else {
        echo "Found " . $allAssignments->count() . " total assignments:\n";
        foreach ($allAssignments as $assignment) {
            $teacherName = $assignment->teacher ? 
                "{$assignment->teacher->last_name}, {$assignment->teacher->first_name}" : 
                "❌ Not assigned";
            $subjectName = $assignment->subject ? $assignment->subject->name : "Unknown";
            $sectionName = $assignment->academicYearStrandSection && $assignment->academicYearStrandSection->section ? 
                $assignment->academicYearStrandSection->section->name : 
                "All sections";
            
            echo "\n  Subject: {$subjectName}\n";
            echo "  Teacher: {$teacherName}\n";
            echo "  Section: {$sectionName}\n";
        }
    }
}

echo "\n=== Done ===\n";
