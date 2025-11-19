<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating Admin Account ===\n\n";

// Check if admin already exists
$adminEmail = 'admin@smac.edu';
$existingAdmin = App\Models\User::where('email', $adminEmail)->first();

if ($existingAdmin) {
    echo "Admin account already exists. Updating password...\n";
    $existingAdmin->password = bcrypt('admin123');
    $existingAdmin->name = 'System Administrator';
    $existingAdmin->type = 'admin';
    $existingAdmin->save();
    echo "✅ Admin account updated successfully!\n";
} else {
    // Create new admin account
    $admin = new App\Models\User();
    $admin->name = 'System Administrator';
    $admin->email = $adminEmail;
    $admin->password = bcrypt('admin123');
    $admin->type = 'admin';
    $admin->email_verified_at = now();
    $admin->save();
    echo "✅ Admin account created successfully!\n";
}

echo "\n=== Admin Login Credentials ===\n";
echo "URL: http://127.0.0.1:8000/admin/login\n";
echo "Email: {$adminEmail}\n";
echo "Password: admin123\n";
echo "\n⚠️  Please change this password after first login!\n";
