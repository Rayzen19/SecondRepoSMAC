<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Manual Assignment Script ===\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit;
}

// Get ABM strand
$abmStrand = \App\Models\Strand::where('code', 'ABM')->first();
if (!$abmStrand) {
    echo "❌ ABM Strand not found!\n";
    exit;
}

// Get JUDE section (ID = 1)
$judeSection = \App\Models\Section::find(1);
if (!$judeSection) {
    echo "❌ JUDE Section not found!\n";
    exit;
}

echo "Active Year: {$activeYear->name} (ID: {$activeYear->id})\n";
echo "Strand: {$abmStrand->code} (ID: {$abmStrand->id})\n";
echo "Section: {$judeSection->name} (ID: {$judeSection->id})\n\n";

// Get or create AYSS
$ayss = \App\Models\AcademicYearStrandSection::firstOrCreate(
    [
        'academic_year_id' => $activeYear->id,
        'strand_id' => $abmStrand->id,
        'section_id' => $judeSection->id,
    ],
    [
        'is_active' => true,
    ]
);

echo "AYSS ID: {$ayss->id}\n\n";

// Get ABM Grade 11 students
$students = \App\Models\Student::where('program', 'ABM')
    ->where(function($q) {
        $q->where('academic_year', 'LIKE', '%11%')
          ->orWhere('academic_year', 'LIKE', '%G-11%');
    })
    ->orderBy('last_name')
    ->take(5) // Just first 5 for testing
    ->get();

echo "Enrolling " . $students->count() . " students to JUDE section:\n\n";

$year = date('Y');
$prefix = "REG-{$year}-";

foreach ($students as $student) {
    // Check existing enrollment
    $existing = \App\Models\StudentEnrollment::where('student_id', $student->id)
        ->where('academic_year_id', $activeYear->id)
        ->first();
    
    if ($existing) {
        // Update
        $existing->update([
            'strand_id' => $abmStrand->id,
            'academic_year_strand_section_id' => $ayss->id,
            'status' => 'enrolled'
        ]);
        echo "✅ Updated: {$student->student_number} - {$student->first_name} {$student->last_name}\n";
    } else {
        // Create new
        // Get last reg number
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
        
        try {
            \App\Models\StudentEnrollment::create([
                'student_id' => $student->id,
                'strand_id' => $abmStrand->id,
                'academic_year_id' => $activeYear->id,
                'academic_year_strand_section_id' => $ayss->id,
                'registration_number' => $registrationNumber,
                'status' => 'enrolled'
            ]);
            echo "✅ Created: {$student->student_number} - {$student->first_name} {$student->last_name} (Reg: {$registrationNumber})\n";
        } catch (\Exception $e) {
            echo "❌ Error for {$student->student_number}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Verification ===\n";
$count = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', $ayss->id)
    ->where('academic_year_id', $activeYear->id)
    ->count();
echo "Total students now in JUDE section: {$count}\n";

echo "\n✅ Assignment complete!\n";
echo "\nNow refresh the Section & Advisers page to see the students!\n";
