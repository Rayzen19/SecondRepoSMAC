<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Admin Account Information ===\n\n";

// Reset password for admin@smac.edu
$admin1 = App\Models\User::where('email', 'admin@smac.edu')->first();
if ($admin1) {
    $admin1->password = bcrypt('admin123');
    $admin1->save();
    echo "✅ Admin Account 1:\n";
    echo "   Email: admin@smac.edu\n";
    echo "   Password: admin123\n\n";
}

// Reset password for joh@gmail.com
$admin2 = App\Models\User::where('email', 'joh@gmail.com')->first();
if ($admin2) {
    $admin2->password = bcrypt('password');
    $admin2->save();
    echo "✅ Admin Account 2:\n";
    echo "   Email: joh@gmail.com\n";
    echo "   Password: password\n\n";
}

echo "=== Login Instructions ===\n";
echo "1. Go to: http://127.0.0.1:8000/fix-session?redirect=admin\n";
echo "2. Or use Incognito mode: Ctrl+Shift+N\n";
echo "3. Then go to: http://127.0.0.1:8000/admin/login\n";
echo "4. Login with either account above\n";
