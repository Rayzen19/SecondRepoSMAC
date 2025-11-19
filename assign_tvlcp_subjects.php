<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AcademicYearStrandSection;
use App\Models\Subject;
use App\Models\StrandSubject;
use App\Models\AcademicYearStrandSubject;
use App\Models\Strand;

echo "=== Assigning TVL-CP Subjects to Section A - TITUS ===\n\n";

// Get the section
$ayss = AcademicYearStrandSection::with(['section', 'strand'])->find(22);

if (!$ayss) {
    echo "❌ Section not found!\n";
    exit;
}

echo "Section Details:\n";
echo "   ID: {$ayss->id}\n";
echo "   Section: {$ayss->section->name}\n";
echo "   Strand: {$ayss->strand->code}\n";
echo "   Academic Year ID: {$ayss->academic_year_id}\n\n";

// Get all subjects for TVL-CP strand for 1st semester
$strand = $ayss->strand;
$strandSubjects = StrandSubject::where('strand_id', $strand->id)
    ->where('semestral_period', '1st')
    ->with('subject')
    ->get();

echo "Found {$strandSubjects->count()} TVL-CP subjects for 1st semester\n\n";

$created = 0;
$skipped = 0;

foreach ($strandSubjects as $ss) {
    $subject = $ss->subject;
    
    // Check if already exists
    $exists = AcademicYearStrandSubject::where('academic_year_id', $ayss->academic_year_id)
        ->where('academic_year_strand_section_id', $ayss->id)
        ->where('subject_id', $subject->id)
        ->exists();
    
    if ($exists) {
        echo "⏭️  {$subject->code} - Already assigned\n";
        $skipped++;
        continue;
    }
    
    // Create the assignment (using default teacher ID 2 for now - can be reassigned later)
    AcademicYearStrandSubject::create([
        'academic_year_id' => $ayss->academic_year_id,
        'strand_id' => $strand->id,
        'subject_id' => $subject->id,
        'academic_year_strand_section_id' => $ayss->id,
        'teacher_id' => 2, // Default teacher - can be reassigned in admin panel
    ]);
    
    echo "✅ {$subject->code} - {$subject->name} ({$subject->type})\n";
    $created++;
}

echo "\n=== Summary ===\n";
echo "   Created: {$created}\n";
echo "   Skipped: {$skipped}\n";
echo "   Total: " . ($created + $skipped) . "\n\n";

echo "Now syncing subject enrollments for all students in this section...\n";

$enrollments = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
    ->where('academic_year_id', $ayss->academic_year_id)
    ->get();

$totalSynced = 0;
foreach ($enrollments as $enrollment) {
    $synced = $enrollment->syncSubjectEnrollments();
    $totalSynced += $synced;
}

echo "✅ Synced {$totalSynced} subject enrollments for {$enrollments->count()} students\n";
