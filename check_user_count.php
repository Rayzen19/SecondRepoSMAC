<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== USER COUNT ANALYSIS ===\n\n";

$totalUsers = User::count();
echo "Total Users in Database: {$totalUsers}\n\n";

echo "Breakdown by Type:\n";
$byType = User::select('type', DB::raw('count(*) as count'))
    ->groupBy('type')
    ->orderBy('count', 'desc')
    ->get();

foreach ($byType as $item) {
    $type = $item->type ?: '(NULL/Empty)';
    echo "  {$type}: {$item->count}\n";
}

echo "\n";

// Check for potential duplicates or test data
echo "Recent 10 Users:\n";
$recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get(['id', 'name', 'email', 'type', 'created_at']);
foreach ($recentUsers as $user) {
    echo "  ID: {$user->id} | {$user->name} | {$user->email} | Type: {$user->type} | Created: {$user->created_at}\n";
}

echo "\n";

// Check actual counts from related tables
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Admin;

echo "Counts from Related Tables:\n";
echo "  Students: " . Student::count() . "\n";
echo "  Teachers: " . Teacher::count() . "\n";

// Check for Admins if the model exists
try {
    echo "  Admins: " . Admin::count() . "\n";
} catch (Exception $e) {
    echo "  Admins: (table not found)\n";
}

// Check for Guardians
try {
    $guardianCount = DB::table('guardians')->count();
    echo "  Guardians: " . $guardianCount . "\n";
} catch (Exception $e) {
    echo "  Guardians: (table not found)\n";
}
