<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\User;
use App\Models\Strand;
use Illuminate\Support\Facades\Hash;

echo "=== Creating 10 Dummy TVL-ICT Student Accounts ===\n\n";

// Find TVL-ICT strand
$strand = Strand::where('code', 'TVL-ICT')->first();

if (!$strand) {
    echo "❌ TVL-ICT strand not found! Please create the strand first.\n";
    exit(1);
}

echo "✓ Found strand: {$strand->code} - {$strand->name}\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

if (!$activeYear) {
    echo "❌ No active academic year found! Please set an active academic year.\n";
    exit(1);
}

echo "✓ Found active academic year: {$activeYear->name}\n\n";

// Generate 10 students
$students = [
    ['first_name' => 'Mark', 'middle_name' => 'Santos', 'last_name' => 'Garcia', 'gender' => 'male'],
    ['first_name' => 'Anna', 'middle_name' => 'Cruz', 'last_name' => 'Reyes', 'gender' => 'female'],
    ['first_name' => 'John', 'middle_name' => 'Dela', 'last_name' => 'Cruz', 'gender' => 'male'],
    ['first_name' => 'Maria', 'middle_name' => 'Ramos', 'last_name' => 'Santos', 'gender' => 'female'],
    ['first_name' => 'Carlos', 'middle_name' => 'Bautista', 'last_name' => 'Mendoza', 'gender' => 'male'],
    ['first_name' => 'Sarah', 'middle_name' => 'Torres', 'last_name' => 'Villanueva', 'gender' => 'female'],
    ['first_name' => 'Miguel', 'middle_name' => 'Lopez', 'last_name' => 'Fernandez', 'gender' => 'male'],
    ['first_name' => 'Katrina', 'middle_name' => 'Rivera', 'last_name' => 'Gomez', 'gender' => 'female'],
    ['first_name' => 'Joshua', 'middle_name' => 'Martinez', 'last_name' => 'Aquino', 'gender' => 'male'],
    ['first_name' => 'Patricia', 'middle_name' => 'Flores', 'last_name' => 'Castillo', 'gender' => 'female'],
];

$createdStudents = [];
$createdCount = 0;

foreach ($students as $index => $studentData) {
    try {
        // Generate student number
        $studentNumber = 'SMAC-2025-TVL-ICT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
        
        // Generate email
        $email = strtolower($studentData['first_name'] . '.' . $studentData['last_name']) . '@tvlict.smac.edu.ph';
        
        // Check if student already exists
        $existingStudent = Student::where('student_number', $studentNumber)->first();
        if ($existingStudent) {
            echo "⚠ Student {$studentNumber} already exists. Skipping...\n";
            continue;
        }
        
        // Check if email already exists
        $existingEmail = Student::where('email', $email)->first();
        if ($existingEmail) {
            $email = strtolower($studentData['first_name'] . '.' . $studentData['last_name']) . $index . '@tvlict.smac.edu.ph';
        }
        
        // Create student
        $student = Student::create([
            'student_number' => $studentNumber,
            'first_name' => $studentData['first_name'],
            'middle_name' => $studentData['middle_name'],
            'last_name' => $studentData['last_name'],
            'gender' => $studentData['gender'],
            'email' => $email,
            'mobile_number' => '09' . rand(100000000, 999999999),
            'birthdate' => '2008-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
            'address' => 'Sample Address, City, Philippines',
            'guardian_name' => 'Guardian of ' . $studentData['first_name'],
            'guardian_contact' => '09' . rand(100000000, 999999999),
            'guardian_email' => 'guardian.' . strtolower($studentData['first_name']) . $index . '@email.com',
            'program' => 'TVL-ICT',
            'academic_year' => $activeYear->name,
            'academic_year_id' => $activeYear->id,
            'status' => 'active',
        ]);
        
        // Create user account for the student
        $user = User::create([
            'name' => $student->first_name . ' ' . $student->last_name,
            'email' => $email,
            'password' => Hash::make('password123'), // Default password
            'type' => 'student',
            'user_pk_id' => $student->id,
            'status' => 'active',
        ]);
        
        $createdStudents[] = [
            'student_number' => $studentNumber,
            'name' => $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name,
            'email' => $email,
            'password' => 'password123'
        ];
        
        $createdCount++;
        echo "✓ Created: {$studentNumber} - {$student->first_name} {$student->last_name}\n";
        
    } catch (\Exception $e) {
        echo "❌ Error creating student {$studentData['first_name']} {$studentData['last_name']}: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "SUMMARY: Created {$createdCount} TVL-ICT student accounts\n";
echo str_repeat("=", 70) . "\n\n";

if (count($createdStudents) > 0) {
    echo "LOGIN CREDENTIALS:\n";
    echo str_repeat("-", 70) . "\n";
    printf("%-25s %-30s %-15s\n", "Student Number", "Name", "Password");
    echo str_repeat("-", 70) . "\n";
    
    foreach ($createdStudents as $student) {
        printf("%-25s %-30s %-15s\n", 
            $student['student_number'], 
            substr($student['name'], 0, 29), 
            $student['password']
        );
        echo "Email: {$student['email']}\n";
        echo str_repeat("-", 70) . "\n";
    }
}

echo "\n✅ Done! All students can login with their email and password: password123\n";
