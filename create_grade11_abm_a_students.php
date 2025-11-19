<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Get the active academic year
$activeYear = AcademicYear::where('is_active', true)->first();
if (!$activeYear) {
    die("Error: No active academic year found. Please set an active academic year first.\n");
}

echo "=== Creating Grade 11 ABM-A Student Accounts ===\n";
echo "Using active academic year: {$activeYear->display_name}\n\n";

// Student names in format: Last Name, First Name Middle Name
$students = [
    ['lastname' => 'Alejandro', 'firstname' => 'Brix Nathan', 'middlename' => 'Esparagoza'],
    ['lastname' => 'Benamir', 'firstname' => 'Romar', 'middlename' => 'Aledia'],
    ['lastname' => 'Cabujat', 'firstname' => 'Anito', 'middlename' => 'Sablawon'],
    ['lastname' => 'Clemente', 'firstname' => 'Anthony Josh', 'middlename' => 'Ambrocio'],
    ['lastname' => 'De Luna', 'firstname' => 'Rexter', 'middlename' => 'Lisada'],
    ['lastname' => 'Dela Cruz', 'firstname' => 'Mark Jiro', 'middlename' => 'Jabas'],
    ['lastname' => 'Dulman', 'firstname' => 'Mark Adrian', 'middlename' => 'Silva'],
    ['lastname' => 'Gabrino', 'firstname' => 'Max Allan', 'middlename' => 'Pingol'],
    ['lastname' => 'Gicoso', 'firstname' => 'Larry', 'middlename' => ''],
    ['lastname' => 'Legaspi', 'firstname' => 'Rheimar', 'middlename' => 'Aquino'],
    ['lastname' => 'Mariano', 'firstname' => 'Laurence', 'middlename' => 'Candelaria'],
    ['lastname' => 'Nava', 'firstname' => 'Jhonn David', 'middlename' => 'Icio'],
    ['lastname' => 'Ngoho', 'firstname' => 'Marc Paul', 'middlename' => 'Menendez'],
    ['lastname' => 'Perez', 'firstname' => 'Vyxzen', 'middlename' => 'De Lima'],
    ['lastname' => 'Saparon', 'firstname' => 'Winston', 'middlename' => 'Pelayo'],
    ['lastname' => 'Sarip', 'firstname' => 'Jibren', 'middlename' => ''],
    ['lastname' => 'Satombo', 'firstname' => 'Sean Jheremt', 'middlename' => 'Dela Cruz'],
    ['lastname' => 'Trinidad', 'firstname' => 'Andrey', 'middlename' => 'Bustos'],
    ['lastname' => 'Tymico', 'firstname' => 'Justine', 'middlename' => 'Sandrino'],
    ['lastname' => 'Vergara', 'firstname' => 'Edrich', 'middlename' => 'Mikael'],
];

// Get the latest student number to continue from
$lastStudent = Student::orderBy('student_number', 'desc')->first();
$startingNumber = 1;
if ($lastStudent && preg_match('/STU-(\d+)/', $lastStudent->student_number, $matches)) {
    $startingNumber = intval($matches[1]) + 1;
}

$createdCount = 0;
$errorCount = 0;
$accountDetails = [];

echo "Starting student number: STU-" . str_pad($startingNumber, 6, '0', STR_PAD_LEFT) . "\n\n";

