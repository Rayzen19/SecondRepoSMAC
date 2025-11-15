<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Direct Database Query ===\n\n";

$enrollments = \Illuminate\Support\Facades\DB::table('student_enrollments')->get();

echo "Total raw enrollments: " . $enrollments->count() . "\n\n";

if ($enrollments->count() > 0) {
    foreach ($enrollments as $enrollment) {
        echo "ID: {$enrollment->id}\n";
        echo "  Student ID: {$enrollment->student_id}\n";
        echo "  Year ID: {$enrollment->academic_year_id}\n";
        echo "  AYSS ID: " . ($enrollment->academic_year_strand_section_id ?? 'NULL') . "\n";
        echo "  Strand ID: " . ($enrollment->strand_id ?? 'NULL') . "\n";
        echo "  Reg#: {$enrollment->registration_number}\n";
        echo "  Deleted at: " . ($enrollment->deleted_at ?? 'NULL') . "\n\n";
    }
    
    echo "\n=== Now updating ABM students ===\n\n";
    
    // Get ABM student IDs
    $abmStudents = \Illuminate\Support\Facades\DB::table('students')
        ->where('program', 'ABM')
        ->pluck('id')
        ->toArray();
    
    echo "Found " . count($abmStudents) . " ABM students\n\n";
    
    // Update enrollments for ABM students
    $updated = \Illuminate\Support\Facades\DB::table('student_enrollments')
        ->whereIn('student_id', $abmStudents)
        ->update([
            'academic_year_strand_section_id' => 1,
            'strand_id' => 1,
            'status' => 'enrolled',
            'updated_at' => now()
        ]);
    
    echo "✅ Updated {$updated} enrollment records!\n";
}

echo "\n✅ Done!\n";
