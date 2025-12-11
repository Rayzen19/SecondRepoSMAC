<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;

echo "=== Searching for Teacher: Pam Toledo Murillo ===\n\n";

// Try various search patterns
echo "All teachers with 'pam' in any name field:\n";
$teachers = Teacher::where('first_name', 'like', '%pam%')
    ->orWhere('middle_name', 'like', '%pam%')
    ->orWhere('last_name', 'like', '%pam%')
    ->get();

if ($teachers->isEmpty()) {
    echo "  No teachers found with 'pam'\n\n";
} else {
    foreach ($teachers as $t) {
        echo "  ID: {$t->id}, Name: {$t->last_name}, {$t->first_name} {$t->middle_name}, Status: {$t->status}\n";
    }
    echo "\n";
}

echo "All teachers with 'toledo' in any name field:\n";
$teachers = Teacher::where('first_name', 'like', '%toledo%')
    ->orWhere('middle_name', 'like', '%toledo%')
    ->orWhere('last_name', 'like', '%toledo%')
    ->get();

if ($teachers->isEmpty()) {
    echo "  No teachers found with 'toledo'\n\n";
} else {
    foreach ($teachers as $t) {
        echo "  ID: {$t->id}, Name: {$t->last_name}, {$t->first_name} {$t->middle_name}, Status: {$t->status}\n";
    }
    echo "\n";
}

echo "All teachers with 'murillo' in any name field:\n";
$teachers = Teacher::where('first_name', 'like', '%murillo%')
    ->orWhere('middle_name', 'like', '%murillo%')
    ->orWhere('last_name', 'like', '%murillo%')
    ->get();

if ($teachers->isEmpty()) {
    echo "  No teachers found with 'murillo'\n\n";
} else {
    foreach ($teachers as $t) {
        echo "  ID: {$t->id}, Name: {$t->last_name}, {$t->first_name} {$t->middle_name}, Status: {$t->status}\n";
    }
    echo "\n";
}

echo "=== All Active Teachers (alphabetically) ===\n";
$allTeachers = Teacher::where('status', 'active')
    ->orderBy('last_name')
    ->orderBy('first_name')
    ->get();

echo "Total active teachers: " . $allTeachers->count() . "\n\n";
foreach ($allTeachers as $t) {
    echo "{$t->id}. {$t->last_name}, {$t->first_name} {$t->middle_name}\n";
}
