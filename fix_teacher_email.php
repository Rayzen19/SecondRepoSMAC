<?php

/**
 * Fix Teacher Email Issue
 * 
 * Problem: John Raymond Barrogo (Teacher) has inconsistent email addresses
 * - Teacher record: johnraymond.barrogo@cvsu.edu.ph
 * - User login: johnraymondbarrogo08@gmail.com
 * 
 * Solution: Update teacher email to match the user login email for consistency
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Teacher Email Fix Script ===\n\n";

// Find the teacher record
$teacher = DB::table('teachers')
    ->where('first_name', 'John Raymond')
    ->where('last_name', 'Barrogo')
    ->first();

if (!$teacher) {
    echo "ERROR: Teacher not found!\n";
    exit(1);
}

echo "Found Teacher:\n";
echo "  ID: {$teacher->id}\n";
echo "  Name: {$teacher->first_name} {$teacher->last_name}\n";
echo "  Current Email: {$teacher->email}\n\n";

// Find user accounts
$users = DB::table('users')
    ->where('email', 'LIKE', '%barrogo%')
    ->get();

echo "Found Users:\n";
foreach ($users as $user) {
    echo "  ID: {$user->id} | Type: {$user->type} | Email: {$user->email}\n";
}
echo "\n";

// Ask which email to use
echo "Which email should the TEACHER use?\n";
echo "1. johnraymond.barrogo@cvsu.edu.ph (CVSU official)\n";
echo "2. johnraymondbarrogo08@gmail.com (Gmail)\n";
echo "Enter choice (1 or 2): ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

$newEmail = '';
if ($line === '1') {
    $newEmail = 'johnraymond.barrogo@cvsu.edu.ph';
} elseif ($line === '2') {
    $newEmail = 'johnraymondbarrogo08@gmail.com';
} else {
    echo "Invalid choice. Exiting.\n";
    exit(1);
}

echo "\n=== Applying Fix ===\n";
echo "New teacher email: {$newEmail}\n\n";

try {
    DB::beginTransaction();
    
    // Update teacher record
    DB::table('teachers')
        ->where('id', $teacher->id)
        ->update(['email' => $newEmail]);
    
    echo "✓ Updated teacher record email\n";
    
    // Update or create user account for teacher
    $teacherUser = DB::table('users')
        ->where('type', 'teacher')
        ->where('user_pk_id', $teacher->id)
        ->first();
    
    if ($teacherUser) {
        DB::table('users')
            ->where('id', $teacherUser->id)
            ->update(['email' => $newEmail]);
        echo "✓ Updated teacher user account email\n";
    } else {
        echo "✗ No teacher user account found (this may need manual creation)\n";
    }
    
    // Check for duplicate emails
    $duplicates = DB::table('users')
        ->where('email', $newEmail)
        ->count();
    
    if ($duplicates > 1) {
        echo "\n⚠ WARNING: Multiple users with email {$newEmail} found!\n";
        echo "Please review user accounts manually.\n";
    }
    
    DB::commit();
    
    echo "\n=== Fix Complete ===\n";
    echo "Teacher email has been updated to: {$newEmail}\n";
    echo "\nPlease verify:\n";
    echo "1. Teacher can log in with the updated email\n";
    echo "2. Messages show the correct email address\n";
    echo "3. No duplicate user accounts exist\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\nERROR: " . $e->getMessage() . "\n";
    exit(1);
}
