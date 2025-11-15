<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Pre-Enrollment Submissions ===\n\n";

$preEnrollments = \App\Models\PreEnrollment::with(['student', 'strand', 'section', 'currentAcademicYear'])
    ->orderBy('submitted_at', 'desc')
    ->get();

echo "Total Pre-Enrollment Submissions: {$preEnrollments->count()}\n\n";

if ($preEnrollments->isEmpty()) {
    echo "No pre-enrollment submissions found.\n";
    exit;
}

$statusCounts = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'processed' => 0,
];

foreach ($preEnrollments as $pe) {
    $statusCounts[$pe->status] = ($statusCounts[$pe->status] ?? 0) + 1;
}

echo "Status Summary:\n";
echo "  - Pending: {$statusCounts['pending']}\n";
echo "  - Approved: {$statusCounts['approved']}\n";
echo "  - Processed: {$statusCounts['processed']}\n";
echo "  - Rejected: {$statusCounts['rejected']}\n\n";

echo "Recent Submissions:\n";
echo str_repeat('-', 80) . "\n";

foreach ($preEnrollments->take(10) as $pe) {
    $student = $pe->student;
    $status = strtoupper($pe->status);
    $badge = match($pe->status) {
        'pending' => '⏳',
        'approved' => '✅',
        'processed' => '✔️',
        'rejected' => '❌',
        default => '❓'
    };
    
    echo "{$badge} [{$status}] {$student->first_name} {$student->last_name} ({$student->student_number})\n";
    echo "    Grade: {$pe->grade_level} | Strand: {$pe->strand->code} | Section: " . ($pe->section ? $pe->section->name : 'No preference') . "\n";
    echo "    Academic Year: {$pe->currentAcademicYear->name}\n";
    echo "    Submitted: {$pe->submitted_at}\n";
    if ($pe->processed_at) {
        echo "    Processed: {$pe->processed_at}\n";
    }
    echo "\n";
}

echo "\n=== Done ===\n";
