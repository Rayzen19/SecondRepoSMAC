<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;
use App\Http\Controllers\Admin\StudentController;

$student = Student::first();
if (!$student) {
    echo "NOSTUDENT\n";
    exit(0);
}

$user = User::where('type', 'student')->where('email', $student->email)->first();
$oldHash = $user ? $user->password : null;
$oldEnc = $student->generated_password_encrypted;

echo "Before: user_hash=" . ($oldHash ? substr($oldHash, 0, 20) . '...' : 'null') . " enc=" . ($oldEnc ? 'SET' : 'NULL') . "\n";

$controller = app()->make(StudentController::class);
$controller->generatePassword($student);

$student->refresh();
$user = User::where('type', 'student')->where('email', $student->email)->first();
$newHash = $user ? $user->password : null;
$newEnc = $student->generated_password_encrypted;

echo "After:  user_hash=" . ($newHash ? substr($newHash, 0, 20) . '...' : 'null') . " enc=" . ($newEnc ? 'SET' : 'NULL') . "\n";

echo (($oldHash !== $newHash) ? "RESULT: USER_HASH_CHANGED\n" : "RESULT: USER_HASH_SAME\n");
echo ($newEnc ? "RESULT: ENCRYPTED_SET\n" : "RESULT: ENCRYPTED_MISSING\n");
