<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\Section;
use App\Models\Strand;
use App\Models\AcademicYear;
use App\Models\AcademicYearStrandSection;
use App\Models\AcademicYearStrandSubject;
use App\Models\Subject;

echo "=== Testing Section Assignment Flow ===\n\n";

// Find the teacher
$teacher = Teacher::where('last_name', 'Barrogo')->first();
if (!$teacher) {
    echo "❌ Teacher Barrogo not found\n";
    $teachers = Teacher::orderBy('last_name')->get();
    echo "\nAvailable teachers:\n";
    foreach ($teachers as $t) {
        echo "  - {$t->first_name} {$t->last_name} (ID: {$t->id})\n";
    }
    exit(1);
}

echo "✅ Teacher: {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->id})\n\n";

// Find G-11 MARCH section
$section = Section::where('name', 'MARCH')->where('grade', 'G-11')->first();
if (!$section) {
    $section = Section::where('name', 'MARCH')->first(); // Try without grade filter
    if (!$section) {
        echo "❌ Section MARCH not found\n";
        echo "\nAvailable sections:\n";
        $sections = Section::orderBy('grade')->orderBy('name')->get();
        foreach ($sections as $s) {
            echo "  - Grade {$s->grade}, Section {$s->name} (ID: {$s->id})\n";
        }
        exit(1);
    }
}

echo "✅ Section: Grade {$section->grade}, {$section->name} (ID: {$section->id})\n\n";

// Find STEM strand
$strand = Strand::where('code', 'STEM')->first();
if (!$strand) {
    echo "❌ STEM strand not found\n";
    exit(1);
}

echo "✅ Strand: {$strand->name} ({$strand->code}, ID: {$strand->id})\n\n";

// Find active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found\n";
    exit(1);
}

echo "✅ Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Find or create AcademicYearStrandSection
$aysSection = AcademicYearStrandSection::firstOrCreate(
    [
        'academic_year_id' => $activeYear->id,
        'strand_id' => $strand->id,
        'section_id' => $section->id,
    ],
    [
        'is_active' => true,
    ]
);

echo "✅ AcademicYearStrandSection ID: {$aysSection->id}\n\n";

// Find Oral Communication subject
$subject = Subject::where('name', 'Oral Communication')->first();
if (!$subject) {
    echo "❌ Subject 'Oral Communication' not found\n";
    exit(1);
}

echo "✅ Subject: {$subject->name} (ID: {$subject->id})\n\n";

// Check current assignment
$currentAssignment = AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('subject_id', $subject->id)
    ->where('teacher_id', $teacher->id)
    ->first();

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "CURRENT ASSIGNMENT STATUS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if ($currentAssignment) {
    echo "✅ Assignment exists (ID: {$currentAssignment->id})\n";
    echo "   - Teacher ID: {$currentAssignment->teacher_id}\n";
    echo "   - Subject ID: {$currentAssignment->subject_id}\n";
    echo "   - Strand ID: {$currentAssignment->strand_id}\n";
    echo "   - Academic Year ID: {$currentAssignment->academic_year_id}\n";
    echo "   - Section ID (AYS): {$currentAssignment->academic_year_strand_section_id}\n";
    
    if ($currentAssignment->academic_year_strand_section_id) {
        $assignedSection = AcademicYearStrandSection::with('section')->find($currentAssignment->academic_year_strand_section_id);
        if ($assignedSection && $assignedSection->section) {
            echo "   - Section Name: Grade {$assignedSection->section->grade}, {$assignedSection->section->name}\n";
            echo "\n   ✅ SECTION IS ASSIGNED!\n";
        }
    } else {
        echo "\n   ❌ NO SECTION ASSIGNED (NULL)\n";
        echo "\n   💡 To fix this:\n";
        echo "   1. Delete this assignment\n";
        echo "   2. Go to Section & Advisers page\n";
        echo "   3. Select: {$activeYear->name} > STEM > MARCH\n";
        echo "   4. Assign adviser first\n";
        echo "   5. Click 'Assign Teacher' button\n";
        echo "   6. Assign teacher to Oral Communication\n";
        echo "   7. Click Save\n";
    }
} else {
    echo "❌ No assignment found\n\n";
    echo "   💡 To assign:\n";
    echo "   1. Go to Section & Advisers page\n";
    echo "   2. Select: {$activeYear->name} > STEM > MARCH\n";
    echo "   3. Assign adviser first\n";
    echo "   4. Click 'Assign Teacher' button\n";
    echo "   5. Assign teacher to Oral Communication\n";
    echo "   6. Click Save\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "EXPECTED FLOW FOR SECTION ASSIGNMENT:\n\n";
echo "Frontend sends:\n";
echo "{\n";
echo "  \"strand_code\": \"STEM\",\n";
echo "  \"grade_level\": \"11\",\n";
echo "  \"section_id\": {$section->id},  ← SECTION ID\n";
echo "  \"subject_id\": {$subject->id},\n";
echo "  \"teacher_id\": {$teacher->id}\n";
echo "}\n\n";

echo "Backend resolves:\n";
echo "  AcademicYearStrandSection ID: {$aysSection->id}\n\n";

echo "Backend saves:\n";
echo "  academic_year_strand_section_id: {$aysSection->id}\n\n";

echo "Result:\n";
echo "  Teacher profile will show: \"Oral Communication (Code) • STEM • Section MARCH\"\n\n";

echo "=== Test Complete ===\n";
