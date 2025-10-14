<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

$email = 'johnraymond.barrogo@cvsu.edu.ph';

echo "Searching for student with email: {$email}\n";

$student = Student::where('email', $email)->first();

if ($student) {
    echo "Found student:\n";
    echo "  ID: {$student->id}\n";
    echo "  Name: {$student->first_name} {$student->last_name}\n";
    echo "  Student Number: {$student->student_number}\n";
    echo "  Email: {$student->email}\n\n";
    
    $student->delete();
    echo "✓ Student deleted successfully.\n";
} else {
    echo "✗ Student not found.\n";
}
