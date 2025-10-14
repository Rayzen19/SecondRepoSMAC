<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\User;

$email = 'johnraymond.barrogo@cvsu.edu.ph';

echo "Searching for teacher with email: {$email}\n\n";

$teacher = Teacher::where('email', $email)->first();

if ($teacher) {
    echo "Found teacher:\n";
    echo "  ID: {$teacher->id}\n";
    echo "  Name: {$teacher->first_name} {$teacher->last_name}\n";
    echo "  Employee Number: {$teacher->employee_number}\n";
    echo "  Email: {$teacher->email}\n";
    echo "  Department: {$teacher->department}\n\n";
    
    // Check if there's a linked user account
    $user = User::where('type', 'teacher')->where('user_pk_id', $teacher->id)->first();
    if ($user) {
        echo "Found linked user account:\n";
        echo "  User ID: {$user->id}\n";
        echo "  Email: {$user->email}\n\n";
        echo "Deleting user account...\n";
        $user->delete();
        echo "✓ User account deleted.\n\n";
    } else {
        echo "No linked user account found.\n\n";
    }
    
    echo "Deleting teacher record...\n";
    $teacher->delete();
    echo "✓ Teacher deleted successfully.\n";
} else {
    echo "✗ Teacher not found.\n";
}
