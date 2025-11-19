<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Finding Valid Student User ===\n\n";

// Get all student users
$studentUsers = \App\Models\Auth\StudentUser::all();

echo "Total Student Users: " . $studentUsers->count() . "\n\n";

foreach ($studentUsers as $user) {
    echo "Student User ID: " . $user->id . "\n";
    echo "Username: " . ($user->username ?? 'N/A') . "\n";
    echo "Email: " . ($user->email ?? 'N/A') . "\n";
    
    // Try to find the student record
    $student = \App\Models\Student::where('user_id', $user->id)->first();
    
    if ($student) {
        echo "✓ Found Student Record:\n";
        echo "  Student ID: " . $student->id . "\n";
        echo "  Name: " . $student->first_name . " " . $student->last_name . "\n";
        
        // Check enrollment
        $enrollment = \App\Models\StudentEnrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->latest()
            ->first();
        
        if ($enrollment) {
            echo "  ✓ Has Active Enrollment\n";
            echo "  Academic Year ID: " . $enrollment->academic_year_id . "\n";
            
            $academicYear = \App\Models\AcademicYear::find($enrollment->academic_year_id);
            if ($academicYear) {
                echo "  Academic Year: " . $academicYear->name . " " . $academicYear->semester . "\n";
                echo "  Pre-enrollment Enabled: " . ($academicYear->pre_enrollment_enabled ? 'YES ✓' : 'NO ✗') . "\n";
            }
        } else {
            echo "  ✗ No Active Enrollment\n";
        }
        
        echo "\n";
        break; // Found one, that's enough
    } else {
        echo "✗ No Student Record Found\n\n";
    }
}

echo "=== Done ===\n";
