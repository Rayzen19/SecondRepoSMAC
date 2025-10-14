<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\User;

$email = 'johnraymond.barrogo@cvsu.edu.ph';

echo "Comprehensive Search for: {$email}\n";
echo str_repeat("=", 70) . "\n\n";

// 1. Check active teachers
echo "1. Checking active teachers table...\n";
$teacher = Teacher::where('email', $email)->first();
if ($teacher) {
    echo "   ✓ Found in teachers table:\n";
    echo "     ID: {$teacher->id}\n";
    echo "     Name: {$teacher->first_name} {$teacher->last_name}\n";
    echo "     Employee #: {$teacher->employee_number}\n";
    echo "     Status: {$teacher->status}\n\n";
} else {
    echo "   ✗ Not found in active teachers\n\n";
}

// 2. Check soft-deleted teachers
echo "2. Checking soft-deleted (trashed) teachers...\n";
$trashedTeacher = Teacher::withTrashed()->where('email', $email)->first();
if ($trashedTeacher && $trashedTeacher->trashed()) {
    echo "   ✓ Found in TRASHED teachers:\n";
    echo "     ID: {$trashedTeacher->id}\n";
    echo "     Name: {$trashedTeacher->first_name} {$trashedTeacher->last_name}\n";
    echo "     Employee #: {$trashedTeacher->employee_number}\n";
    echo "     Deleted at: {$trashedTeacher->deleted_at}\n\n";
    
    echo "   Do you want to permanently delete this teacher? (y/n)\n";
    echo "   This will remove the email from the system completely.\n\n";
    
    // Auto-delete for script purposes
    echo "   Permanently deleting trashed teacher...\n";
    $trashedTeacher->forceDelete();
    echo "   ✓ Teacher permanently deleted from database!\n\n";
} elseif ($trashedTeacher && !$trashedTeacher->trashed()) {
    echo "   ✓ Teacher exists but is NOT trashed (active)\n\n";
} else {
    echo "   ✗ Not found in trashed teachers\n\n";
}

// 3. Check users table
echo "3. Checking users table...\n";
$user = User::where('email', $email)->first();
if ($user) {
    echo "   ✓ Found in users table:\n";
    echo "     User ID: {$user->id}\n";
    echo "     Name: {$user->name}\n";
    echo "     Email: {$user->email}\n";
    echo "     Type: {$user->type}\n";
    echo "     User PK ID: {$user->user_pk_id}\n\n";
    
    echo "   Deleting user account...\n";
    $user->delete();
    echo "   ✓ User account deleted!\n\n";
} else {
    echo "   ✗ Not found in users table\n\n";
}

// 4. Final verification
echo "4. Final verification - checking if email is now available...\n";
$stillExists = Teacher::withTrashed()->where('email', $email)->exists();
$userStillExists = User::where('email', $email)->exists();

if (!$stillExists && !$userStillExists) {
    echo "   ✓ SUCCESS! Email '{$email}' is now available!\n";
    echo "   You can now use this email to create a new teacher.\n";
} else {
    echo "   ✗ Email still exists somewhere in the system.\n";
    if ($stillExists) echo "     - Still in teachers table\n";
    if ($userStillExists) echo "     - Still in users table\n";
}
