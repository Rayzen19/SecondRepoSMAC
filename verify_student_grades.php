<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\SubjectRecordResult;
use App\Models\SubjectRecord;

echo "=== Verifying Student Grades ===\n\n";

// Get a sample of students to verify
$students = Student::whereIn('student_number', ['STU-000041', 'STU-000042', '2025-00001'])
    ->get();

foreach ($students as $student) {
    echo "Student: {$student->first_name} {$student->last_name} ({$student->student_number})\n";
    echo str_repeat('-', 70) . "\n";
    
    $results = SubjectRecordResult::with(['subjectRecord.assignment.subject'])
        ->where('student_id', $student->id)
        ->get();
    
    if ($results->isEmpty()) {
        echo "  No grades found\n\n";
        continue;
    }
    
    $groupedResults = $results->groupBy(function($result) {
        return $result->subjectRecord->assignment->subject->name ?? 'Unknown Subject';
    });
    
    foreach ($groupedResults as $subjectName => $subjectResults) {
        echo "\n  Subject: {$subjectName}\n";
        
        foreach ($subjectResults as $result) {
            $record = $result->subjectRecord;
            echo "    • {$record->name}: {$result->raw_score}/{$result->base_score} = {$result->final_score}%\n";
        }
    }
    
    echo "\n  Total assessments: {$results->count()}\n";
    echo "\n";
}

echo "=== Verification Complete ===\n";
