<?php
/**
 * Fix 419 Page Expired Error
 * This script clears cache, sessions, and optimizes the application
 */

echo "Starting 419 Error Fix...\n\n";

// Change to the Laravel root directory
chdir(__DIR__);

$commands = [
    'php artisan config:clear' => 'Clearing configuration cache...',
    'php artisan cache:clear' => 'Clearing application cache...',
    'php artisan view:clear' => 'Clearing view cache...',
    'php artisan route:clear' => 'Clearing route cache...',
    'php artisan config:cache' => 'Recaching configuration...',
];

foreach ($commands as $command => $message) {
    echo "$message\n";
    echo "Running: $command\n";
    passthru($command, $returnVar);
    echo $returnVar === 0 ? "✓ Success\n\n" : "✗ Failed\n\n";
}

echo "\n=== IMPORTANT: Please also check these ===\n\n";
echo "1. Clear your browser cache and cookies\n";
echo "2. Try accessing in incognito/private mode\n";
echo "3. Make sure APP_URL in .env matches your actual URL (127.0.0.1:8000)\n";
echo "4. Check if sessions table exists in database\n";
echo "5. Verify storage/framework/sessions directory has write permissions\n\n";

// Check sessions table
echo "Checking sessions table...\n";
passthru('php artisan tinker --execute="echo \\DB::table(\'sessions\')->count() . \' sessions in database\n\'"');

echo "\nDone! Try logging in again.\n";
