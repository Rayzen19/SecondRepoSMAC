<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking JUDE Section Assignments ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit;
}
echo "✅ Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Find section JUDE
$section = \App\Models\Section::where('name', 'LIKE', '%JUDE%')
    ->orWhere('name', 'LIKE', '%A - JUDE%')
    ->first();

if (!$section) {
    echo "❌ Section JUDE not found!\n";
    exit;
}
echo "✅ Section Found: Grade {$section->grade} {$section->name} (ID: {$section->id})\n\n";

// Find ABM strand
$abmStrand = \App\Models\Strand::where('code', 'ABM')->first();
if (!$abmStrand) {
    echo "❌ ABM Strand not found!\n";
    exit;
}
echo "✅ ABM Strand: {$abmStrand->name} (ID: {$abmStrand->id})\n\n";

// Check if academic_year_strand_section exists
$ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $abmStrand->id)
    ->where('section_id', $section->id)
    ->first();

if (!$ayss) {
    echo "❌ No AcademicYearStrandSection record found for ABM + JUDE in active year!\n";
    echo "Creating one now...\n";
    
    $ayss = \App\Models\AcademicYearStrandSection::create([
        'academic_year_id' => $activeYear->id,
        'strand_id' => $abmStrand->id,
        'section_id' => $section->id,
        'is_active' => true,
    ]);
    echo "✅ Created AcademicYearStrandSection (ID: {$ayss->id})\n\n";
} else {
    echo "✅ AcademicYearStrandSection exists (ID: {$ayss->id})\n\n";
}

// Check student enrollments
$enrollments = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
    ->where('academic_year_id', $activeYear->id)
    ->with('student')
    ->get();

echo "📊 Students enrolled in JUDE section: " . $enrollments->count() . "\n";
if ($enrollments->count() > 0) {
    echo "\nStudent List:\n";
    foreach ($enrollments as $enrollment) {
        if ($enrollment->student) {
            echo "  - {$enrollment->student->student_number}: {$enrollment->student->first_name} {$enrollment->student->last_name}\n";
        }
    }
} else {
    echo "  (No students found)\n";
}

echo "\n=== Checking Recent Student Assignments ===\n";
// Check students with ABM program
$abmStudents = \App\Models\Student::where('program', 'ABM')
    ->orderBy('last_name')
    ->take(5)
    ->get();

echo "Sample ABM Students:\n";
foreach ($abmStudents as $student) {
    $enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
        ->where('academic_year_id', $activeYear->id)
        ->with('academicYearStrandSection.section')
        ->first();
    
    $sectionInfo = 'Not enrolled';
    if ($enrollment && $enrollment->academicYearStrandSection && $enrollment->academicYearStrandSection->section) {
        $sectionInfo = $enrollment->academicYearStrandSection->section->name;
    }
    
    echo "  - {$student->student_number}: {$student->first_name} {$student->last_name} => {$sectionInfo}\n";
}

echo "\n✅ Check complete!\n";
