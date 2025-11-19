<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Existing Enrollments ===\n\n";

$enrollments = \App\Models\StudentEnrollment::with('student')->where('academic_year_id', 5)->get();

echo "Total enrollments for year ID 5: " . $enrollments->count() . "\n\n";

foreach ($enrollments as $enrollment) {
    $studentName = $enrollment->student ? "{$enrollment->student->student_number}: {$enrollment->student->first_name} {$enrollment->student->last_name}" : "Student {$enrollment->student_id}";
    echo "Enrollment #{$enrollment->id}:\n";
    echo "  Student: {$studentName}\n";
    echo "  AYSS ID: " . ($enrollment->academic_year_strand_section_id ?? 'NULL') . "\n";
    echo "  Strand ID: " . ($enrollment->strand_id ?? 'NULL') . "\n";
    echo "  Reg#: {$enrollment->registration_number}\n\n";
}

echo "\n✅ Now let's UPDATE these to assign them to JUDE section!\n";

$abmStrand = \App\Models\Strand::where('code', 'ABM')->first();
$judeSection = \App\Models\Section::find(1);
$activeYear = \App\Models\AcademicYear::find(5);

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

$updated = 0;
foreach ($enrollments as $enrollment) {
    if ($enrollment->student && $enrollment->student->program === 'ABM') {
        $enrollment->update([
            'strand_id' => $abmStrand->id,
            'academic_year_strand_section_id' => $ayss->id,
            'status' => 'enrolled'
        ]);
        echo "✅ Updated: {$enrollment->student->student_number}\n";
        $updated++;
    }
}

echo "\n✅ Updated {$updated} ABM students to JUDE section!\n";
echo "Now refresh the Section & Advisers page to see them!\n";
