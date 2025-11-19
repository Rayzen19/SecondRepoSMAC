<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking for duplicate registration numbers ===\n\n";

// Check using DB facade
$duplicates = \Illuminate\Support\Facades\DB::table('student_enrollments')
    ->where('registration_number', 'LIKE', 'REG-2025-%')
    ->get();

echo "Found " . $duplicates->count() . " records with REG-2025 prefix:\n\n";

foreach ($duplicates as $dup) {
    echo "ID: {$dup->id}, Reg#: {$dup->registration_number}, Student: {$dup->student_id}, Year: {$dup->academic_year_id}\n";
}

if ($duplicates->count() > 0) {
    echo "\n⚠️  Would you like to delete these records? (Y/N): ";
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    if(trim($line) == 'Y' || trim($line) == 'y'){
        \Illuminate\Support\Facades\DB::table('student_enrollments')
            ->where('registration_number', 'LIKE', 'REG-2025-%')
            ->delete();
        echo "✅ Deleted all REG-2025 enrollment records\n";
    } else {
        echo "❌ Aborted\n";
    }
    fclose($handle);
}

echo "\n✅ Done!\n";
