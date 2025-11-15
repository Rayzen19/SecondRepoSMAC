<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Subject Assignments for STEM Grade 11 Section A - LUKE ===\n\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
$enrollment = \App\Models\StudentEnrollment::find(547);

echo "Enrollment ID: {$enrollment->id}\n";
echo "Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n";
echo "Section Assignment ID: {$enrollment->academic_year_strand_section_id}\n\n";

// Check what subjects are assigned to this section
$subjects = \App\Models\AcademicYearStrandSubject::with(['subject', 'teacher'])
    ->where('academic_year_id', $activeYear->id)
    ->where('academic_year_strand_section_id', $enrollment->academic_year_strand_section_id)
    ->get();

echo "📚 Subjects assigned to this section: {$subjects->count()}\n\n";

if ($subjects->isEmpty()) {
    echo "❌ No subjects assigned to this section yet!\n";
    echo "This is why no subject enrollments were created.\n\n";
    echo "To fix this:\n";
    echo "1. Go to Admin > Academic Years\n";
    echo "2. Select 2025-2026\n";
    echo "3. Assign subjects and teachers to Section A - LUKE (G-11) STEM\n";
} else {
    echo "Available subjects:\n";
    foreach ($subjects as $ayss) {
        echo "  - [{$ayss->subject->code}] {$ayss->subject->name}\n";
        echo "    Teacher: " . ($ayss->teacher ? $ayss->teacher->first_name . ' ' . $ayss->teacher->last_name : 'Not assigned') . "\n";
        echo "    AYSS ID: {$ayss->id}\n\n";
    }
    
    echo "Attempting to sync subject enrollments...\n";
    $created = $enrollment->syncSubjectEnrollments();
    echo "✅ Created {$created} new subject enrollments\n";
}

echo "\n=== Done ===\n";
