<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $deleted = DB::table('system_settings')
        ->where('key', 'max_teacher_subjects_per_section')
        ->delete();
    
    if ($deleted) {
        echo "✓ Successfully deleted 'max_teacher_subjects_per_section' setting from database.\n";
    } else {
        echo "ℹ Setting 'max_teacher_subjects_per_section' not found in database.\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
