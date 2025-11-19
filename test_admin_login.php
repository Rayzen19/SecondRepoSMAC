<?php
// Test admin login debug script
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Admin Login Debug ===\n\n";

$email = 'tamayomoore@gmail.com';

// Check if user exists
$user = User::where('email', $email)->first();

if ($user) {
    echo "✓ User found\n";
    echo "  Email: {$user->email}\n";
    echo "  User Type: {$user->type}\n";
    echo "  ID: {$user->id}\n";
    echo "  Has Password: " . (!empty($user->password) ? 'Yes' : 'No') . "\n";
    
    // Test password
    $testPassword = 'password'; // Default password
    if (Hash::check($testPassword, $user->password)) {
        echo "  ✓ Password 'password' works!\n";
    } else {
        echo "  ✗ Password 'password' does NOT work\n";
        echo "  Try resetting the password.\n";
    }
} else {
    echo "✗ User not found with email: {$email}\n";
}

echo "\n=== Session Configuration ===\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";
echo "SESSION_DOMAIN: " . (config('session.domain') ?: 'null') . "\n";
echo "SESSION_SAME_SITE: " . config('session.same_site') . "\n";
echo "APP_URL: " . config('app.url') . "\n";

echo "\n=== Check sessions table ===\n";
try {
    $sessionCount = DB::table('sessions')->count();
    echo "Sessions in database: {$sessionCount}\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
