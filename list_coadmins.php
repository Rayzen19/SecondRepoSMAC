<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== Co-Admin Users in Database ===\n\n";

$coAdmins = DB::table('users')
    ->where('type', 'co-admin')
    ->select('id', 'name', 'email', 'type', 'created_at')
    ->get();

if ($coAdmins->isEmpty()) {
    echo "No co-admin users found.\n";
} else {
    foreach ($coAdmins as $user) {
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Type: {$user->type}\n";
        echo "Created: {$user->created_at}\n";
        echo "---\n";
    }
    echo "\nTotal co-admin users: " . $coAdmins->count() . "\n";
}
