<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking Student and User Records:\n";
echo "========================================\n\n";

// Check first student
$studentNumber = 'STU-000041';
echo "Looking for student: $studentNumber\n\n";

$student = DB::table('students')->where('student_number', $studentNumber)->first();
if ($student) {
    echo "✅ Student found:\n";
    print_r($student);
    
    echo "\n\nLooking for user account with user_pk_id = {$student->id}\n";
    $user = DB::table('users')->where('user_pk_id', $student->id)->where('type', 'student')->first();
    
    if ($user) {
        echo "✅ User account found:\n";
        print_r($user);
    } else {
        echo "❌ No user account found\n";
        
        // Check if there's any user with this email
        $email = 'brixnathan.alejandro@student.newsmac.edu.ph';
        echo "\nChecking for user with email: $email\n";
        $userByEmail = DB::table('users')->where('email', $email)->first();
        if ($userByEmail) {
            echo "✅ Found user by email:\n";
            print_r($userByEmail);
        } else {
            echo "❌ No user found with that email either\n";
        }
    }
} else {
    echo "❌ Student not found\n";
}

echo "\n\nAll students with STU-000041 to STU-000060:\n";
echo "========================================\n";
for ($i = 41; $i <= 60; $i++) {
    $num = str_pad($i, 6, '0', STR_PAD_LEFT);
    $studentNum = "STU-$num";
    $student = DB::table('students')->where('student_number', $studentNum)->first();
    if ($student) {
        echo "✅ $studentNum - {$student->first_name} {$student->last_name} (ID: {$student->id})\n";
    } else {
        echo "❌ $studentNum - Not found\n";
    }
}
