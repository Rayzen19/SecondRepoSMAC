<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Enrollment 547 ===\n\n";

$enrollment = \App\Models\StudentEnrollment::find(547);

if ($enrollment) {
    echo "✅ Enrollment 547 EXISTS\n";
    echo "Student ID: {$enrollment->student_id}\n";
    echo "Academic Year ID: {$enrollment->academic_year_id}\n";
    echo "Status: {$enrollment->status}\n";
    
    $student = \App\Models\Student::find($enrollment->student_id);
    echo "Student: {$student->first_name} {$student->last_name} ({$student->student_number})\n";
} else {
    echo "❌ Enrollment 547 DOES NOT EXIST\n";
    echo "It may have been deleted.\n";
}

echo "\n=== Checking all enrollments for student 65 ===\n";
$enrollments = \App\Models\StudentEnrollment::where('student_id', 65)->get();
echo "Total: {$enrollments->count()}\n";

foreach ($enrollments as $e) {
    echo "  - ID: {$e->id}, Year: {$e->academic_year_id}, Status: {$e->status}\n";
}

echo "\n=== Done ===\n";
