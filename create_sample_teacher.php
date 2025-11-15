<?php

/**
 * Create a sample teacher account
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=================================\n";
echo "Create Sample Teacher Account\n";
echo "=================================\n\n";

try {
    DB::beginTransaction();
    
    // Sample teacher data
    $employeeNumber = 'T-2024-001';
    $firstName = 'Maria';
    $middleName = 'Santos';
    $lastName = 'Cruz';
    $gender = 'female';
    $email = 'maria.cruz@smac.edu';
    $phone = '09123456789';
    $department = 'Senior High School';
    $specialization = 'Mathematics';
    $term = 'permanent';
    $status = 'active';
    $password = 'teacher123';
    
    // Check if teacher already exists
    $existingTeacher = Teacher::where('employee_number', $employeeNumber)->first();
    if ($existingTeacher) {
        echo "✓ Teacher already exists (ID: {$existingTeacher->id})\n";
        echo "  Employee Number: {$existingTeacher->employee_number}\n";
        echo "  Name: {$existingTeacher->first_name} {$existingTeacher->last_name}\n";
        echo "  Email: {$existingTeacher->email}\n\n";
        
        // Check user account
        $existingUser = User::where('email', $email)->where('type', 'teacher')->first();
        if ($existingUser) {
            echo "✓ User account already exists (ID: {$existingUser->id})\n";
        }
        
        DB::rollBack();
        echo "\nTeacher account already exists in the system.\n";
        echo "Login URL: http://127.0.0.1:8000/teacher/login\n";
        exit(0);
    }
    
    // Create teacher record
    $teacher = Teacher::create([
        'employee_number' => $employeeNumber,
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'suffix' => null,
        'gender' => $gender,
        'email' => $email,
        'phone' => $phone,
        'address' => 'Sample Address, City',
        'department' => $department,
        'specialization' => $specialization,
        'term' => $term,
        'status' => $status,
    ]);
    
    echo "✓ Teacher record created (ID: {$teacher->id})\n";
    
    // Create user account for login
    $user = User::create([
        'name' => $firstName . ' ' . $lastName,
        'email' => $email,
        'password' => Hash::make($password),
        'type' => 'teacher',
        'user_pk_id' => $teacher->id,
        'email_verified_at' => now(),
    ]);
    
    echo "✓ User account created (ID: {$user->id})\n";
    
    DB::commit();
    
    echo "\n=================================\n";
    echo "Sample Teacher Account Created!\n";
    echo "=================================\n";
    echo "Employee Number: {$teacher->employee_number}\n";
    echo "Name: {$teacher->first_name} {$teacher->middle_name} {$teacher->last_name}\n";
    echo "Gender: {$teacher->gender}\n";
    echo "Email: {$teacher->email}\n";
    echo "Phone: {$teacher->phone}\n";
    echo "Department: {$teacher->department}\n";
    echo "Specialization: {$teacher->specialization}\n";
    echo "Term: {$teacher->term}\n";
    echo "Status: {$teacher->status}\n";
    echo "\n=== Login Credentials ===\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";
    echo "Login URL: http://127.0.0.1:8000/teacher/login\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error creating teacher account: " . $e->getMessage() . "\n";
    exit(1);
}
