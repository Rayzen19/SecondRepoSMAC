<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Pre-Enrollment Status & Fix for John Raymond ===\n\n";

$student = \App\Models\Student::where('student_number', '2025-00005')->first();
echo "Student: {$student->first_name} {$student->last_name} (ID: {$student->id})\n\n";

$preEnrollments = \App\Models\PreEnrollment::where('student_id', $student->id)
    ->with(['strand', 'section', 'targetAcademicYear'])
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total pre-enrollments: {$preEnrollments->count()}\n\n";

foreach ($preEnrollments as $pe) {
    echo "Pre-Enrollment ID: {$pe->id}\n";
    echo "Status: {$pe->status}\n";
    echo "Target Year: " . ($pe->targetAcademicYear->name ?? 'N/A') . "\n";
    echo "Strand: " . ($pe->strand->code ?? 'N/A') . "\n";
    echo "Section: " . ($pe->section->name ?? 'N/A') . "\n";
    echo "Grade Level: {$pe->grade_level}\n";
    echo "Submitted: {$pe->submitted_at}\n";
    echo "Processed: " . ($pe->processed_at ?? 'Not processed') . "\n";
    echo "\n";
}

echo "=== Creating Enrollment ===\n";

$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
$approvedPE = \App\Models\PreEnrollment::where('student_id', $student->id)
    ->where('status', 'enrolled')
    ->first();

if (!$approvedPE) {
    echo "❌ No approved/enrolled pre-enrollment found\n";
    exit;
}

echo "Using pre-enrollment:\n";
echo "  Strand: {$approvedPE->strand->code}\n";
echo "  Section ID: {$approvedPE->section_id}\n";
echo "  Grade Level: {$approvedPE->grade_level}\n\n";

// Find the correct AYSS
$ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $approvedPE->strand_id)
    ->where('section_id', $approvedPE->section_id)
    ->first();

if (!$ayss) {
    echo "⚠️ AYSS not found for approved section. Looking for alternative...\n";
    
    // Find any section for this strand and grade level
    $section = \App\Models\Section::where('strand_id', $approvedPE->strand_id)
        ->where('grade', $approvedPE->grade_level)
        ->first();
    
    if ($section) {
        echo "Found section: {$section->name}\n";
        $ayss = \App\Models\AcademicYearStrandSection::firstOrCreate(
            [
                'academic_year_id' => $activeYear->id,
                'strand_id' => $approvedPE->strand_id,
                'section_id' => $section->id,
            ],
            ['is_active' => true]
        );
        echo "Created/found AYSS ID: {$ayss->id}\n";
    }
}

if (!$ayss) {
    echo "❌ Could not find or create AYSS\n";
    exit;
}

echo "Using AYSS ID: {$ayss->id}\n";
echo "  Section: {$ayss->section->name}\n";
echo "  Strand: {$ayss->strand->code}\n\n";

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

echo "Registration number: {$registrationNumber}\n\n";

// Create enrollment
$enrollment = \App\Models\StudentEnrollment::create([
    'student_id' => $student->id,
    'academic_year_id' => $activeYear->id,
    'strand_id' => $approvedPE->strand_id,
    'academic_year_strand_section_id' => $ayss->id,
    'registration_number' => $registrationNumber,
    'status' => 'enrolled',
]);

echo "✅ Enrollment created!\n";
echo "   Enrollment ID: {$enrollment->id}\n";
echo "   Student: {$student->student_number}\n";
echo "   Section: {$ayss->section->name}\n";
echo "   Strand: {$ayss->strand->code}\n";
echo "   Status: {$enrollment->status}\n\n";

// Sync subject enrollments
$enrollment->syncSubjectEnrollments();
echo "✅ Subject enrollments synced\n\n";

echo "=== Done ===\n";
echo "Student should now be able to access the dashboard without errors.\n";
echo "They will appear in Section & Advisers: {$ayss->strand->code} - {$ayss->section->name}\n";
