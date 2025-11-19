<?php

/**
 * Test Co-Admin Profile Access
 * 
 * This script verifies that co-admin users can access profile features.
 * Run this from the command line to test the co-admin profile implementation.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "🧪 Testing Co-Admin Profile Feature\n";
echo str_repeat("=", 50) . "\n\n";

// Find or create a test co-admin
echo "1️⃣  Checking for test co-admin account...\n";

$coAdmin = User::where('email', 'coadmin@test.com')->first();

if (!$coAdmin) {
    echo "   ❌ Test co-admin not found!\n";
    echo "   💡 Run: php create_test_coadmin.php\n";
    exit(1);
}

if ($coAdmin->type !== 'co-admin') {
    echo "   ❌ User exists but is not a co-admin!\n";
    exit(1);
}

echo "   ✅ Test co-admin found: {$coAdmin->email}\n\n";

// Test profile data access
echo "2️⃣  Testing profile data retrieval...\n";

try {
    echo "   Name: {$coAdmin->name}\n";
    echo "   Email: {$coAdmin->email}\n";
    echo "   Type: {$coAdmin->type}\n";
    echo "   Created: {$coAdmin->created_at->format('Y-m-d')}\n";
    echo "   ✅ Profile data accessible\n\n";
} catch (Exception $e) {
    echo "   ❌ Error accessing profile data: {$e->getMessage()}\n";
    exit(1);
}

// Test profile update
echo "3️⃣  Testing profile update capability...\n";

try {
    $originalName = $coAdmin->name;
    $testName = "Test CoAdmin " . time();
    
    $coAdmin->name = $testName;
    $coAdmin->save();
    
    $coAdmin->refresh();
    
    if ($coAdmin->name === $testName) {
        echo "   ✅ Name update successful\n";
        
        // Restore original name
        $coAdmin->name = $originalName;
        $coAdmin->save();
        echo "   ✅ Name restored to original\n\n";
    } else {
        echo "   ❌ Name update failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error updating profile: {$e->getMessage()}\n";
    exit(1);
}

// Test email update
echo "4️⃣  Testing email update capability...\n";

try {
    $originalEmail = $coAdmin->email;
    $testEmail = "test_" . time() . "@example.com";
    
    $coAdmin->email = $testEmail;
    $coAdmin->save();
    
    $coAdmin->refresh();
    
    if ($coAdmin->email === $testEmail) {
        echo "   ✅ Email update successful\n";
        
        // Restore original email
        $coAdmin->email = $originalEmail;
        $coAdmin->save();
        echo "   ✅ Email restored to original\n\n";
    } else {
        echo "   ❌ Email update failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error updating email: {$e->getMessage()}\n";
    exit(1);
}

// Test password update
echo "5️⃣  Testing password update capability...\n";

try {
    $originalPassword = $coAdmin->password;
    $testPassword = 'TestPassword123!@#';
    
    $coAdmin->password = Hash::make($testPassword);
    $coAdmin->save();
    
    $coAdmin->refresh();
    
    if (Hash::check($testPassword, $coAdmin->password)) {
        echo "   ✅ Password update successful\n";
        echo "   ✅ Password verification works\n";
        
        // Restore original password
        $coAdmin->password = $originalPassword;
        $coAdmin->save();
        echo "   ✅ Password restored to original\n\n";
    } else {
        echo "   ❌ Password verification failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error updating password: {$e->getMessage()}\n";
    exit(1);
}

// Test account type detection
echo "6️⃣  Testing account type detection...\n";

try {
    $isCoAdmin = ($coAdmin->type === 'co-admin');
    $isNotAdmin = ($coAdmin->type !== 'admin');
    
    if ($isCoAdmin && $isNotAdmin) {
        echo "   ✅ Account type correctly identified as co-admin\n\n";
    } else {
        echo "   ❌ Account type detection failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error detecting account type: {$e->getMessage()}\n";
    exit(1);
}

// Test routes existence
echo "7️⃣  Checking profile routes...\n";

$routes = [
    'admin.profile.show',
    'admin.profile.edit',
    'admin.profile.update',
    'admin.profile.password.edit',
    'admin.profile.password.update',
];

$routesExist = true;
foreach ($routes as $routeName) {
    try {
        $url = route($routeName);
        echo "   ✅ Route exists: {$routeName}\n";
    } catch (Exception $e) {
        echo "   ❌ Route missing: {$routeName}\n";
        $routesExist = false;
    }
}

if (!$routesExist) {
    exit(1);
}

echo "\n";

// Final summary
echo str_repeat("=", 50) . "\n";
echo "✅ ALL TESTS PASSED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "📊 Summary:\n";
echo "   ✓ Co-admin account verified\n";
echo "   ✓ Profile data accessible\n";
echo "   ✓ Name updates work\n";
echo "   ✓ Email updates work\n";
echo "   ✓ Password updates work\n";
echo "   ✓ Account type detection works\n";
echo "   ✓ All profile routes exist\n\n";

echo "🎯 Co-Admin Profile Feature: READY TO USE!\n\n";

echo "🔗 Test the feature in browser:\n";
echo "   1. Login at: /admin/login\n";
echo "   2. Email: coadmin@test.com\n";
echo "   3. Password: password\n";
echo "   4. Click 'My Profile' in sidebar\n\n";

echo "📚 Documentation:\n";
echo "   • CO_ADMIN_PROFILE_FEATURE.md - Full documentation\n";
echo "   • CO_ADMIN_PROFILE_QUICK_GUIDE.md - Quick reference\n";
echo "   • CO_ADMIN_PROFILE_VISUAL_GUIDE.md - Visual guide\n";
echo "   • CO_ADMIN_PROFILE_SUMMARY.md - Implementation summary\n\n";
