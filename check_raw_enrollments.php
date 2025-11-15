<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Raw Database ===\n\n";

// Check with soft deletes
$withSoftDeletes = \App\Models\StudentEnrollment::where('academic_year_strand_section_id', 1)
    ->where('academic_year_id', 5)
    ->count();

echo "Count with soft delete filtering (Model): {$withSoftDeletes}\n";

// Check without soft deletes
$withTrashed = \App\Models\StudentEnrollment::withTrashed()
    ->where('academic_year_strand_section_id', 1)
    ->where('academic_year_id', 5)
    ->count();

echo "Count including soft deleted (withTrashed): {$withTrashed}\n";

// Raw count
$raw = \Illuminate\Support\Facades\DB::table('student_enrollments')
    ->where('academic_year_strand_section_id', 1)
    ->where('academic_year_id', 5)
    ->count();

echo "Raw DB count: {$raw}\n\n";

// Show the records
$records = \Illuminate\Support\Facades\DB::table('student_enrollments')
    ->where('academic_year_strand_section_id', 1)
    ->where('academic_year_id', 5)
    ->select('id', 'student_id', 'deleted_at', 'updated_at')
    ->get();

echo "Records:\n";
foreach ($records as $rec) {
    $deletedStatus = $rec->deleted_at ? "DELETED ({$rec->deleted_at})" : "ACTIVE";
    echo "  ID: {$rec->id}, Student: {$rec->student_id}, Status: {$deletedStatus}\n";
}

echo "\n✅ Done!\n";
