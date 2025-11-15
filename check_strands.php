<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Strand;

echo "=== Checking Available Strands ===\n\n";

$strands = Strand::all();

if ($strands->isEmpty()) {
    echo "❌ No strands found in the database!\n";
    echo "\nYou need to create strands first. Run the strand seeder.\n";
} else {
    echo "Found {$strands->count()} strand(s):\n\n";
    foreach ($strands as $strand) {
        echo "ID: {$strand->id}\n";
        echo "Code: {$strand->code}\n";
        echo "Name: {$strand->name}\n";
        echo "Description: {$strand->description}\n";
        echo "---\n\n";
    }
}
