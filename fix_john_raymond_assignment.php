<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Diagnosing John Raymond Barrogo Assignment Issue\n";
echo "=================================================\n\n";

// Find the student
$student = App\Models\Student::where('student_number', '2025-00021')->first();

if (!$student) {
    echo "❌ Student with number 2025-00021 not found!\n";
    echo "Searching by name...\n";
    $student = App\Models\Student::where('first_name', 'LIKE', '%John%')
        ->where('last_name', 'LIKE', '%Barrogo%')
        ->first();
}

if (!$student) {
    echo "❌ Student not found at all!\n";
    exit(1);
}

echo "✅ Found Student:\n";
echo "   ID: {$student->id}\n";
echo "   Name: {$student->first_name} {$student->last_name}\n";
echo "   Student No: {$student->student_number}\n";
echo "   Program: {$student->program}\n";
echo "   Academic Year: {$student->academic_year}\n\n";

// Check if student has enrollment
$activeYear = App\Models\AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year found!\n";
    exit(1);
}

echo "✅ Active Academic Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

$enrollment = App\Models\StudentEnrollment::where('student_id', $student->id)
    ->where('academic_year_id', $activeYear->id)
    ->first();

if ($enrollment) {
    echo "✅ Student has enrollment:\n";
    echo "   Enrollment ID: {$enrollment->id}\n";
    echo "   Status: {$enrollment->status}\n";
    echo "   Registration Number: {$enrollment->registration_number}\n";
    echo "   Strand ID: {$enrollment->strand_id}\n";
    echo "   Section Assignment ID: {$enrollment->academic_year_strand_section_id}\n";
    
    if ($enrollment->academic_year_strand_section_id) {
        $sectionAssignment = App\Models\AcademicYearStrandSection::with(['section', 'strand'])
            ->find($enrollment->academic_year_strand_section_id);
        
        if ($sectionAssignment && $sectionAssignment->section) {
            echo "\n✅ Assigned to Section:\n";
            echo "   Section: {$sectionAssignment->section->name}\n";
            echo "   Grade: {$sectionAssignment->section->grade}\n";
            echo "   Strand: {$sectionAssignment->strand->code}\n";
        }
    } else {
        echo "\n❌ No section assignment found!\n";
        echo "   This is why the student shows 'Not assigned'\n";
    }
} else {
    echo "❌ No enrollment found for current academic year!\n\n";
    echo "Creating enrollment...\n";
    
    // Find STEM strand
    $stemStrand = App\Models\Strand::where('code', 'STEM')->first();
    if (!$stemStrand) {
        echo "❌ STEM strand not found!\n";
        exit(1);
    }
    
    // Find APRIL section
    $aprilSection = App\Models\Section::where('name', 'APRIL')
        ->where('strand_id', $stemStrand->id)
        ->where('grade', 'G-11')
        ->first();
    
    if (!$aprilSection) {
        echo "❌ APRIL section not found!\n";
        exit(1);
    }
    
    // Find academic year strand section
    $sectionAssignment = App\Models\AcademicYearStrandSection::where('academic_year_id', $activeYear->id)
        ->where('strand_id', $stemStrand->id)
        ->where('section_id', $aprilSection->id)
        ->first();
    
    if (!$sectionAssignment) {
        echo "❌ Section assignment (AYSS) not found! Creating...\n";
        $sectionAssignment = App\Models\AcademicYearStrandSection::create([
            'academic_year_id' => $activeYear->id,
            'strand_id' => $stemStrand->id,
            'section_id' => $aprilSection->id,
        ]);
    }
    
    // Generate registration number
    $year = date('Y');
    $prefix = "REG-{$year}-";
    $lastEnrollment = App\Models\StudentEnrollment::where('academic_year_id', $activeYear->id)
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
    
    // Create enrollment
    $enrollment = App\Models\StudentEnrollment::create([
        'student_id' => $student->id,
        'strand_id' => $stemStrand->id,
        'academic_year_id' => $activeYear->id,
        'academic_year_strand_section_id' => $sectionAssignment->id,
        'registration_number' => $registrationNumber,
        'status' => 'enrolled'
    ]);
    
    echo "✅ Created enrollment:\n";
    echo "   Enrollment ID: {$enrollment->id}\n";
    echo "   Registration Number: {$registrationNumber}\n";
    echo "   Section: APRIL (G-11 STEM)\n";
}

echo "\n🎉 DONE! John Raymond should now show 'APRIL' in the Section column.\n";
echo "Refresh your browser to see the changes.\n";
