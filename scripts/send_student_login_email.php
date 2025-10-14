<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Http\Controllers\Admin\StudentController;

$studentNumber = $argv[1] ?? null;
if (!$studentNumber) {
    fwrite(STDERR, "Usage: php scripts/send_student_login_email.php <student_number>\n");
    exit(1);
}

$student = Student::where('student_number', $studentNumber)->first();
if (!$student) {
    fwrite(STDERR, "Student not found: {$studentNumber}\n");
    exit(2);
}

$controller = app()->make(StudentController::class);
$controller->generatePassword($student);

$student->refresh();
echo "Email triggered for student {$student->student_number} ({$student->email}).\n";
echo "Encrypted password field is now: " . (empty($student->generated_password_encrypted) ? 'MISSING' : 'SET') . "\n";
