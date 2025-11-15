<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StrandSubject;

echo "=== TVL-CP StrandSubject Grade Levels ===\n\n";

$strandSubjects = StrandSubject::with('subject')
    ->where('strand_id', 5)
    ->get();

foreach ($strandSubjects as $ss) {
    echo "ID: {$ss->id} | ";
    echo "Subject: [{$ss->subject->code}] {$ss->subject->name} | ";
    echo "Grade: " . ($ss->grade_level ?? 'NULL') . " | ";
    echo "Active: " . ($ss->is_active ? 'Yes' : 'No') . "\n";
}
