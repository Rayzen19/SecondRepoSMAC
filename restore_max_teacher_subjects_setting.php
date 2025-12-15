<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Check if it already exists
    $exists = DB::table('system_settings')
        ->where('key', 'max_teacher_subjects_per_section')
        ->exists();
    
    if ($exists) {
        echo "ℹ Setting 'max_teacher_subjects_per_section' already exists in database.\n";
    } else {
        // Insert the setting
        DB::table('system_settings')->insert([
            'key' => 'max_teacher_subjects_per_section',
            'value' => '5',
            'type' => 'integer',
            'description' => 'Maximum number of subjects a teacher can be assigned per section',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "✓ Successfully restored 'max_teacher_subjects_per_section' setting to database.\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
