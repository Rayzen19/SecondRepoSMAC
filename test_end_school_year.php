<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing End of School Year Functionality ===\n\n";

// Get the active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

if (!$activeYear) {
    echo "❌ No active academic year found.\n";
    exit;
}

echo "Active Academic Year BEFORE:\n";
echo "ID: " . $activeYear->id . "\n";
echo "Name: " . $activeYear->name . "\n";
echo "Semester: " . $activeYear->semester . "\n";
echo "Pre-enrollment Enabled: " . ($activeYear->pre_enrollment_enabled ? 'YES ✓' : 'NO ✗') . "\n\n";

// Mark the school year as ended for all assignments in the active academic year
$updated = \App\Models\AcademicYearStrandSubject::where('academic_year_id', $activeYear->id)
    ->update([
        'school_year_ended' => true,
        'school_year_ended_at' => now(),
    ]);

echo "Updated {$updated} assignment(s) to mark school_year_ended = true\n\n";

// Enable pre-enrollment for the active academic year
$activeYear->update([
    'pre_enrollment_enabled' => true,
]);

echo "✓ Pre-enrollment has been enabled for the active academic year\n\n";

// Refresh and check again
$activeYear->refresh();

echo "Active Academic Year AFTER:\n";
echo "ID: " . $activeYear->id . "\n";
echo "Name: " . $activeYear->name . "\n";
echo "Semester: " . $activeYear->semester . "\n";
echo "Pre-enrollment Enabled: " . ($activeYear->pre_enrollment_enabled ? 'YES ✓' : 'NO ✗') . "\n\n";

echo "=== Success! School year has been ended and pre-enrollment enabled ===\n";
