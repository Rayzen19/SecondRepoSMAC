<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

echo "Student::count(): " . Student::count() . PHP_EOL;
echo "Student::withTrashed()->count(): " . Student::withTrashed()->count() . PHP_EOL;
echo "Student::onlyTrashed()->count(): " . Student::onlyTrashed()->count() . PHP_EOL;

$all = Student::withTrashed()->orderBy('id')->get();
if ($all->isEmpty()) {
    echo "No student rows found\n";
} else {
    foreach ($all as $s) {
        echo "id={$s->id} student_number={$s->student_number} deleted_at={$s->deleted_at}\n";
    }
}
