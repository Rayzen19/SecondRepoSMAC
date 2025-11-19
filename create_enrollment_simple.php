<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Creating Enrollment for John Raymond Barrogo ===\n\n";

try {
    DB::beginTransaction();
    
    $student = \App\Models\Student::where('student_number', '2025-00005')->first();
    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
    
    echo "Student: {$student->first_name} {$student->last_name}\n";
    echo "Academic Year: {$activeYear->name}\n\n";
    
    // Check for existing enrollment
    $existing = \App\Models\StudentEnrollment::where('student_id', $student->id)
        ->where('academic_year_id', $activeYear->id)
        ->first();
    
    if ($existing) {
        echo "✅ Enrollment already exists (ID: {$existing->id})\n";
        DB::rollBack();
        exit;
    }
    
    // Find STEM Grade 11 section
    $stemStrand = \App\Models\Strand::where('code', 'STEM')->first();
    $section = \App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $stemStrand->id)
        ->whereHas('section', function($q) {
            $q->where('grade', 'LIKE', '%11%');
        })
        ->first();
    
    echo "Strand: {$stemStrand->code}\n";
    echo "Section: {$section->section->name}\n\n";
    
    // Generate unique registration number
    $year = date('Y');
    $prefix = "REG-{$year}-";
    
    // Get max number
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
    echo "Registration Number: {$registrationNumber}\n\n";
    
    // Create enrollment
    $enrollment = \App\Models\StudentEnrollment::create([
        'student_id' => $student->id,
        'academic_year_id' => $activeYear->id,
        'strand_id' => $stemStrand->id,
        'academic_year_strand_section_id' => $section->id,
        'registration_number' => $registrationNumber,
        'status' => 'enrolled',
    ]);
    
    echo "✅ Enrollment created (ID: {$enrollment->id})\n\n";
    
    // Sync subject enrollments
    echo "🔄 Syncing subject enrollments...\n";
    $created = $enrollment->syncSubjectEnrollments();
    echo "✅ Created {$created} subject enrollments\n\n";
    
    DB::commit();
    
    echo "=== SUCCESS ===\n";
    echo "Student is now enrolled and can access the dashboard.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