foreach ($students as $index => $studentData) {
    try {
        $studentNumber = 'STU-' . str_pad($startingNumber + $index, 6, '0', STR_PAD_LEFT);
        $firstName = $studentData['firstname'];
        $lastName = $studentData['lastname'];
        $middleName = $studentData['middlename'];
        
        // Generate email from first name and last name
        $emailName = strtolower(str_replace(' ', '', $firstName . '.' . $lastName));
        $emailName = preg_replace('/[^a-z0-9.]/', '', $emailName); // Remove special characters
        $email = $emailName . '@student.newsmac.edu.ph';
        
        // Check if email already exists
        $emailExists = User::where('email', $email)->exists();
        if ($emailExists) {
            $email = $emailName . rand(1, 99) . '@student.newsmac.edu.ph';
        }
        
        // Generate unique password for each student (FirstnameLastname + random 4 digits)
        $passwordBase = ucfirst(strtolower(str_replace(' ', '', $firstName))) . ucfirst(strtolower(str_replace(' ', '', explode(' ', $lastName)[0])));
        $password = $passwordBase . rand(1000, 9999);
        
        // Create user account first
        $fullName = $firstName . ' ' . $lastName;
        $user = User::create([
            'name' => $fullName,
            'email' => $email,
            'password' => Hash::make($password),
            'user_type' => 'student',
            'email_verified_at' => now(),
        ]);
        
        // Generate placeholder data
        $birthdate = date('Y-m-d', strtotime('-17 years')); // Assume 17 years old
        $mobileNumber = '09' . rand(100000000, 999999999);
        $guardianContact = '09' . rand(100000000, 999999999);
        
        // Make sure guardian contact is unique
        while (Student::where('guardian_contact', $guardianContact)->exists()) {
            $guardianContact = '09' . rand(100000000, 999999999);
        }
        
        $guardianEmail = strtolower('guardian.' . str_replace(' ', '', $firstName . $lastName)) . '@email.com';
        
        // Make sure guardian email is unique
        $emailCounter = 1;
        while (Student::where('guardian_email', $guardianEmail)->exists()) {
            $guardianEmail = strtolower('guardian.' . str_replace(' ', '', $firstName . $lastName . $emailCounter)) . '@email.com';
            $emailCounter++;
        }
        
        // Make sure mobile number is unique
        while (Student::where('mobile_number', $mobileNumber)->exists()) {
            $mobileNumber = '09' . rand(100000000, 999999999);
        }
        
        // Create student record
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => $studentNumber,
            'first_name' => $firstName,
            'middle_name' => $middleName ?: null,
            'last_name' => $lastName,
            'suffix' => null,
            'gender' => 'male', // Default, can be updated later
            'birthdate' => $birthdate,
            'email' => $email,
            'mobile_number' => $mobileNumber,
            'guardian_contact' => $guardianContact,
            'guardian_email' => $guardianEmail,
            'address' => 'Cavite', // Placeholder address
            'program' => 'ABM',
            'academic_year' => 'G-11',
            'academic_year_id' => $activeYear->id,
            'status' => 'active',
        ]);
        
        $createdCount++;
        $accountDetails[] = [
            'number' => $createdCount,
            'student_number' => $studentNumber,
            'name' => $fullName,
            'email' => $email,
            'password' => $password,
        ];
        
        echo "✓ [{$createdCount}/20] Created: {$studentNumber} - {$fullName}\n";
        echo "   Email: {$email}\n";
        echo "   Password: {$password}\n\n";
        
    } catch (\Exception $e) {
        $errorCount++;
        echo "✗ Error creating student {$studentData['firstname']} {$studentData['lastname']}: " . $e->getMessage() . "\n\n";
    }
}

// Display summary
echo "\n" . str_repeat("=", 80) . "\n";
echo "ACCOUNT CREATION SUMMARY\n";
echo str_repeat("=", 80) . "\n\n";

if ($createdCount > 0) {
    echo "Successfully created {$createdCount} Grade 11 ABM-A student accounts:\n\n";
    
    foreach ($accountDetails as $account) {
        echo "{$account['number']}. {$account['name']}\n";
        echo "   Student Number: {$account['student_number']}\n";
        echo "   Email: {$account['email']}\n";
        echo "   Password: {$account['password']}\n";
        echo "   Grade Level: Grade 11\n";
        echo "   Program: ABM\n";
        echo "   Section: ABM-A\n\n";
    }
}

if ($errorCount > 0) {
    echo "\n✗ Errors encountered: {$errorCount}\n";
}

echo str_repeat("=", 80) . "\n";
echo "NOTE: Students need to be enrolled in a section separately.\n";
echo "Use the admin panel to assign them to section 'ABM-A'.\n";
echo str_repeat("=", 80) . "\n";
