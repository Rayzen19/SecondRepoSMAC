<?php

/**
 * Script to create a Co-Admin user account
 * 
 * Usage: php create_co_admin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "\n=== Create Co-Admin Account ===\n\n";

// Get user input
echo "Enter co-admin name: ";
$name = trim(fgets(STDIN));

echo "Enter co-admin email: ";
$email = trim(fgets(STDIN));

echo "Enter co-admin password: ";
$password = trim(fgets(STDIN));

if (empty($name) || empty($email) || empty($password)) {
    echo "\nError: All fields are required!\n";
    exit(1);
}

// Check if email already exists
$existingUser = DB::table('users')->where('email', $email)->first();
if ($existingUser) {
    echo "\nError: A user with this email already exists!\n";
    echo "Current user type: " . $existingUser->type . "\n";
    exit(1);
}

// Create the co-admin user
try {
    DB::table('users')->insert([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'email_verified_at' => now(),
        'type' => 'co-admin',
        'user_pk_id' => null, // Co-admin doesn't need a reference to another table
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "\n✓ Co-Admin account created successfully!\n";
    echo "Email: $email\n";
    echo "You can now login at the admin login page.\n\n";

} catch (Exception $e) {
    echo "\nError creating co-admin: " . $e->getMessage() . "\n";
    exit(1);
}
