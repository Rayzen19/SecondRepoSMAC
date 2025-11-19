<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Enrolling Grade 11 ABM-A Students ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    die("❌ No active academic year found!\n");
}
echo "Active Year: {$activeYear->name}\n";

// Get ABM strand
$strand = \App\Models\Strand::where('code', 'ABM')->first();
if (!$strand) {
    die("❌ ABM strand not found!\n");
}
echo "Strand: {$strand->code} - {$strand->name}\n";

// Get ABM-A section (A - JUDE)
$section = \App\Models\Section::where('strand_id', $strand->id)
    ->where('grade', 'G-11')
    ->where('name', 'LIKE', 'A - JUDE')
    ->first();

if (!$section) {
    die("❌ ABM-A section not found!\n");
}
echo "Section: {$section->name} (ID: {$section->id})\n";

// Get AYSS
$ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('section_id', $section->id)
    ->first();

if (!$ayss) {
    die("❌ AYSS record not found for section!\n");
}
echo "AYSS ID: {$ayss->id}\n\n";

// Get all 20 students we just created (STU-000041 to STU-000060)
$students = \App\Models\Student::whereBetween('student_number', ['STU-000041', 'STU-000060'])
    ->where('program', 'ABM')
    ->where('academic_year', 'G-11')
    ->orderBy('student_number')
    ->get();

echo "Found {$students->count()} students to enroll\n\n";

if ($students->count() !== 20) {
    echo "⚠️ Warning: Expected 20 students but found {$students->count()}\n\n";
}

$enrolledCount = 0;
$skippedCount = 0;
$errorCount = 0;

// Get starting registration number once - check including soft deleted records
$year = date('Y');
$prefix = "REG-{$year}-";

// Use withTrashed to include soft-deleted records
$lastEnrollment = \App\Models\StudentEnrollment::withTrashed()
    ->where('registration_number', 'like', "{$prefix}%")
    ->orderBy('registration_number', 'desc')
    ->first();

if ($lastEnrollment) {
    $currentNumber = (int) str_replace($prefix, '', $lastEnrollment->registration_number);
    echo "Found last registration number: {$lastEnrollment->registration_number}\n";
    echo "Starting from number: " . ($currentNumber + 1) . "\n\n";
} else {
    $currentNumber = 0;
    echo "No existing registrations found, starting from 1\n\n";
}

foreach ($students as $student) {
    try {
        // Check if already enrolled
        $existing = \App\Models\StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->first();
        
        if ($existing) {
            echo "⚠️ [{$student->student_number}] {$student->first_name} {$student->last_name} - Already enrolled\n";
            $skippedCount++;
            continue;
        }
        
        // Generate registration number
        $currentNumber++;
        $registrationNumber = $prefix . str_pad($currentNumber, 5, '0', STR_PAD_LEFT);
        
        // Create enrollment
        $enrollment = \App\Models\StudentEnrollment::create([
            'student_id' => $student->id,
            'strand_id' => $strand->id,
            'academic_year_id' => $activeYear->id,
            'academic_year_strand_section_id' => $ayss->id,
            'registration_number' => $registrationNumber,
            'status' => 'enrolled'
        ]);
        
        $enrolledCount++;
        echo "✓ [{$student->student_number}] {$student->first_name} {$student->last_name} - Enrolled (Reg: {$registrationNumber})\n";
        
    } catch (\Exception $e) {
        $errorCount++;
        echo "✗ [{$student->student_number}] {$student->first_name} {$student->last_name} - Error: " . $e->getMessage() . "\n";
    }
}

// Verify final count
$finalCount = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
    ->where('academic_year_id', $activeYear->id)
    ->count();

echo "\n" . str_repeat("=", 80) . "\n";
echo "ENROLLMENT SUMMARY\n";
echo str_repeat("=", 80) . "\n";
echo "✓ Successfully enrolled: {$enrolledCount} students\n";
if ($skippedCount > 0) {
    echo "⚠ Skipped (already enrolled): {$skippedCount} students\n";
}
if ($errorCount > 0) {
    echo "✗ Errors: {$errorCount} students\n";
}
echo "\nTotal students now in {$section->name}: {$finalCount}\n";
echo "Academic Year: {$activeYear->name}\n";
echo "Strand: {$strand->code} - {$strand->name}\n";
echo "Section: {$section->name}\n";
echo str_repeat("=", 80) . "\n";

// Display all enrolled students
if ($finalCount > 0) {
    echo "\nAll students enrolled in {$section->name}:\n\n";
    
    $enrollments = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
        ->where('academic_year_id', $activeYear->id)
        ->with('student')
        ->orderBy('registration_number')
        ->get();
    
    foreach ($enrollments as $index => $enrollment) {
        $student = $enrollment->student;
        echo ($index + 1) . ". {$student->student_number} - {$student->first_name} {$student->last_name}\n";
        echo "   Registration: {$enrollment->registration_number}\n";
        echo "   Status: {$enrollment->status}\n\n";
    }
}

echo "✅ Enrollment complete!\n";
