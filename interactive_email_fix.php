<?php

/**
 * Interactive Fix for Email Mismatch
 * 
 * This script helps you choose the right solution
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     MESSAGE EMAIL MISMATCH - INTERACTIVE FIX              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Show current situation
echo "CURRENT SITUATION:\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Teacher (User ID 5):\n";
echo "  • Name: Sir Barrogo, John Raymond\n";
echo "  • Email: johnraymond.barrogo@cvsu.edu.ph\n";
echo "  • Type: teacher\n";
echo "\n";
echo "Student (User ID 26):\n";
echo "  • Name: Barrogo, John Raymond\n";
echo "  • Email: johnraymondbarrogo08@gmail.com\n";
echo "  • Type: student\n";
echo "  • Student #: 2025-00021\n";
echo "\n";

echo "QUESTION: Are these the SAME person or DIFFERENT people?\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "1. SAME PERSON (Delete the student account, teacher was created twice)\n";
echo "2. DIFFERENT PEOPLE (Keep both, just update names to avoid confusion)\n";
echo "3. CANCEL (Don't make any changes)\n";
echo "\n";
echo "Enter your choice (1, 2, or 3): ";

$handle = fopen("php://stdin", "r");
$choice = trim(fgets($handle));
fclose($handle);

try {
    DB::beginTransaction();
    
    if ($choice === '1') {
        // Same person - delete duplicate student account
        echo "\n";
        echo "Deleting duplicate student account...\n";
        
        // Delete messages involving student account
        $messageIds = DB::table('messages')->where('sender_id', 26)->pluck('id');
        DB::table('message_recipients')->whereIn('message_id', $messageIds)->delete();
        DB::table('messages')->where('sender_id', 26)->delete();
        DB::table('message_recipients')->where('recipient_id', 26)->delete();
        
        // Delete user and student records
        DB::table('users')->where('id', 26)->delete();
        DB::table('students')->where('id', 21)->delete();
        
        echo "✓ Deleted student account (User ID 26)\n";
        echo "✓ Deleted student record (Student ID 21)\n";
        echo "✓ Cleaned up associated messages\n";
        echo "\n";
        echo "SUCCESS! Teacher should now use:\n";
        echo "  Email: johnraymond.barrogo@cvsu.edu.ph\n";
        echo "  Login at: http://127.0.0.1:8000/teacher/login\n";
        
    } elseif ($choice === '2') {
        // Different people - update names to avoid confusion
        echo "\n";
        echo "Updating display names to avoid confusion...\n";
        
        DB::table('users')->where('id', 5)->update([
            'name' => 'Barrogo, Sir John Raymond (Teacher)'
        ]);
        
        DB::table('users')->where('id', 26)->update([
            'name' => 'Barrogo, John Raymond (Student)'
        ]);
        
        echo "✓ Updated teacher display name\n";
        echo "✓ Updated student display name\n";
        echo "\n";
        echo "SUCCESS! Accounts are now clearly labeled:\n";
        echo "  Teacher: Barrogo, Sir John Raymond (Teacher)\n";
        echo "           Email: johnraymond.barrogo@cvsu.edu.ph\n";
        echo "\n";
        echo "  Student: Barrogo, John Raymond (Student)\n";
        echo "           Email: johnraymondbarrogo08@gmail.com\n";
        
    } else {
        echo "\nCancelled. No changes made.\n";
        DB::rollBack();
        exit(0);
    }
    
    DB::commit();
    
    echo "\n";
    echo "NEXT STEPS:\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "1. Log out from all browser sessions\n";
    echo "2. Clear browser cache (Ctrl+Shift+Delete)\n";
    echo "3. Log in with the correct account\n";
    echo "4. Test the messaging system\n";
    echo "\n";
    echo "Caches cleared automatically.\n";
    
    // Clear Laravel caches
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║                    FIX COMPLETED!                          ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n";
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\n";
    exit(1);
}
