<?php
/**
 * Test Pusher Connection Script
 * This script tests if Pusher credentials are valid
 */

require __DIR__.'/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Pusher\Pusher;

echo "Testing Pusher Connection...\n";
echo "========================================\n\n";

try {
    // Create Pusher instance
    $pusher = new Pusher(
        $_ENV['PUSHER_APP_KEY'],
        $_ENV['PUSHER_APP_SECRET'],
        $_ENV['PUSHER_APP_ID'],
        [
            'cluster' => $_ENV['PUSHER_APP_CLUSTER'],
            'useTLS' => true
        ]
    );
    
    echo "✓ Pusher instance created successfully\n";
    echo "  App ID: " . $_ENV['PUSHER_APP_ID'] . "\n";
    echo "  Cluster: " . $_ENV['PUSHER_APP_CLUSTER'] . "\n\n";
    
    // Test trigger event
    echo "Testing event trigger...\n";
    $result = $pusher->trigger(
        'test-channel',
        'test-event',
        ['message' => 'Hello from SMAC! Connection successful!']
    );
    
    if ($result) {
        echo "✓ Event triggered successfully!\n";
        echo "✓ Pusher connection is working!\n\n";
        echo "========================================\n";
        echo "SUCCESS! Your Pusher credentials are correct.\n";
        echo "Real-time messaging should now work!\n";
    } else {
        echo "✗ Failed to trigger event\n";
        echo "Check your credentials.\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nPlease check your Pusher credentials in .env file.\n";
}
