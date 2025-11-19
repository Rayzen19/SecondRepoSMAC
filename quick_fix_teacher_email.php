<?php

/**
 * Quick Fix: Consolidate Teacher Email
 * 
 * This script will update the teacher's email to use the CVSU email
 * and ensure consistency across the system.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Quick Fix: Teacher Email Consolidation ===\n\n";

try {
    DB::beginTransaction();
    
    // The official email to use for the teacher
    $officialEmail = 'johnraymond.barrogo@cvsu.edu.ph';
    
    echo "Setting teacher email to: {$officialEmail}\n\n";
    
    // 1. Update teacher record
    $updated = DB::table('teachers')
        ->where('id', 1)
        ->update(['email' => $officialEmail]);
    
    if ($updated) {
        echo "✓ Updated teacher record\n";
    }
    
    // 2. Update teacher user account
    $updated = DB::table('users')
        ->where('id', 5)
        ->update(['email' => $officialEmail]);
    
    if ($updated) {
        echo "✓ Updated teacher user account\n";
    }
    
    // 3. Check for conflicts
    $userCount = DB::table('users')
        ->where('email', $officialEmail)
        ->count();
    
    if ($userCount > 1) {
        echo "\n⚠ WARNING: Multiple users with email {$officialEmail}\n";
        echo "Manual intervention may be required.\n";
    }
    
    DB::commit();
    
    echo "\n=== Fix Applied Successfully ===\n";
    echo "Teacher email is now: {$officialEmail}\n";
    echo "\nNext steps:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Teacher should log in with: {$officialEmail}\n";
    echo "3. Verify messages show correct email\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\nERROR: " . $e->getMessage() . "\n";
    exit(1);
}
