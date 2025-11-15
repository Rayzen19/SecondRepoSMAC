<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Teacher;

$teacher = Teacher::first();

if ($teacher) {
    echo "Teacher ID: {$teacher->id} - {$teacher->first_name} {$teacher->last_name}\n";
} else {
    echo "No teachers found\n";
}
