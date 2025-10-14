<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use Illuminate\Support\Facades\Crypt;

$year = date('Y');
$students_this_year = Student::whereYear('created_at', $year)->count();
$studentNumber = $year . '-' . str_pad($students_this_year + 1, 5, '0', STR_PAD_LEFT);

$plainPassword = 'TestPwd@' . rand(1000, 9999);
$uniq = (string) time();

$student = Student::create([
    'student_number' => $studentNumber,
    'first_name' => 'Test',
    'middle_name' => 'T',
    'last_name' => 'Student',
    'suffix' => null,
    'gender' => 'male',
    'birthdate' => '2000-01-01',
    'email' => 'test.student+' . $uniq . '@example.test',
    'mobile_number' => '09' . rand(100000000, 999999999),
    'address' => 'Test Address',
    'guardian_name' => 'Parent Test',
    'guardian_contact' => '09' . rand(100000000, 999999999),
    'guardian_email' => 'parent+' . $uniq . '@example.test',
    'program' => 'Test Program',
    'academic_year' => '2025-2026',
    'academic_year_id' => 1,
    'status' => 'active',
    'generated_password_encrypted' => Crypt::encryptString($plainPassword),
]);

echo "Inserted student id={$student->id}\n";
