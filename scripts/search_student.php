<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

echo "Searching for students with 'barrogo' in email or name:\n\n";

$students = Student::where('email', 'LIKE', '%barrogo%')
    ->orWhere('first_name', 'LIKE', '%barrogo%')
    ->orWhere('last_name', 'LIKE', '%barrogo%')
    ->get();

if ($students->count() > 0) {
    echo "Found {$students->count()} student(s):\n";
    foreach ($students as $student) {
        echo "  ID: {$student->id}\n";
        echo "  Name: {$student->first_name} {$student->last_name}\n";
        echo "  Student Number: {$student->student_number}\n";
        echo "  Email: {$student->email}\n";
        echo "  ---\n";
    }
} else {
    echo "No students found.\n";
}

echo "\nSearching for students with 'johnraymond' or 'john raymond' in name:\n\n";

$students = Student::where('first_name', 'LIKE', '%john%')
    ->where('first_name', 'LIKE', '%raymond%')
    ->orWhere('first_name', 'LIKE', '%johnraymond%')
    ->get();

if ($students->count() > 0) {
    echo "Found {$students->count()} student(s):\n";
    foreach ($students as $student) {
        echo "  ID: {$student->id}\n";
        echo "  Name: {$student->first_name} {$student->last_name}\n";
        echo "  Student Number: {$student->student_number}\n";
        echo "  Email: {$student->email}\n";
        echo "  ---\n";
    }
} else {
    echo "No students found.\n";
}
