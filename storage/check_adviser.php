<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYearStrandAdviser;

$adv = AcademicYearStrandAdviser::where('academic_year_id', 5)->first();
echo 'Adviser id: ' . ($adv->id ?? 'none') . PHP_EOL;

// If none, create one for testing purposes
if (!$adv) {
    $teacher = \App\Models\Teacher::first();
    $strand = \App\Models\Strand::first();
    $adv = AcademicYearStrandAdviser::create([
        'academic_year_id' => 5,
        'strand_id' => $strand->id,
        'teacher_id' => $teacher->id,
    ]);
    echo 'Created adviser id: ' . $adv->id . PHP_EOL;
} else {
    echo 'Existing adviser id: ' . $adv->id . PHP_EOL;
}
