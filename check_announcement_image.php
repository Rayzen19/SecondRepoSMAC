<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Announcement;

echo "=== Checking Announcement Image ===\n\n";

$announcement = Announcement::find(1);

if ($announcement) {
    echo "Title: {$announcement->title}\n";
    echo "Image URL: " . ($announcement->image_url ?? 'NULL') . "\n";
    echo "Image Path: " . ($announcement->image_path ?? 'NULL') . "\n";
    echo "Has Image: " . ($announcement->hasImage() ? 'Yes' : 'No') . "\n";
    
    if ($announcement->hasImage()) {
        echo "Image Source: " . $announcement->image . "\n";
    }
} else {
    echo "❌ Announcement not found.\n";
}
