<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Announcement;

echo "=== Fixing Announcement Publish Date ===\n\n";

$announcement = Announcement::find(1);

if ($announcement) {
    echo "Found: {$announcement->title}\n";
    echo "Current Published At: " . ($announcement->published_at ? $announcement->published_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    
    // Set published_at to null so it shows immediately
    $announcement->published_at = null;
    $announcement->save();
    
    echo "Updated Published At: NULL (shows immediately)\n";
    echo "\n✅ Announcement is now visible on the landing page!\n";
} else {
    echo "❌ Announcement not found.\n";
}
