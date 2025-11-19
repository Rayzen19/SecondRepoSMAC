<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Diagnosing Email Uniqueness Issue\n";
echo "==================================\n\n";

$email = 'johnraymond.barrogo@cvsu.edu.ph';
$teacherId = 1028; // Employee ID from the image

echo "Looking for email: {$email}\n";
echo "Teacher ID: {$teacherId}\n\n";

// Find teacher by employee number
$teacher = App\Models\Teacher::where('employee_number', '20211028')->first();

if (!$teacher) {
    echo "❌ Teacher not found by employee number!\n";
    echo "Searching by email...\n";
    $teacher = App\Models\Teacher::where('email', $email)->first();
}

if ($teacher) {
    echo "✅ Found Teacher:\n";
    echo "   ID: {$teacher->id}\n";
    echo "   Employee #: {$teacher->employee_number}\n";
    echo "   Name: {$teacher->first_name} {$teacher->last_name}\n";
    echo "   Email: {$teacher->email}\n\n";
} else {
    echo "❌ Teacher not found!\n\n";
}

// Check for duplicate teachers with same email
echo "Checking for duplicate TEACHERS with email '{$email}':\n";
$teachersWithEmail = App\Models\Teacher::where('email', $email)->get();
echo "   Found: {$teachersWithEmail->count()} teacher(s)\n";
foreach ($teachersWithEmail as $t) {
    echo "   - ID: {$t->id}, Name: {$t->first_name} {$t->last_name}, Deleted: " . ($t->deleted_at ? 'YES' : 'NO') . "\n";
}
echo "\n";

// Check for duplicate users with same email
echo "Checking for duplicate USERS with email '{$email}':\n";
$usersWithEmail = App\Models\User::where('email', $email)->get();
echo "   Found: {$usersWithEmail->count()} user(s)\n";
foreach ($usersWithEmail as $u) {
    echo "   - ID: {$u->id}, Type: {$u->type}, PK ID: {$u->user_pk_id}, Name: {$u->name}\n";
}
echo "\n";

// Check soft-deleted teachers
echo "Checking for SOFT-DELETED teachers with email '{$email}':\n";
$deletedTeachers = App\Models\Teacher::onlyTrashed()->where('email', $email)->get();
echo "   Found: {$deletedTeachers->count()} soft-deleted teacher(s)\n";
foreach ($deletedTeachers as $t) {
    echo "   - ID: {$t->id}, Name: {$t->first_name} {$t->last_name}, Deleted at: {$t->deleted_at}\n";
}
echo "\n";

if ($teacher) {
    // Check linked user
    echo "Checking for linked auth USER:\n";
    $linkedUser = App\Models\User::where('type', 'teacher')->where('user_pk_id', $teacher->id)->first();
    if ($linkedUser) {
        echo "   ✅ Found linked user:\n";
        echo "      User ID: {$linkedUser->id}\n";
        echo "      Email: {$linkedUser->email}\n";
        echo "      Name: {$linkedUser->name}\n";
    } else {
        echo "   ℹ️  No linked auth user found\n";
    }
    echo "\n";
}

// Test the validation rules
if ($teacher) {
    echo "Testing validation with current data:\n";
    echo "=====================================\n";
    
    $linkedUser = App\Models\User::where('type', 'teacher')->where('user_pk_id', $teacher->id)->first();
    
    echo "Current teacher ID: {$teacher->id}\n";
    echo "Linked user ID: " . ($linkedUser ? $linkedUser->id : 'NULL') . "\n\n";
    
    echo "Validation will ignore:\n";
    echo "   - Teachers table: ID {$teacher->id}\n";
    echo "   - Users table: ID " . (optional($linkedUser)->id ?? 'NULL') . "\n\n";
    
    // Check if the email would pass validation
    $validator = Illuminate\Support\Facades\Validator::make(
        ['email' => $email],
        [
            'email' => [
                'required',
                'email',
                Illuminate\Validation\Rule::unique('teachers', 'email')->ignore($teacher->id),
                Illuminate\Validation\Rule::unique('users', 'email')->ignore(optional($linkedUser)->id),
            ]
        ]
    );
    
    if ($validator->fails()) {
        echo "❌ VALIDATION FAILED:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - {$error}\n";
        }
    } else {
        echo "✅ VALIDATION PASSED - Email should be accepted\n";
    }
}

echo "\n";
echo "🔍 Diagnosis complete!\n";
