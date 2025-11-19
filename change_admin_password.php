<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Auth\AdminUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// Check if password is provided as argument
if ($argc < 2) {
    echo "Usage: php change_admin_password.php <new_password> [email]\n";
    echo "Example: php change_admin_password.php 'NewP@ssw0rd123'\n";
    echo "Example with email: php change_admin_password.php 'NewP@ssw0rd123' admin@school.test\n";
    exit(1);
}

$newPassword = $argv[1];
$email = $argv[2] ?? null;

// Validate password requirements
$validator = Validator::make(['password' => $newPassword], [
    'password' => [
        'required',
        'string',
        'min:8',
        'regex:/[a-z]/',      // must contain at least one lowercase letter
        'regex:/[A-Z]/',      // must contain at least one uppercase letter
        'regex:/[0-9]/',      // must contain at least one digit
        'regex:/[@$!%*#?&]/', // must contain at least one special character
    ]
]);

if ($validator->fails()) {
    echo "Password does not meet requirements:\n";
    echo "- At least 8 characters long\n";
    echo "- Contains both uppercase and lowercase letters\n";
    echo "- Contains at least one number\n";
    echo "- Contains at least one special character (@\$!%*#?&)\n\n";
    foreach ($validator->errors()->all() as $error) {
        echo "✗ {$error}\n";
    }
    exit(1);
}

// Get admin user by email or first admin
if ($email) {
    $admin = AdminUser::where('email', $email)->first();
    if (!$admin) {
        echo "No admin user found with email: {$email}\n";
        exit(1);
    }
} else {
    $admin = AdminUser::first();
    if (!$admin) {
        echo "No admin user found!\n";
        exit(1);
    }
}

// Update password
$admin->password = Hash::make($newPassword);
$admin->save();

echo "✓ Admin password updated successfully!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Email: {$admin->email}\n";
echo "Name: {$admin->name}\n";
echo "New Password: {$newPassword}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
