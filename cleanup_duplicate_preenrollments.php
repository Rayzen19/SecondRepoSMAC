<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Cleaning Up Duplicate Pre-Enrollments ===\n\n";

$student = \App\Models\Student::where('student_number', '2025-00005')->first();
echo "Student: {$student->first_name} {$student->last_name} ({$student->student_number})\n\n";

// Get all pre-enrollments for this student
$preEnrollments = \App\Models\PreEnrollment::where('student_id', $student->id)
    ->orderBy('id', 'asc')
    ->get();

echo "Total pre-enrollments: {$preEnrollments->count()}\n\n";

// Show all pre-enrollments
echo "Current pre-enrollments:\n";
foreach ($preEnrollments as $pe) {
    echo "  - ID: {$pe->id}, Status: {$pe->status}, Submitted: {$pe->submitted_at}\n";
}
echo "\n";

// Keep only the most recent enrolled one, mark others appropriately
$enrolledPEs = $preEnrollments->where('status', 'enrolled');
$pendingPEs = $preEnrollments->where('status', 'pending');

echo "Enrolled pre-enrollments: {$enrolledPEs->count()}\n";
echo "Pending pre-enrollments: {$pendingPEs->count()}\n\n";

if ($enrolledPEs->count() > 1) {
    echo "⚠️ Found {$enrolledPEs->count()} enrolled pre-enrollments. Keeping the most recent one...\n";
    
    // Keep the most recent, mark others as duplicate
    $mostRecent = $enrolledPEs->sortByDesc('id')->first();
    echo "Keeping: Pre-Enrollment ID {$mostRecent->id}\n\n";
    
    $duplicates = $enrolledPEs->where('id', '!=', $mostRecent->id);
    foreach ($duplicates as $dup) {
        echo "Updating duplicate ID {$dup->id}...\n";
        $dup->update([
            'remarks' => 'Duplicate - Already processed via Pre-Enrollment ID ' . $mostRecent->id
        ]);
    }
    
    echo "\n✅ Cleaned up {$duplicates->count()} duplicate enrolled pre-enrollments\n";
}

if ($pendingPEs->count() > 0) {
    echo "\n⚠️ Found {$pendingPEs->count()} pending pre-enrollments.\n";
    echo "These should be rejected or approved individually.\n";
    
    foreach ($pendingPEs as $pending) {
        echo "\nPre-Enrollment ID {$pending->id}:\n";
        echo "  Status: {$pending->status}\n";
        echo "  Submitted: {$pending->submitted_at}\n";
        echo "  Action: Rejecting as student is already enrolled...\n";
        
        $pending->update([
            'status' => 'rejected',
            'remarks' => 'Student already has an active enrollment for this academic year',
            'processed_at' => now(),
            'processed_by' => 1 // Admin ID
        ]);
        
        echo "  ✅ Rejected\n";
    }
}

echo "\n=== Final Status ===\n";
$finalPEs = \App\Models\PreEnrollment::where('student_id', $student->id)
    ->orderBy('id', 'asc')
    ->get();

foreach ($finalPEs as $pe) {
    echo "  - ID: {$pe->id}, Status: {$pe->status}\n";
}

echo "\n=== Done ===\n";
