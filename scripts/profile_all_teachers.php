<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Teacher;
use App\Models\Subject;

$teachers = Teacher::where('status', 'active')->get();
$subjects = Subject::all();

if ($teachers->isEmpty() || $subjects->isEmpty()) {
    echo "No teachers or subjects found.\n";
    exit;
}

echo "Profiling all teachers for all subjects...\n";
foreach ($teachers as $teacher) {
    $teacher->subjects()->sync($subjects->pluck('id')->toArray());
    echo "✓ {$teacher->first_name} {$teacher->last_name}\n";
}

echo "\nTotal links: " . DB::table('teacher_subject')->count() . "\n";
echo "Done!\n";
