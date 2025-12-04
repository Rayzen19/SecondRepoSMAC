<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Announcement;

echo "=== Testing Announcement Image Display ===\n\n";

$announcement = Announcement::find(1);

if ($announcement) {
    echo "Title: {$announcement->title}\n";
    echo "Image Path (DB): " . ($announcement->image_path ?? 'NULL') . "\n";
    echo "Image URL (Generated): " . ($announcement->image ?? 'NULL') . "\n";
    echo "Has Image: " . ($announcement->hasImage() ? 'Yes' : 'No') . "\n\n";
    
    // Check file existence
    $fullPath = storage_path('app/public/' . $announcement->image_path);
    echo "Full File Path: $fullPath\n";
    echo "File Exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($fullPath)) {
        echo "File Size: " . filesize($fullPath) . " bytes\n";
        echo "File Readable: " . (is_readable($fullPath) ? 'Yes' : 'No') . "\n";
        
        // Check mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);
        echo "MIME Type: $mimeType\n";
    }
    
    // Check symlink
    $symlinkPath = public_path('storage');
    echo "\nSymlink Path: $symlinkPath\n";
    echo "Symlink Exists: " . (file_exists($symlinkPath) ? 'Yes' : 'No') . "\n";
    echo "Is Link: " . (is_link($symlinkPath) ? 'Yes' : 'No') . "\n";
    
    if (is_link($symlinkPath)) {
        echo "Link Target: " . readlink($symlinkPath) . "\n";
    }
    
    // Test the public URL path
    $publicImagePath = public_path('storage/' . $announcement->image_path);
    echo "\nPublic Image Path: $publicImagePath\n";
    echo "Accessible via symlink: " . (file_exists($publicImagePath) ? 'Yes' : 'No') . "\n";
    
} else {
    echo "❌ Announcement not found.\n";
}
