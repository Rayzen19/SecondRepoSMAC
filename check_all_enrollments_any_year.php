<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== All Enrollments ===\n\n";

$enrollments = \App\Models\StudentEnrollment::with(['student', 'academicYear'])
    ->orderBy('id', 'desc')
    ->take(20)
    ->get();

echo "Total: " . \App\Models\StudentEnrollment::count() . "\n";
echo "Showing last 20:\n\n";

foreach ($enrollments as $e) {
    $yearName = $e->academicYear ? $e->academicYear->name : 'No year';
    $studentName = $e->student ? "{$e->student->student_number}: {$e->student->first_name} {$e->student->last_name}" : "Student ID: {$e->student_id}";
    
    echo "Enrollment #{$e->id}:\n";
    echo "  Student: {$studentName}\n";
    echo "  Year: {$yearName} (ID: {$e->academic_year_id})\n";
    echo "  AYSS ID: " . ($e->academic_year_strand_section_id ?? 'NULL') . "\n";
    echo "  Reg #: {$e->registration_number}\n\n";
}
