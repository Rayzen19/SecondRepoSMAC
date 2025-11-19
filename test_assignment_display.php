<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Assignment Display Fix\n";
echo "================================\n\n";

// Get active academic year
$activeYear = App\Models\AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    echo "❌ No active academic year!\n";
    exit(1);
}

echo "✅ Active Year: {$activeYear->name} (ID: {$activeYear->id})\n\n";

// Get existing assignments that should be displayed
$enrollments = App\Models\StudentEnrollment::with([
    'student',
    'strand',
    'academicYearStrandSection.section'
])
->where('academic_year_id', $activeYear->id)
->whereNotNull('academic_year_strand_section_id')
->get();

echo "📊 Found {$enrollments->count()} enrolled students with section assignments:\n\n";

foreach ($enrollments as $enrollment) {
    if ($enrollment->student && $enrollment->strand && $enrollment->academicYearStrandSection && $enrollment->academicYearStrandSection->section) {
        $student = $enrollment->student;
        $section = $enrollment->academicYearStrandSection->section;
        $strand = $enrollment->strand;
        
        echo "✅ {$student->first_name} {$student->last_name}\n";
        echo "   Student ID: {$student->id}\n";
        echo "   Section: {$section->name} ({$section->grade})\n";
        echo "   Strand: {$strand->code}\n";
        echo "   Enrollment ID: {$enrollment->id}\n\n";
    }
}

// Build the assignment array that will be passed to the view
$existingAssignments = [];
foreach ($enrollments as $enrollment) {
    if ($enrollment->student && $enrollment->strand && $enrollment->academicYearStrandSection && $enrollment->academicYearStrandSection->section) {
        $existingAssignments[] = [
            'student_id' => $enrollment->student_id,
            'strand_code' => $enrollment->strand->code,
            'section_id' => $enrollment->academicYearStrandSection->section_id,
            'section_name' => $enrollment->academicYearStrandSection->section->name,
            'section_grade' => $enrollment->academicYearStrandSection->section->grade,
        ];
    }
}

echo "📋 Assignment Data for View:\n";
echo json_encode($existingAssignments, JSON_PRETTY_PRINT);
echo "\n\n";

echo "🎉 This data will now be loaded by the Assigning List page!\n";
echo "Refresh your browser to see the changes.\n";
