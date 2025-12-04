<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Announcement;

echo "=== Active Announcements Check ===\n\n";

$allAnnouncements = Announcement::all();
echo "Total announcements in database: " . $allAnnouncements->count() . "\n\n";

if ($allAnnouncements->count() > 0) {
    echo "All Announcements:\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($allAnnouncements as $announcement) {
        echo "ID: {$announcement->id}\n";
        echo "Title: {$announcement->title}\n";
        echo "Is Active: " . ($announcement->is_active ? 'Yes' : 'No') . "\n";
        echo "Published At: " . ($announcement->published_at ? $announcement->published_at->format('Y-m-d H:i:s') : 'NULL (immediate)') . "\n";
        echo "Expires At: " . ($announcement->expires_at ? $announcement->expires_at->format('Y-m-d H:i:s') : 'NULL (never)') . "\n";
        echo "Created At: " . $announcement->created_at->format('Y-m-d H:i:s') . "\n";
        echo str_repeat("-", 80) . "\n";
    }
}

echo "\n=== Active Announcements (shown on landing page) ===\n\n";
$activeAnnouncements = Announcement::active()->latest()->take(3)->get();
echo "Count: " . $activeAnnouncements->count() . "\n\n";

if ($activeAnnouncements->count() > 0) {
    foreach ($activeAnnouncements as $announcement) {
        echo "• {$announcement->title}\n";
        echo "  Content: " . \Illuminate\Support\Str::limit($announcement->content, 80) . "\n";
        echo "  Published: " . ($announcement->published_at ? $announcement->published_at->format('M d, Y') : 'Now') . "\n\n";
    }
} else {
    echo "No active announcements found.\n";
    echo "\nTo create announcements:\n";
    echo "1. Login as admin at: " . url('/admin/login') . "\n";
    echo "2. Go to Announcements menu\n";
    echo "3. Click 'Add Announcement'\n";
    echo "4. Fill in the form and check 'Active' checkbox\n\n";
    echo "Or run the seeder: php artisan db:seed --class=AnnouncementSeeder\n";
}
