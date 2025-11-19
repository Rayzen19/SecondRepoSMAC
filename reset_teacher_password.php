<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'teacher@school.test')->first();
if ($user) {
    $user->password = bcrypt('password');
    $user->save();
    echo "✅ Teacher password reset successfully!\n";
    echo "Email: teacher@school.test\n";
    echo "Password: password\n";
} else {
    echo "❌ Teacher user not found!\n";
}
