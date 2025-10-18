<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Teacher 15:\n";
echo "=====================================\n\n";

$teacher = App\Models\Teacher::find(15);

if ($teacher) {
    echo "✓ Teacher Found!\n\n";
    echo "ID: " . $teacher->id . "\n";
    echo "Name: " . $teacher->first_name . " " . $teacher->last_name . "\n";
    echo "Email: " . $teacher->email . "\n";
    
    echo "\n\nAdvised Sections:\n";
    $advisedSections = App\Models\AcademicYearStrandSection::with(['section', 'strand', 'academicYear'])
        ->where('adviser_teacher_id', $teacher->id)
        ->where('is_active', true)
        ->get();
    
    if ($advisedSections->isEmpty()) {
        echo "No advised sections found.\n";
    } else {
        foreach ($advisedSections as $section) {
            echo "  - ID: " . $section->id . " - ";
            echo "Grade " . ($section->section->grade ?? '?') . " ";
            echo "Section " . ($section->section->name ?? '?') . " - ";
            echo ($section->strand->code ?? '?') . "\n";
        }
    }
} else {
    echo "✗ Teacher 15 NOT FOUND\n";
}

echo "\n\nAll Teachers:\n";
echo "=====================================\n";
$allTeachers = App\Models\Teacher::all();
foreach ($allTeachers as $t) {
    echo "ID: " . $t->id . " - " . $t->first_name . " " . $t->last_name . "\n";
}
