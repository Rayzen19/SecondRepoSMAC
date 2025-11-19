<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Manual Assignment ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
echo "Active Year: {$activeYear->name}\n";

// Get ABM strand
$strand = \App\Models\Strand::where('code', 'ABM')->first();
echo "Strand: {$strand->code}\n";

// Get JUDE section
$section = \App\Models\Section::where('name', 'LIKE', '%JUDE%')->first();
echo "Section: {$section->name} (ID: {$section->id})\n";

// Get AYSS
$ayss = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
    ->where('strand_id', $strand->id)
    ->where('section_id', $section->id)
    ->first();
echo "AYSS ID: {$ayss->id}\n\n";

// Get first ABM student
$student = \App\Models\Student::where('program', 'ABM')
    ->orderBy('last_name')
    ->first();

echo "Testing with student: {$student->student_number} - {$student->first_name} {$student->last_name}\n\n";

// Check existing enrollment
$existing = \App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if ($existing) {
    echo "⚠️ Student already has enrollment (ID: {$existing->id})\n";
    echo "   Current AYSS ID: " . ($existing->academic_year_strand_section_id ?? 'NULL') . "\n";
    echo "   Updating to JUDE section...\n";
    
    $existing->update([
        'strand_id' => $strand->id,
        'academic_year_strand_section_id' => $ayss->id,
        'status' => 'enrolled'
    ]);
    
    echo "✅ Updated enrollment!\n";
} else {
    echo "Creating new enrollment...\n";
    
    // Generate registration number
    $year = date('Y');
    $prefix = "REG-{$year}-";
    $lastEnrollment = \App\Models\StudentEnrollment::where('academic_year_id', $activeYear->id)
        ->where('registration_number', 'like', "{$prefix}%")
        ->orderBy('registration_number', 'desc')
        ->first();
    
    if ($lastEnrollment) {
        $lastNumber = (int) str_replace($prefix, '', $lastEnrollment->registration_number);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    $registrationNumber = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    echo "Registration Number: {$registrationNumber}\n";
    
    $enrollment = \App\Models\StudentEnrollment::create([
        'student_id' => $student->id,
        'strand_id' => $strand->id,
        'academic_year_id' => $activeYear->id,
        'academic_year_strand_section_id' => $ayss->id,
        'registration_number' => $registrationNumber,
        'status' => 'enrolled'
    ]);
    
    echo "✅ Created enrollment (ID: {$enrollment->id})\n";
}

// Verify
echo "\n=== Verification ===\n";
$count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
    ->where('academic_year_id', $activeYear->id)
    ->count();
echo "Total students in JUDE section: {$count}\n";

echo "\n✅ Test complete!\n";
