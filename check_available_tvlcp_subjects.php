<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Subject;
use App\Models\AcademicYearStrandSubject;

echo "=== Available TVL-CP Subjects ===\n\n";

// Get all TVL-CP subjects
$subjects = Subject::where('strand', 'TVL-CP')
    ->orWhere('strand', 'CORE')
    ->orderBy('type')
    ->orderBy('name')
    ->get();

echo "Total subjects for TVL-CP/CORE: {$subjects->count()}\n\n";

foreach ($subjects as $subject) {
    echo "[{$subject->code}] {$subject->name}\n";
    echo "  Type: {$subject->type} | Strand: {$subject->strand} | Semester: {$subject->semester} | Units: {$subject->units}\n";
    
    // Check if assigned in active year
    $assignment = AcademicYearStrandSubject::with(['teacher', 'sectionAssignment.section'])
        ->where('subject_id', $subject->id)
        ->where('academic_year_id', 5)
        ->first();
    
    if ($assignment) {
        $teacher = $assignment->teacher;
        $teacherName = $teacher ? "{$teacher->last_name}, {$teacher->first_name}" : "❌ No teacher assigned";
        $section = $assignment->sectionAssignment?->section;
        $sectionName = $section ? $section->name : "All sections";
        echo "  ✅ Assigned | Teacher: {$teacherName} | Section: {$sectionName}\n";
    } else {
        echo "  ❌ NOT assigned in current academic year\n";
    }
    
    echo "\n";
}
