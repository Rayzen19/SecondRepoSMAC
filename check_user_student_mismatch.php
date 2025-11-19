<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking User ID 325 vs Student ID ===\n\n";

// Check if there's a User with ID 325
$user = \App\Models\User::find(325);
if ($user) {
    echo "✓ User ID 325 exists:\n";
    echo "  Email: {$user->email}\n";
    echo "  Name: {$user->name}\n";
    echo "  Role: {$user->role}\n\n";
}

// Check if there's a Student with ID 325
$student = \App\Models\Student::find(325);
if ($student) {
    echo "✓ Student ID 325 exists:\n";
    echo "  Number: {$student->student_number}\n";
    echo "  Name: {$student->first_name} {$student->last_name}\n";
} else {
    echo "❌ Student ID 325 does NOT exist\n\n";
}

// Find the actual student record for John Raymond
echo "=== Finding John Raymond Barrogo ===\n\n";
$johnRaymond = \App\Models\Student::where('student_number', '2025-00005')->first();
if ($johnRaymond) {
    echo "Student ID: {$johnRaymond->id}\n";
    echo "Student Number: {$johnRaymond->student_number}\n";
    echo "Email: {$johnRaymond->email}\n\n";
    
    // Check if there's a user account for this student
    $userAccount = \App\Models\User::where('email', $johnRaymond->email)
        ->where('role', 'student')
        ->first();
    
    if ($userAccount) {
        echo "User Account ID: {$userAccount->id}\n";
        echo "User Email: {$userAccount->email}\n\n";
        
        if ($userAccount->id != $johnRaymond->id) {
            echo "⚠️ MISMATCH DETECTED!\n";
            echo "User ID ({$userAccount->id}) does NOT match Student ID ({$johnRaymond->id})\n";
            echo "This is causing the authentication issue.\n\n";
            echo "SOLUTION: The user table ID should match the student table ID.\n";
        }
    } else {
        echo "❌ No user account found for this student's email\n";
    }
}

echo "\n=== Done ===\n";
