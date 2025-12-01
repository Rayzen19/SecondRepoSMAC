<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Final Verification: Testing getSectionStudents API ===\n\n";

// Simulate a request to get students for G-11 A - JUDE
$controller = new \App\Http\Controllers\Admin\SectionAdviserController();

// Create a mock request
$request = new \Illuminate\Http\Request();
$request->merge([
    'strand_code' => 'ABM',
    'section_id' => 1, // G-11 A - JUDE
]);

echo "Test 1: G-11 A - JUDE (should return 0 students)\n";
echo "================================================\n";

try {
    $response = $controller->getSectionStudents($request);
    $data = json_decode($response->getContent(), true);
    
    echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "Student Count: " . ($data['count'] ?? 0) . "\n";
    echo "Students: " . (empty($data['students']) ? 'None' : count($data['students'])) . "\n";
    
    if (!empty($data['students'])) {
        echo "\n⚠️ WARNING: Students found (should be empty!):\n";
        foreach ($data['students'] as $student) {
            echo "  - {$student['first_name']} {$student['last_name']} ({$student['student_number']})\n";
        }
    } else {
        echo "\n✅ CORRECT: No students in G-11 A - JUDE\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n\nTest 2: G-12 A - JOB (should return 1 student: John Raymond)\n";
echo "=============================================================\n";

$request2 = new \Illuminate\Http\Request();
$request2->merge([
    'strand_code' => 'ABM',
    'section_id' => 24, // G-12 A - JOB
]);

try {
    $response2 = $controller->getSectionStudents($request2);
    $data2 = json_decode($response2->getContent(), true);
    
    echo "Success: " . ($data2['success'] ? 'Yes' : 'No') . "\n";
    echo "Student Count: " . ($data2['count'] ?? 0) . "\n";
    
    if (!empty($data2['students'])) {
        echo "\nStudents:\n";
        foreach ($data2['students'] as $student) {
            echo "  ✅ {$student['first_name']} {$student['last_name']}\n";
            echo "     Student Number: {$student['student_number']}\n";
            echo "     Program: {$student['program']}\n";
            echo "     Academic Year: {$student['academic_year']}\n";
            echo "     Registration: {$student['registration_number']}\n";
        }
    } else {
        echo "\n⚠️ WARNING: No students found (expected John Raymond)\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n\n=== Verification Complete ===\n";
