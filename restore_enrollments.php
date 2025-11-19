<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Restoring Soft-Deleted Enrollments ===\n\n";

// Restore all soft-deleted enrollments
$restored = \Illuminate\Support\Facades\DB::table('student_enrollments')
    ->whereNotNull('deleted_at')
    ->update([
        'deleted_at' => null,
        'updated_at' => now()
    ]);

echo "✅ Restored {$restored} soft-deleted enrollment records!\n\n";

// Verify
$enrollments = \App\Models\StudentEnrollment::where('academic_year_id', 5)
    ->where('academic_year_strand_section_id', 1)
    ->with('student')
    ->get();

echo "Students now enrolled in JUDE section: " . $enrollments->count() . "\n\n";

foreach ($enrollments as $enrollment) {
    if ($enrollment->student) {
        echo "  - {$enrollment->student->student_number}: {$enrollment->student->first_name} {$enrollment->student->last_name}\n";
    }
}

echo "\n✅ Done! Now refresh the Section & Advisers page!\n";
