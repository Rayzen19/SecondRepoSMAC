<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking all teacher users in the system\n";
echo "========================================\n\n";

$teacherUsers = App\Models\User::where('type', 'teacher')->get();

echo "Found {$teacherUsers->count()} teacher user(s):\n\n";

foreach ($teacherUsers as $user) {
    echo "User ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Type: {$user->type}\n";
    echo "  user_pk_id: {$user->user_pk_id}\n";
    
    // Check if linked teacher exists
    if ($user->user_pk_id) {
        $teacher = App\Models\Teacher::find($user->user_pk_id);
        if ($teacher) {
            echo "  ✅ Linked to teacher: {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->id})\n";
            echo "     Teacher email: {$teacher->email}\n";
            
            if ($user->email !== $teacher->email) {
                echo "     ⚠️  EMAIL MISMATCH!\n";
            }
        } else {
            echo "  ❌ Linked teacher not found!\n";
        }
    } else {
        echo "  ℹ️  No teacher link (user_pk_id is NULL)\n";
    }
    
    echo "\n";
}

// Now specifically check user ID 5
echo "Specifically checking User ID 5:\n";
echo "=================================\n";
$user5 = App\Models\User::find(5);
if ($user5) {
    echo "✅ Found User ID 5\n";
    echo "   Email: {$user5->email}\n";
    echo "   Type: {$user5->type}\n";
    echo "   user_pk_id: {$user5->user_pk_id}\n";
    echo "   Name: {$user5->name}\n";
}
