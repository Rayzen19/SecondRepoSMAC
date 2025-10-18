<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\{AcademicYear, Strand, Teacher, Subject, AcademicYearStrandAdviser, AcademicYearStrandSubject};

echo "=== Testing Subject-Teacher Assignment ===\n\n";

// Check active year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year!\n";
    exit(1);
}
echo "✓ Active Year: {$activeYear->year} (ID: {$activeYear->id})\n\n";

// Check strands
$strand = Strand::where('code', 'ABM')->first();
if (!$strand) {
    echo "❌ ABM strand not found!\n";
    exit(1);
}
echo "✓ Strand: {$strand->code} (ID: {$strand->id})\n\n";

// Check adviser for ABM
$adviser = AcademicYearStrandAdviser::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->first();

if (!$adviser) {
    echo "⚠ No adviser found for ABM. Creating one...\n";
    $teacher = Teacher::where('status', 'active')->first();
    if (!$teacher) {
        echo "❌ No teachers found!\n";
        exit(1);
    }
    $adviser = AcademicYearStrandAdviser::create([
        'academic_year_id' => $activeYear->id,
        'strand_id' => $strand->id,
        'teacher_id' => $teacher->id
    ]);
    echo "✓ Created adviser (ID: {$adviser->id}) with teacher: {$teacher->first_name} {$teacher->last_name}\n\n";
} else {
    echo "✓ Adviser exists (ID: {$adviser->id})\n\n";
}

// Get a subject
$subject = Subject::first();
if (!$subject) {
    echo "❌ No subjects found!\n";
    exit(1);
}
echo "✓ Subject: {$subject->name} (ID: {$subject->id})\n\n";

// Get a teacher profiled for this subject
$teacher = Teacher::where('status', 'active')
    ->whereHas('subjects', function($q) use ($subject) {
        $q->where('subject_id', $subject->id);
    })
    ->first();

if (!$teacher) {
    echo "❌ No teacher profiled for subject {$subject->name}!\n";
    exit(1);
}
echo "✓ Teacher: {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->id})\n\n";

// Try to create assignment
echo "Attempting to assign teacher to subject...\n";
try {
    $assignment = AcademicYearStrandSubject::updateOrCreate(
        [
            'academic_year_id' => $activeYear->id,
            'strand_id' => $strand->id,
            'subject_id' => $subject->id,
        ],
        [
            'academic_year_strand_adviser_id' => $adviser->id,
            'teacher_id' => $teacher->id,
        ]
    );
    
    echo "✅ Assignment created/updated!\n";
    echo "   ID: {$assignment->id}\n";
    echo "   Subject: {$subject->name}\n";
    echo "   Teacher: {$teacher->first_name} {$teacher->last_name}\n";
    echo "   Was Recently Created: " . ($assignment->wasRecentlyCreated ? 'Yes' : 'No') . "\n\n";
    
    // Verify it was saved
    $verify = AcademicYearStrandSubject::find($assignment->id);
    if ($verify) {
        echo "✅ Verified in database\n";
        echo "   Teacher ID: {$verify->teacher_id}\n";
        echo "   Adviser ID: {$verify->academic_year_strand_adviser_id}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\nDone!\n";
