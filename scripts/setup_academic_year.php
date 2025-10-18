<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicYear;

$year = AcademicYear::where('is_active', true)->first();
if (!$year) {
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $yearString = "$currentYear-$nextYear";
    $year = AcademicYear::create([
        'name' => "Academic Year $yearString",
        'year' => $yearString,
        'is_active' => true,
        'start_date' => "$currentYear-08-01",
        'end_date' => "$nextYear-05-31"
    ]);
    echo "Created active academic year: {$year->year}\n";
} else {
    echo "Active academic year exists: {$year->year}\n";
}
