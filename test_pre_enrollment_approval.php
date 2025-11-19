<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Testing Pre-Enrollment Approval Process ===\n\n";

// Find the approved pre-enrollment that hasn't been processed
$preEnrollment = \App\Models\PreEnrollment::with(['student', 'strand', 'section', 'currentAcademicYear'])
    ->where('status', 'approved')
    ->first();

if (!$preEnrollment) {
    echo "❌ No approved pre-enrollments found to process.\n";
    exit;
}

$student = $preEnrollment->student;
echo "Processing Pre-Enrollment:\n";
echo "  Student: {$student->first_name} {$student->last_name} ({$student->student_number})\n";
echo "  Grade Level: {$preEnrollment->grade_level}\n";
echo "  Strand: {$preEnrollment->strand->code}\n";
echo "  Section Preference: " . ($preEnrollment->section ? $preEnrollment->section->name : 'None') . "\n";
echo "  Academic Year: {$preEnrollment->currentAcademicYear->name}\n\n";

// Simulate the new approval logic
DB::beginTransaction();
try {
    // Find or use the current academic year if no target year is set
    $targetAcademicYearId = $preEnrollment->target_academic_year_id ?? $preEnrollment->current_academic_year_id;
    echo "Target Academic Year ID: {$targetAcademicYearId}\n";
    
    // Check if section is selected, otherwise find available section
    $sectionId = $preEnrollment->section_id;
    
    if (!$sectionId) {
        echo "No section preference, finding available section...\n";
        $availableSection = \App\Models\Section::where('strand_id', $preEnrollment->strand_id)
            ->where('grade', $preEnrollment->grade_level)
            ->first();
        
        if ($availableSection) {
            $sectionId = $availableSection->id;
            echo "Found section: {$availableSection->name}\n";
        } else {
            throw new \Exception('No section found for strand ' . $preEnrollment->strand->code . ' and grade ' . $preEnrollment->grade_level);
        }
    } else {
        echo "Using preferred section ID: {$sectionId}\n";
    }
    
    // Find the section assignment for the target year
    echo "\nLooking for section assignment...\n";
    echo "  Academic Year ID: {$targetAcademicYearId}\n";
    echo "  Strand ID: {$preEnrollment->strand_id}\n";
    echo "  Section ID: {$sectionId}\n\n";
    
    $sectionAssignment = \App\Models\AcademicYearStrandSection::with(['section'])
        ->where('academic_year_id', $targetAcademicYearId)
        ->where('strand_id', $preEnrollment->strand_id)
        ->where('section_id', $sectionId)
        ->first();

    if (!$sectionAssignment) {
        echo "❌ Section assignment not found!\n";
        echo "Available section assignments for this year and strand:\n";
        
        $available = \App\Models\AcademicYearStrandSection::with(['section'])
            ->where('academic_year_id', $targetAcademicYearId)
            ->where('strand_id', $preEnrollment->strand_id)
            ->get();
        
        foreach ($available as $ayss) {
            echo "  - ID: {$ayss->id} | Section: {$ayss->section->name} ({$ayss->section->grade})\n";
        }
        
        throw new \Exception('Section assignment not found for the academic year.');
    }

    echo "✅ Section assignment found: {$sectionAssignment->section->name} (ID: {$sectionAssignment->id})\n\n";

    // Check if student already has enrollment for this academic year
    $existingEnrollment = \App\Models\StudentEnrollment::where('student_id', $preEnrollment->student_id)
        ->where('academic_year_id', $targetAcademicYearId)
        ->first();

    if ($existingEnrollment) {
        echo "⚠️ Student already has an enrollment for this academic year (ID: {$existingEnrollment->id})\n";
        echo "Enrollment details:\n";
        echo "  Registration: {$existingEnrollment->registration_number}\n";
        echo "  Status: {$existingEnrollment->status}\n";
        echo "  Section Assignment ID: {$existingEnrollment->academic_year_strand_section_id}\n\n";
        
        DB::rollBack();
        
        echo "Updating pre-enrollment status to 'enrolled' anyway...\n";
        $preEnrollment->update([
            'status' => 'enrolled',
            'processed_at' => now(),
        ]);
        
        echo "✅ Pre-enrollment marked as enrolled.\n";
        exit;
    }

    // Generate registration number
    $year = date('Y');
    $prefix = "REG-{$year}-";
    
    $maxReg = DB::table('student_enrollments')
        ->where('registration_number', 'like', "{$prefix}%")
        ->orderByRaw('CAST(SUBSTRING(registration_number, LENGTH(?)+1) AS UNSIGNED) DESC', [$prefix])
        ->value('registration_number');
    
    if ($maxReg) {
        $lastNumber = (int) str_replace($prefix, '', $maxReg);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    $registrationNumber = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    echo "Generated Registration Number: {$registrationNumber}\n\n";
    
    echo "Creating enrollment...\n";
    $enrollment = \App\Models\StudentEnrollment::create([
        'student_id' => $preEnrollment->student_id,
        'academic_year_id' => $targetAcademicYearId,
        'strand_id' => $preEnrollment->strand_id,
        'academic_year_strand_section_id' => $sectionAssignment->id,
        'registration_number' => $registrationNumber,
        'status' => 'enrolled',
    ]);

    echo "✅ Enrollment created (ID: {$enrollment->id})\n\n";

    // Sync subject enrollments
    echo "Syncing subject enrollments...\n";
    $created = $enrollment->syncSubjectEnrollments();
    echo "✅ Created {$created} subject enrollments\n\n";

    // Update pre-enrollment status to enrolled
    echo "Updating pre-enrollment status...\n";
    $preEnrollment->update([
        'status' => 'enrolled',
        'processed_at' => now(),
        'section_id' => $sectionId,
    ]);

    DB::commit();

    echo "\n✅ SUCCESS! Student has been enrolled and added to {$sectionAssignment->section->name}.\n";
    echo "The student should now appear in Section Advisers Management.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== Done ===\n";
