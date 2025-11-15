<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Creating test co-admin user...\n";

DB::table('users')->insert([
    'name' => 'Test Co-Admin',
    'email' => 'coadmin@test.com',
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
    'type' => 'co-admin',
    'user_pk_id' => null,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✓ Co-Admin user created successfully!\n";
echo "Email: coadmin@test.com\n";
echo "Password: password\n";
echo "\nYou can now login at: /admin/login\n";
