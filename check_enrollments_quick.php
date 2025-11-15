<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Existing Enrollments ===\n\n";

$totalEnrollments = \App\Models\StudentEnrollment::count();
echo "Total enrollments in database: {$totalEnrollments}\n\n";

if ($totalEnrollments > 0) {
    $lastEnrollment = \App\Models\StudentEnrollment::orderBy('registration_number', 'desc')->first();
    echo "Last registration number: {$lastEnrollment->registration_number}\n\n";
    
    // Show last 5 enrollments
    echo "Last 5 enrollments:\n";
    $recent = \App\Models\StudentEnrollment::with('student')
        ->orderBy('registration_number', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recent as $e) {
        echo "- {$e->registration_number}: {$e->student->first_name} {$e->student->last_name}\n";
    }
}
