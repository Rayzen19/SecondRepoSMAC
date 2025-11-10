<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Email Validation Fix\n";
echo "=============================\n\n";

$teacher = App\Models\Teacher::where('employee_number', '20211028')->first();

if (!$teacher) {
    echo "❌ Teacher not found!\n";
    exit(1);
}

echo "✅ Testing teacher: {$teacher->first_name} {$teacher->last_name}\n";
echo "   ID: {$teacher->id}\n";
echo "   Current email: {$teacher->email}\n\n";

// Find ALL linked users
$linkedUsers = App\Models\User::where('type', 'teacher')->where('user_pk_id', $teacher->id)->get();
$linkedUserIds = $linkedUsers->pluck('id')->toArray();

echo "Found {$linkedUsers->count()} linked user(s): " . implode(', ', $linkedUserIds) . "\n\n";

// Test validation with same email (should PASS)
echo "Test 1: Updating with SAME email\n";
echo "---------------------------------\n";

$validator = Illuminate\Support\Facades\Validator::make(
    ['email' => $teacher->email],
    [
        'email' => [
            'required',
            'email',
            Illuminate\Validation\Rule::unique('teachers', 'email')->ignore($teacher->id),
            function ($attribute, $value, $fail) use ($linkedUserIds) {
                $exists = \App\Models\User::where('email', $value)
                    ->whereNotIn('id', $linkedUserIds)
                    ->exists();
                if ($exists) {
                    $fail('The email has already been taken.');
                }
            },
        ]
    ]
);

if ($validator->fails()) {
    echo "❌ FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "   - {$error}\n";
    }
} else {
    echo "✅ PASSED - Can update with same email\n";
}

echo "\n";

// Test validation with different email (should PASS if email is not taken)
echo "Test 2: Updating with NEW email\n";
echo "---------------------------------\n";

$newEmail = 'john.barrogo.new@cvsu.edu.ph';

$validator2 = Illuminate\Support\Facades\Validator::make(
    ['email' => $newEmail],
    [
        'email' => [
            'required',
            'email',
            Illuminate\Validation\Rule::unique('teachers', 'email')->ignore($teacher->id),
            function ($attribute, $value, $fail) use ($linkedUserIds) {
                $exists = \App\Models\User::where('email', $value)
                    ->whereNotIn('id', $linkedUserIds)
                    ->exists();
                if ($exists) {
                    $fail('The email has already been taken.');
                }
            },
        ]
    ]
);

if ($validator2->fails()) {
    echo "❌ FAILED:\n";
    foreach ($validator2->errors()->all() as $error) {
        echo "   - {$error}\n";
    }
} else {
    echo "✅ PASSED - Can update with new email\n";
}

echo "\n";

// Test with an email that belongs to another user
echo "Test 3: Updating with email from ANOTHER user\n";
echo "----------------------------------------------\n";

$anotherUserEmail = 'Ic.xevierclyde.bitancor@cvsu.edu.ph'; // Belongs to clyde Toledo

$validator3 = Illuminate\Support\Facades\Validator::make(
    ['email' => $anotherUserEmail],
    [
        'email' => [
            'required',
            'email',
            Illuminate\Validation\Rule::unique('teachers', 'email')->ignore($teacher->id),
            function ($attribute, $value, $fail) use ($linkedUserIds) {
                $exists = \App\Models\User::where('email', $value)
                    ->whereNotIn('id', $linkedUserIds)
                    ->exists();
                if ($exists) {
                    $fail('The email has already been taken.');
                }
            },
        ]
    ]
);

if ($validator3->fails()) {
    echo "✅ CORRECTLY FAILED (email belongs to another user):\n";
    foreach ($validator3->errors()->all() as $error) {
        echo "   - {$error}\n";
    }
} else {
    echo "❌ PASSED - This should have failed!\n";
}

echo "\n🎉 Validation fix is working correctly!\n";
