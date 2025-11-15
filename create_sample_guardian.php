<?php

/**
 * Create a sample guardian account
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

echo "=================================\n";
echo "Create Sample Guardian Account\n";
echo "=================================\n\n";

try {
    DB::beginTransaction();
    
    // Sample guardian data
    $guardianNumber = 'GRD-2024-001';
    $firstName = 'Juan';
    $middleName = 'Dela';
    $lastName = 'Cruz';
    $gender = 'male';
    $email = 'juan.delacruz@gmail.com';
    $mobileNumber = '09171234567';
    $address = 'Blk 10 Lot 5, Sample Street, Quezon City';
    $status = 'active';
    $password = 'guardian123';
    
    // Check if guardian already exists
    $existingGuardian = Guardian::where('guardian_number', $guardianNumber)->first();
    if ($existingGuardian) {
        echo "✓ Guardian already exists (ID: {$existingGuardian->id})\n";
        echo "  Guardian Number: {$existingGuardian->guardian_number}\n";
        echo "  Name: {$existingGuardian->first_name} {$existingGuardian->last_name}\n";
        echo "  Email: {$existingGuardian->email}\n\n";
        
        // Check user account
        $existingUser = User::where('email', $email)->where('type', 'guardian')->first();
        if ($existingUser) {
            echo "✓ User account already exists (ID: {$existingUser->id})\n";
        }
        
        DB::rollBack();
        echo "\nGuardian account already exists in the system.\n";
        echo "Login URL: http://127.0.0.1:8000/guardian/login\n";
        exit(0);
    }
    
    // Create guardian record
    $guardian = Guardian::create([
        'guardian_number' => $guardianNumber,
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'suffix' => null,
        'gender' => $gender,
        'email' => $email,
        'mobile_number' => $mobileNumber,
        'address' => $address,
        'status' => $status,
        'generated_password_encrypted' => Crypt::encryptString($password),
    ]);
    
    echo "✓ Guardian record created (ID: {$guardian->id})\n";
    
    // Create user account for login
    $user = User::create([
        'name' => $firstName . ' ' . $lastName,
        'email' => $email,
        'password' => Hash::make($password),
        'type' => 'guardian',
        'user_pk_id' => $guardian->id,
        'email_verified_at' => now(),
    ]);
    
    echo "✓ User account created (ID: {$user->id})\n";
    
    DB::commit();
    
    echo "\n=================================\n";
    echo "Sample Guardian Account Created!\n";
    echo "=================================\n";
    echo "Guardian Number: {$guardian->guardian_number}\n";
    echo "Name: {$guardian->first_name} {$guardian->middle_name} {$guardian->last_name}\n";
    echo "Gender: {$guardian->gender}\n";
    echo "Email: {$guardian->email}\n";
    echo "Mobile: {$guardian->mobile_number}\n";
    echo "Address: {$guardian->address}\n";
    echo "Status: {$guardian->status}\n";
    echo "\n=== Login Credentials ===\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";
    echo "Login URL: http://127.0.0.1:8000/guardian/login\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error creating guardian account: " . $e->getMessage() . "\n";
    exit(1);
}
