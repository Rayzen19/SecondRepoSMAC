<?php
/**
 * Check Laravel Configuration for Session Issues
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Laravel Configuration Check ===\n\n";

echo "APP_URL: " . config('app.url') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n\n";

echo "SESSION_DRIVER: " . config('session.driver') . "\n";
echo "SESSION_LIFETIME: " . config('session.lifetime') . " minutes\n";
echo "SESSION_COOKIE: " . config('session.cookie') . "\n";
echo "SESSION_DOMAIN: " . (config('session.domain') ?: 'null (default)') . "\n";
echo "SESSION_SECURE_COOKIE: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "SESSION_SAME_SITE: " . config('session.same_site') . "\n\n";

// Check if sessions table exists
try {
    $sessionsCount = DB::table('sessions')->count();
    echo "Sessions in database: $sessionsCount\n";
} catch (\Exception $e) {
    echo "Sessions table check failed: " . $e->getMessage() . "\n";
}

// Check storage permissions
echo "\n=== Storage Directory Permissions ===\n";
$sessionPath = storage_path('framework/sessions');
echo "Session path: $sessionPath\n";
echo "Writable: " . (is_writable($sessionPath) ? 'Yes' : 'NO - THIS IS A PROBLEM!') . "\n";

if (!is_writable($sessionPath)) {
    echo "\n⚠️  WARNING: Session directory is not writable!\n";
    echo "Run this command to fix:\n";
    echo "chmod -R 775 storage\n";
    echo "chown -R www-data:www-data storage\n";
}

echo "\n=== CSRF Token Test ===\n";
echo "Current CSRF Token: " . csrf_token() . "\n";

echo "\n=== Recommendations ===\n";
echo "1. Make sure APP_URL in .env matches the URL you're using\n";
echo "   Example: APP_URL=http://127.0.0.1:8000\n";
echo "2. Clear browser cache and cookies\n";
echo "3. Try in incognito/private browsing mode\n";
echo "4. If using HTTPS, make sure SESSION_SECURE_COOKIE is true\n";
echo "5. If using HTTP, make sure SESSION_SECURE_COOKIE is false or null\n";
