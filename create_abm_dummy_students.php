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

echo "Creating 40 dummy ABM student accounts...\n";
echo "Using active academic year: {$activeYear->display_name}\n\n";

// Filipino first names
$firstNames = [
    'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Miguel', 'Carmen', 'Luis', 'Elena',
    'Carlos', 'Sofia', 'Ramon', 'Isabel', 'Antonio', 'Lucia', 'Manuel', 'Teresa', 'Fernando', 'Beatriz',
    'Ricardo', 'Patricia', 'Roberto', 'Angela', 'Diego', 'Monica', 'Alejandro', 'Cristina', 'Pablo', 'Diana',
    'Andres', 'Laura', 'Rafael', 'Gabriela', 'Jorge', 'Natalia', 'Francisco', 'Valeria', 'Eduardo', 'Sandra',
    'Daniel', 'Melissa', 'Gabriel', 'Andrea', 'Victor', 'Carolina', 'Oscar', 'Jasmine', 'Emilio', 'Nicole'
];

// Filipino last names
$lastNames = [
    'Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Gonzales', 'Ramos', 'Flores', 'Mendoza', 'Torres',
    'Rivera', 'Castillo', 'Gomez', 'Morales', 'Sanchez', 'Ramirez', 'Dela Cruz', 'Fernandez', 'Lopez', 'Hernandez',
    'Villanueva', 'Castro', 'Domingo', 'Santiago', 'Aquino', 'Pascual', 'Marquez', 'Valdez', 'Salazar', 'Miranda',
    'De Leon', 'Aguilar', 'Navarro', 'Perez', 'Jimenez', 'Ortega', 'Diaz', 'Rojas', 'Gutierrez', 'Vargas'
];

$middleNames = [
    'Antonio', 'Jose', 'Luis', 'Miguel', 'Carlos', 'Rafael', 'Angel', 'Manuel', 'Pedro', 'Francisco',
    'Maria', 'Rosa', 'Ana', 'Carmen', 'Luz', 'Isabel', 'Teresa', 'Elena', 'Sofia', 'Victoria'
];

$municipalities = [
    'Bacoor City', 'Dasmariñas City', 'Imus City', 'Cavite City', 'General Trias City',
    'Tagaytay City', 'Trece Martires City', 'Silang', 'Carmona', 'Naic'
];

$barangays = [
    'Poblacion', 'San Agustin', 'San Jose', 'San Nicolas', 'Santa Cruz',
    'Bagong Silang', 'Malagasang', 'Palico', 'Salinas', 'Tabing Dagat'
];

// Get the latest student number to continue from
$lastStudent = Student::orderBy('student_number', 'desc')->first();
$startingNumber = 1;
if ($lastStudent && preg_match('/STU-(\d+)/', $lastStudent->student_number, $matches)) {
    $startingNumber = intval($matches[1]) + 1;
}

$createdCount = 0;
$errorCount = 0;

for ($i = 0; $i < 40; $i++) {
    try {
        $studentNumber = 'STU-' . str_pad($startingNumber + $i, 6, '0', STR_PAD_LEFT);
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $middleName = $middleNames[array_rand($middleNames)];
        $gender = rand(0, 1) ? 'male' : 'female';
        $municipality = $municipalities[array_rand($municipalities)];
        $barangay = $barangays[array_rand($barangays)];
        $gradeLevel = rand(0, 1) ? 'G-11' : 'G-12'; // Random grade 11 or 12
        
        // Generate email
        $emailName = strtolower(str_replace(' ', '', $firstName . '.' . $lastName . rand(1, 999)));
        $email = $emailName . '@student.newsmac.edu.ph';
        
        // Create user account first
        $user = User::create([
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'password' => Hash::make('password123'), // Default password
            'user_type' => 'student',
            'email_verified_at' => now(),
        ]);
        
        // Create student record
        $birthdate = date('Y-m-d', strtotime('-' . rand(16, 18) . ' years'));
        $mobileNumber = '09' . rand(100000000, 999999999);
        $guardianContact = '09' . rand(100000000, 999999999);
        $guardianEmail = strtolower('guardian.' . str_replace(' ', '', $firstName . $lastName . rand(1, 99))) . '@email.com';
        
        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => $studentNumber,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'suffix' => null,
            'gender' => $gender,
            'birthdate' => $birthdate,
            'email' => $email,
            'mobile_number' => $mobileNumber,
            'guardian_contact' => $guardianContact,
            'guardian_email' => $guardianEmail,
            'address' => rand(1, 999) . ' ' . $barangay,
            'program' => 'ABM', // ABM Strand
            'academic_year' => $gradeLevel, // Grade level
            'academic_year_id' => $activeYear->id, // Active academic year
            'status' => 'active',
        ]);
        
        $createdCount++;
        echo "✓ Created: {$studentNumber} - {$firstName} {$lastName} ({$gender}, {$gradeLevel})\n";
        echo "  Email: {$email} | Password: password123\n";
        
    } catch (\Exception $e) {
        $errorCount++;
        echo "✗ Error creating student #{$i}: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Summary:\n";
echo "✓ Successfully created: {$createdCount} ABM students\n";
if ($errorCount > 0) {
    echo "✗ Errors: {$errorCount}\n";
}
echo "\nAll students have the default password: password123\n";
echo "Students are assigned to ABM strand with random grade levels (G-11 or G-12)\n";
echo str_repeat("=", 60) . "\n";

