<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;

echo "Searching for teachers with 'barrogo' in email or name:\n\n";

$teachers = Teacher::where('email', 'LIKE', '%barrogo%')
    ->orWhere('first_name', 'LIKE', '%barrogo%')
    ->orWhere('last_name', 'LIKE', '%barrogo%')
    ->get();

if ($teachers->count() > 0) {
    echo "Found {$teachers->count()} teacher(s):\n\n";
    foreach ($teachers as $teacher) {
        echo "  ID: {$teacher->id}\n";
        echo "  Name: {$teacher->first_name} {$teacher->last_name}\n";
        echo "  Employee Number: {$teacher->employee_number}\n";
        echo "  Email: {$teacher->email}\n";
        echo "  Department: {$teacher->department}\n";
        echo "  Status: {$teacher->status}\n";
        echo "  " . str_repeat("-", 50) . "\n\n";
    }
} else {
    echo "No teachers found with 'barrogo'.\n\n";
}

echo "Searching for teachers with 'johnraymond' or 'john raymond':\n\n";

$teachers = Teacher::where(function($query) {
        $query->where('first_name', 'LIKE', '%john%')
              ->where('first_name', 'LIKE', '%raymond%');
    })
    ->orWhere('first_name', 'LIKE', '%johnraymond%')
    ->get();

if ($teachers->count() > 0) {
    echo "Found {$teachers->count()} teacher(s):\n\n";
    foreach ($teachers as $teacher) {
        echo "  ID: {$teacher->id}\n";
        echo "  Name: {$teacher->first_name} {$teacher->last_name}\n";
        echo "  Employee Number: {$teacher->employee_number}\n";
        echo "  Email: {$teacher->email}\n";
        echo "  Department: {$teacher->department}\n";
        echo "  Status: {$teacher->status}\n";
        echo "  " . str_repeat("-", 50) . "\n\n";
    }
} else {
    echo "No teachers found.\n\n";
}

echo "Searching for all teachers with @cvsu.edu.ph email:\n\n";

$teachers = Teacher::where('email', 'LIKE', '%@cvsu.edu.ph%')->get();

if ($teachers->count() > 0) {
    echo "Found {$teachers->count()} teacher(s) with CVSU email:\n\n";
    foreach ($teachers as $teacher) {
        echo "  ID: {$teacher->id}\n";
        echo "  Name: {$teacher->first_name} {$teacher->last_name}\n";
        echo "  Employee Number: {$teacher->employee_number}\n";
        echo "  Email: {$teacher->email}\n";
        echo "  Department: {$teacher->department}\n";
        echo "  Status: {$teacher->status}\n";
        echo "  " . str_repeat("-", 50) . "\n\n";
    }
} else {
    echo "No teachers found with @cvsu.edu.ph email.\n";
}
