<?php

/**
 * Create teacher account for Jessie Molecass
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
echo "Create Teacher Account\n";
echo "=================================\n\n";

try {
    DB::beginTransaction();
    
    // Teacher data for Jessie Molecass
    $employeeNumber = 'T-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $firstName = 'Jessie';
    $middleName = '';
    $lastName = 'Molecass';
    $gender = 'male'; // Adjust if needed
    $email = 'jessie.molecass@smac.edu';
    $phone = '09' . rand(100000000, 999999999);
    $department = 'Senior High School';
    $specialization = 'General Education';
    $term = 'permanent';
    $status = 'active';
    $password = 'Jessie@2024'; // Secure password
    
    // Check if teacher already exists by name
    $existingTeacher = Teacher::where('first_name', $firstName)
                               ->where('last_name', $lastName)
                               ->first();
    if ($existingTeacher) {
        echo "✓ Teacher already exists (ID: {$existingTeacher->id})\n";
        echo "  Employee Number: {$existingTeacher->employee_number}\n";
        echo "  Name: {$existingTeacher->first_name} {$existingTeacher->last_name}\n";
        echo "  Email: {$existingTeacher->email}\n\n";
        
        // Check user account
        $existingUser = User::where('email', $existingTeacher->email)->where('type', 'teacher')->first();
        if ($existingUser) {
            echo "✓ User account already exists (ID: {$existingUser->id})\n";
        }
        
        DB::rollBack();
        echo "\nTeacher account already exists in the system.\n";
        echo "Login URL: http://127.0.0.1:8000/teacher/login\n";
        exit(0);
    }
    
    // Check if email is already used
    $existingEmail = User::where('email', $email)->first();
    if ($existingEmail) {
        // Generate alternative email
        $email = 'j.molecass@smac.edu';
        $existingEmail = User::where('email', $email)->first();
        if ($existingEmail) {
            $email = 'jessie.molecass' . rand(100, 999) . '@smac.edu';
        }
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
        'address' => 'Address on file',
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
    echo "Teacher Account Created!\n";
    echo "=================================\n";
    echo "Employee Number: {$teacher->employee_number}\n";
    echo "Name: {$teacher->first_name} " . ($teacher->middle_name ? $teacher->middle_name . ' ' : '') . "{$teacher->last_name}\n";
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
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
