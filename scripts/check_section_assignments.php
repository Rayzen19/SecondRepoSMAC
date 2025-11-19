<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYearStrandSubject;

echo "=== Checking Subject-Teacher Assignments in Database ===\n\n";

$assignments = AcademicYearStrandSubject::with([
    'subject', 
    'strand', 
    'teacher', 
    'sectionAssignment.section'
])
->whereNotNull('teacher_id')
->get();

echo "Total assignments: " . $assignments->count() . "\n\n";

if ($assignments->isEmpty()) {
    echo "❌ No subject-teacher assignments found.\n";
    echo "Please assign teachers via Section & Advisers interface.\n";
    exit(1);
}

foreach ($assignments as $a) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Teacher: " . optional($a->teacher)->first_name . ' ' . optional($a->teacher)->last_name . "\n";
    echo "Subject: " . optional($a->subject)->name . "\n";
    echo "Strand: " . optional($a->strand)->name . "\n";
    echo "Section ID (in DB column): " . ($a->academic_year_strand_section_id ?? 'NULL') . "\n";
    
    if ($a->academic_year_strand_section_id) {
        $sectionName = optional(optional($a->sectionAssignment)->section)->name;
        echo "Section Name (via relationship): " . ($sectionName ?? 'Not loaded') . "\n";
        echo "✅ HAS SECTION - Will display on profile\n";
    } else {
        echo "Section Name: No specific section assigned\n";
        echo "ℹ️  GENERAL ASSIGNMENT - Section won't display\n";
    }
    
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "💡 To assign teachers to SPECIFIC SECTIONS:\n";
echo "1. Go to Section & Advisers management\n";
echo "2. Select Academic Year, Strand, and Section\n";
echo "3. Assign an adviser\n";
echo "4. Click 'Assign Teacher' button\n";
echo "5. Assign teachers to subjects\n";
echo "6. The section_id will be saved automatically\n";
