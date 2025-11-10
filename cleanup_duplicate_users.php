<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Cleaning Up Duplicate Teacher Users\n";
echo "====================================\n\n";

// Find all teachers
$teachers = App\Models\Teacher::all();

echo "Checking {$teachers->count()} teachers for duplicate users...\n\n";

$cleanedCount = 0;
$issueCount = 0;

foreach ($teachers as $teacher) {
    $users = App\Models\User::where('type', 'teacher')->where('user_pk_id', $teacher->id)->get();
    
    if ($users->count() > 1) {
        $issueCount++;
        echo "⚠️  Teacher: {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->id})\n";
        echo "   Email: {$teacher->email}\n";
        echo "   Has {$users->count()} user accounts:\n";
        
        foreach ($users as $user) {
            $match = ($user->email === $teacher->email) ? '✅' : '❌';
            echo "     {$match} User ID {$user->id}: {$user->email}\n";
        }
        
        // Find the correct user (matching email)
        $correctUser = $users->firstWhere('email', $teacher->email);
        
        if ($correctUser) {
            echo "   Keeping User ID {$correctUser->id} (matches teacher email)\n";
            
            // Delete duplicates
            $deleted = 0;
            foreach ($users as $user) {
                if ($user->id !== $correctUser->id) {
                    echo "   🗑️  Deleting duplicate User ID {$user->id} ({$user->email})\n";
                    $user->delete();
                    $deleted++;
                }
            }
            
            echo "   ✅ Cleaned up {$deleted} duplicate user(s)\n";
            $cleanedCount++;
        } else {
            echo "   ⚠️  No user with matching email found - skipping\n";
        }
        
        echo "\n";
    }
}

if ($issueCount === 0) {
    echo "✅ No duplicate users found!\n";
} else {
    echo "🎉 Cleanup complete!\n";
    echo "   Teachers with duplicates: {$issueCount}\n";
    echo "   Teachers cleaned: {$cleanedCount}\n";
}
