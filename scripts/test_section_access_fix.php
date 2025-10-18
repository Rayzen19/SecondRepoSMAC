<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Section Access Fix\n";
echo "====================================\n\n";

// Simulate logged in as teacher user (get first teacher user)
$user = App\Models\User::where('type', 'teacher')->first();

if (!$user) {
    echo "❌ No teacher users found in database\n";
    exit;
}

echo "Simulating login as:\n";
echo "  User ID: " . $user->id . "\n";
echo "  User Email: " . $user->email . "\n";
echo "  Teacher ID (user_pk_id): " . $user->user_pk_id . "\n\n";

$teacherId = $user->user_pk_id;
$teacher = App\Models\Teacher::find($teacherId);

if (!$teacher) {
    echo "❌ Teacher record not found\n";
    exit;
}

echo "Teacher Details:\n";
echo "  Name: " . $teacher->first_name . " " . $teacher->last_name . "\n\n";

// Test accessing section 7
echo "Testing Access to Section 7:\n";
echo "-------------------------------------\n";

$sectionAssignment = App\Models\AcademicYearStrandSection::with(['section', 'strand', 'academicYear'])
    ->where('id', 7)
    ->where('adviser_teacher_id', $teacherId)
    ->first();

if ($sectionAssignment) {
    echo "✅ SUCCESS! Can access section 7\n\n";
    echo "Section Details:\n";
    echo "  ID: " . $sectionAssignment->id . "\n";
    echo "  Section: " . ($sectionAssignment->section->name ?? 'N/A') . "\n";
    echo "  Grade: " . ($sectionAssignment->section->grade ?? 'N/A') . "\n";
    echo "  Strand: " . ($sectionAssignment->strand->code ?? 'N/A') . "\n";
    echo "  Academic Year: " . ($sectionAssignment->academicYear->name ?? 'N/A') . "\n\n";
    
    // Get students
    $students = App\Models\StudentEnrollment::with(['student'])
        ->where('academic_year_strand_section_id', $sectionAssignment->id)
        ->get();
    
    $total = $students->count();
    $male = $students->filter(fn($e) => strtolower($e->student->gender ?? '') === 'male')->count();
    $female = $students->filter(fn($e) => strtolower($e->student->gender ?? '') === 'female')->count();
    
    echo "Student Statistics:\n";
    echo "  Total: " . $total . "\n";
    echo "  Male: " . $male . "\n";
    echo "  Female: " . $female . "\n\n";
    
    echo "✅ The fix is working! Section page will load successfully.\n";
} else {
    echo "❌ FAILED! Cannot access section 7\n";
    echo "  This teacher (ID: $teacherId) is not the adviser of section 7\n\n";
    
    // Show what sections this teacher CAN access
    $mySections = App\Models\AcademicYearStrandSection::with(['section', 'strand'])
        ->where('adviser_teacher_id', $teacherId)
        ->where('is_active', true)
        ->get();
    
    if ($mySections->isEmpty()) {
        echo "  This teacher has no advised sections.\n";
    } else {
        echo "  This teacher can access these sections:\n";
        foreach ($mySections as $sec) {
            echo "    - ID: " . $sec->id . " - Grade " . ($sec->section->grade ?? '?') . " Section " . ($sec->section->name ?? '?') . " (" . ($sec->strand->code ?? '?') . ")\n";
        }
    }
}

echo "\n====================================\n";
echo "Test Complete!\n";
