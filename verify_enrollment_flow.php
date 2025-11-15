<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICATION: Pre-Enrollment to Section & Advisers Flow ===\n\n";

$student = \App\Models\Student::where('student_number', '2025-00005')->first();
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

echo "Student: {$student->first_name} {$student->last_name} ({$student->student_number})\n";
echo "Active Year: {$activeYear->name}\n\n";

// 1. Check Pre-Enrollment
echo "Step 1: Pre-Enrollment Status\n";
echo "==============================\n";
$preEnrollment = \App\Models\PreEnrollment::where('student_id', $student->id)
    ->where('status', 'enrolled')
    ->first();

if ($preEnrollment) {
    echo "✅ Pre-Enrollment Status: {$preEnrollment->status}\n";
    echo "   Target Grade: {$preEnrollment->grade_level}\n";
    echo "   Target Strand: {$preEnrollment->strand->code}\n";
    echo "   Target Section ID: {$preEnrollment->section_id}\n\n";
} else {
    echo "❌ No enrolled pre-enrollment found\n\n";
}

// 2. Check StudentEnrollment
echo "Step 2: Student Enrollment Record\n";
echo "==================================\n";
$enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if ($enrollment) {
    echo "✅ Enrollment ID: {$enrollment->id}\n";
    echo "   Status: {$enrollment->status}\n";
    echo "   Strand ID: {$enrollment->strand_id}\n";
    echo "   AYSS ID: {$enrollment->academic_year_strand_section_id}\n";
    echo "   Registration: {$enrollment->registration_number}\n\n";
} else {
    echo "❌ No enrollment found for active year\n\n";
}

// 3. Check AcademicYearStrandSection
echo "Step 3: Academic Year Strand Section (AYSS)\n";
echo "============================================\n";
if ($enrollment && $enrollment->academic_year_strand_section_id) {
    $ayss = \App\Models\AcademicYearStrandSection::with(['strand', 'section'])
        ->find($enrollment->academic_year_strand_section_id);
    
    if ($ayss) {
        echo "✅ AYSS ID: {$ayss->id}\n";
        echo "   Strand: {$ayss->strand->code} (ID: {$ayss->strand_id})\n";
        echo "   Section: {$ayss->section->name} (ID: {$ayss->section_id})\n";
        echo "   Grade: {$ayss->section->grade}\n\n";
        
        $sectionKey = "{$ayss->strand->code}-{$ayss->section_id}";
        echo "   Frontend Key: {$sectionKey}\n\n";
    } else {
        echo "❌ AYSS record not found!\n\n";
    }
}

// 4. Test getSectionStudents API
echo "Step 4: API Test - getSectionStudents\n";
echo "======================================\n";
if ($enrollment && $ayss) {
    $students = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
        ->with('student')
        ->get();
    
    echo "✅ API Query Result: {$students->count()} student(s)\n";
    foreach ($students as $s) {
        echo "   - {$s->student->student_number}: {$s->student->first_name} {$s->student->last_name}\n";
    }
    echo "\n";
}

// 5. Test getSectionCounts API
echo "Step 5: API Test - getSectionCounts\n";
echo "====================================\n";
if ($ayss) {
    $count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)->count();
    $key = $ayss->strand->code . '-' . $ayss->section_id;
    echo "✅ Section Key: {$key}\n";
    echo "   Student Count: {$count}\n\n";
}

// 6. Final Verification
echo "=== FINAL VERIFICATION ===\n";
if ($enrollment && $ayss && $count > 0) {
    echo "✅ ALL SYSTEMS WORKING!\n\n";
    echo "Student IS appearing in Section & Advisers Management:\n";
    echo "  → Strand: {$ayss->strand->code}\n";
    echo "  → Section: {$ayss->section->grade} {$ayss->section->name}\n";
    echo "  → Count: {$count} student(s)\n\n";
    echo "To view in UI:\n";
    echo "  1. Go to Section & Advisers page\n";
    echo "  2. Look for the {$ayss->strand->code} card\n";
    echo "  3. Find section '{$ayss->section->grade} {$ayss->section->name}'\n";
    echo "  4. You should see '{$count} student(s)' in green/bold\n";
    echo "  5. Click the eye icon to view students\n";
} else {
    echo "❌ ISSUE DETECTED - Student not appearing properly\n";
}

echo "\n=== Done ===\n";
