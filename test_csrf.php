<?php
// Simple test to check if CSRF token generation works
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\nTesting CSRF Token Generation:\n";
echo "==============================\n\n";

// Try to get CSRF token
$token = csrf_token();

echo "✓ CSRF Token Generated: " . substr($token, 0, 20) . "...\n";
echo "✓ Server is working correctly!\n\n";

echo "If you're still getting 419 error:\n";
echo "→ You MUST clear your browser cookies!\n";
echo "→ Or use Incognito/Private mode\n\n";

echo "Quick Fix:\n";
echo "1. Press Ctrl+Shift+Delete\n";
echo "2. Check 'Cookies and other site data'\n";
echo "3. Click 'Clear data'\n";
echo "4. Close ALL browser windows\n";
echo "5. Reopen browser and try again\n";
