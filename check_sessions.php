<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check the most recent login session
echo "=== Checking Current Browser Session ===\n\n";

// List all students with pre-enrollments
$preEnrollments = \App\Models\PreEnrollment::with(['student'])
    ->orderBy('created_at', 'desc')
    ->get();

echo "All Pre-Enrollments:\n";
foreach ($preEnrollments as $pe) {
    echo "  Student ID: {$pe->student_id} | Email: {$pe->student->email} | Status: {$pe->status} | Created: {$pe->created_at}\n";
}

echo "\n\nTo see which student you're logged in as, check the current session or login again.\n";
