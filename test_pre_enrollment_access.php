<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Testing Pre-Enrollment Route Access ===\n\n";

// Login as first student
$student = \App\Models\Student::first();
if (!$student) {
    echo "✗ No student found\n";
    exit(1);
}

echo "Student: {$student->first_name} {$student->last_name}\n";
echo "Student ID: {$student->id}\n\n";

// Create a fake authenticated request
\Illuminate\Support\Facades\Auth::guard('student')->loginUsingId($student->id);

echo "Authenticated as student: " . (\Illuminate\Support\Facades\Auth::guard('student')->check() ? "YES" : "NO") . "\n\n";

// Try to call the controller method directly
try {
    $controller = new \App\Http\Controllers\Student\PreEnrollmentController();
    echo "Controller instantiated successfully\n";
    
    // Test the index method
    echo "\nCalling index() method...\n";
    $response = $controller->index();
    
    if ($response instanceof \Illuminate\View\View) {
        echo "✓ View returned successfully\n";
        echo "View name: " . $response->name() . "\n";
        echo "View data keys: " . implode(', ', array_keys($response->getData())) . "\n";
    } elseif ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✓ Redirect returned\n";
        echo "Target URL: " . $response->getTargetUrl() . "\n";
        $session = $response->getSession();
        if ($session && $session->has('error')) {
            echo "Error message: " . $session->get('error') . "\n";
        }
        if ($session && $session->has('success')) {
            echo "Success message: " . $session->get('success') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Done ===\n";
