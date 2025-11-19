<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Finding Student by Number Pattern ===\n\n";

// Search for students with similar number
$students = \App\Models\Student::where('student_number', 'LIKE', '%2025%00027%')
    ->orWhere('student_number', 'LIKE', '%00027%')
    ->get();

echo "Found {$students->count()} students matching pattern:\n\n";

foreach ($students as $student) {
    echo "Student Number: {$student->student_number}\n";
    echo "Name: {$student->first_name} {$student->last_name}\n";
    echo "ID: {$student->id}\n\n";
}

// Also check the first few students
echo "\n=== First 5 students in database ===\n\n";
$firstStudents = \App\Models\Student::take(5)->get();
foreach ($firstStudents as $student) {
    echo "Student Number: {$student->student_number}\n";
    echo "Name: {$student->first_name} {$student->last_name}\n";
    echo "ID: {$student->id}\n\n";
}

echo "\n=== Done ===\n";
