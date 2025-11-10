<?php

/**
 * Script to create user accounts for existing guardians
 * Run this to give guardians login access to the system
 * 
 * Usage: php create_guardian_user_accounts.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Guardian;
use App\Models\Auth\GuardianUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

echo "Creating guardian user accounts...\n\n";

$guardians = Guardian::all();
$totalGuardians = $guardians->count();
$createdAccounts = 0;
$skippedAccounts = 0;
$errors = 0;

echo "Found {$totalGuardians} guardians\n\n";

foreach ($guardians as $index => $guardian) {
    echo "[" . ($index + 1) . "/{$totalGuardians}] Processing: {$guardian->name} ({$guardian->guardian_number})...\n";
    
    try {
        DB::beginTransaction();

        // Check if user account already exists
        $existingUser = DB::table('users')
            ->where('email', $guardian->email)
            ->where('type', 'guardian')
            ->first();

        if ($existingUser) {
            echo "  ⊘ Guardian user account already exists\n\n";
            $skippedAccounts++;
            DB::commit();
            continue;
        }

        // Generate password
        $plainPassword = Str::password(12, symbols: true);

        // Create guardian user account
        GuardianUser::query()->withoutGlobalScopes()->create([
            'name' => $guardian->name,
            'email' => $guardian->email,
            'password' => Hash::make($plainPassword),
            'type' => 'guardian',
            'user_pk_id' => $guardian->id,
        ]);

        // Store encrypted password on guardian profile
        $guardian->forceFill([
            'generated_password_encrypted' => Crypt::encryptString($plainPassword),
        ])->save();

        $createdAccounts++;
        echo "  ✓ Created guardian user account\n";
        echo "  ℹ Email: {$guardian->email}\n";
        echo "  ℹ Password: {$plainPassword}\n";
        echo "  ⚠ IMPORTANT: Save this password or send it to the guardian!\n\n";

        DB::commit();

    } catch (\Throwable $e) {
        DB::rollBack();
        $errors++;
        echo "  ✗ Error: {$e->getMessage()}\n\n";
    }
}

echo "\n==========================================\n";
echo "Guardian User Account Creation Complete!\n";
echo "==========================================\n";
echo "Total guardians: {$totalGuardians}\n";
echo "Accounts created: {$createdAccounts}\n";
echo "Already existed: {$skippedAccounts}\n";
echo "Errors: {$errors}\n";
echo "==========================================\n\n";

if ($createdAccounts > 0) {
    echo "⚠ IMPORTANT: The generated passwords are displayed above.\n";
    echo "   Please save them or send them to the guardians via email.\n";
    echo "   Guardians should change their passwords after first login.\n\n";
}
