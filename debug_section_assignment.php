<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debugging Section Assignment for John Raymond ===\n\n";

$student = \App\Models\Student::where('student_number', '2025-00005')->first();
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

echo "Student ID: {$student->id}\n";
echo "Active Year ID: {$activeYear->id}\n\n";

// Check enrollment
$enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if (!$enrollment) {
    echo "❌ NO ENROLLMENT FOUND\n";
    exit(1);
}

echo "✓ Enrollment ID: {$enrollment->id}\n";
echo "  Status: {$enrollment->status}\n";
echo "  Strand ID: {$enrollment->strand_id}\n";
echo "  Academic Year Strand Section ID: " . ($enrollment->academic_year_strand_section_id ?? 'NULL') . "\n\n";

if (!$enrollment->academic_year_strand_section_id) {
    echo "❌ PROBLEM FOUND: academic_year_strand_section_id is NULL!\n";
    echo "This is why the student doesn't appear in Section & Advisers.\n\n";
    
    // Try to find the correct section assignment
    echo "Looking for section assignment...\n";
    
    $strand = \App\Models\Strand::find($enrollment->strand_id);
    echo "Strand: {$strand->code}\n";
    
    // Check pre-enrollment to see what section was intended
    $preEnrollment = \App\Models\PreEnrollment::where('student_id', $student->id)
        ->where('status', 'enrolled')
        ->first();
    
    if ($preEnrollment) {
        echo "Pre-Enrollment Section ID: {$preEnrollment->section_id}\n";
        echo "Pre-Enrollment Grade Level: {$preEnrollment->grade_level}\n\n";
        
        // Find the academic_year_strand_section
        $ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
            ->where('strand_id', $strand->id)
            ->where('section_id', $preEnrollment->section_id)
            ->first();
        
        if ($ayss) {
            echo "✓ Found AcademicYearStrandSection: {$ayss->id}\n";
            echo "  Section: " . $ayss->section->name . "\n";
            echo "  Grade: " . $ayss->section->grade . "\n\n";
            
            echo "FIXING: Updating enrollment with correct academic_year_strand_section_id...\n";
            $enrollment->update(['academic_year_strand_section_id' => $ayss->id]);
            echo "✅ FIXED! Student should now appear in Section & Advisers.\n";
        } else {
            echo "❌ AcademicYearStrandSection NOT FOUND for:\n";
            echo "   Academic Year: {$activeYear->id}\n";
            echo "   Strand: {$strand->id} ({$strand->code})\n";
            echo "   Section: {$preEnrollment->section_id}\n\n";
            
            echo "Available AcademicYearStrandSection records for this year:\n";
            $all = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
                ->with(['strand', 'section'])
                ->get();
            foreach ($all as $a) {
                echo "  - ID: {$a->id}, Strand: {$a->strand->code}, Section: {$a->section->name}, Grade: {$a->section->grade}\n";
            }
        }
    }
} else {
    echo "✓ academic_year_strand_section_id is set: {$enrollment->academic_year_strand_section_id}\n";
    
    // Check if the record exists
    $ayss = \App\Models\AcademicYearStrandSection::find($enrollment->academic_year_strand_section_id);
    
    if ($ayss) {
        echo "✓ AcademicYearStrandSection exists:\n";
        echo "  Section: {$ayss->section->name}\n";
        echo "  Strand: {$ayss->strand->code}\n";
        echo "  Grade: {$ayss->section->grade}\n\n";
        echo "✅ Everything looks correct! Student should appear in Section & Advisers.\n";
        echo "Try refreshing the Section & Advisers page.\n";
    } else {
        echo "❌ PROBLEM: AcademicYearStrandSection ID {$enrollment->academic_year_strand_section_id} does NOT exist!\n";
        echo "The enrollment references a deleted or invalid section assignment.\n";
    }
}

echo "\n=== Done ===\n";
