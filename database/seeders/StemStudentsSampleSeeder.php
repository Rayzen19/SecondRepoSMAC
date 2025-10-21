<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\Student;
use App\Models\User;
use App\Models\AcademicYear;
use Carbon\Carbon;

class StemStudentsSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 20 STEM student account samples
     */
    public function run(): void
    {
        // Get current active academic year or create one
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::first() ?? AcademicYear::create([
                'name' => '2025-2026',
                'semester' => '1st',
                'academic_status' => 'ongoing school year',
                'is_active' => true,
            ]);
        }

        $year = now()->year;
        
        // Get the last student number for this year
        $lastStudentNumber = Student::withTrashed()
            ->where('student_number', 'like', $year . '-%')
            ->orderByDesc('student_number')
            ->value('student_number');

        $lastSeq = 0;
        if ($lastStudentNumber) {
            $parts = explode('-', $lastStudentNumber, 2);
            $lastSeq = isset($parts[1]) ? (int) $parts[1] : 0;
        }

        // Sample Filipino names for STEM students
        $students = [
            ['first_name' => 'Maria', 'middle_name' => 'Santos', 'last_name' => 'Cruz', 'gender' => 'female'],
            ['first_name' => 'Juan', 'middle_name' => 'Dela', 'last_name' => 'Cruz', 'gender' => 'male'],
            ['first_name' => 'Sofia', 'middle_name' => 'Reyes', 'last_name' => 'Garcia', 'gender' => 'female'],
            ['first_name' => 'Miguel', 'middle_name' => 'Ramos', 'last_name' => 'Santos', 'gender' => 'male'],
            ['first_name' => 'Isabella', 'middle_name' => 'Torres', 'last_name' => 'Reyes', 'gender' => 'female'],
            ['first_name' => 'Gabriel', 'middle_name' => 'Flores', 'last_name' => 'Mendoza', 'gender' => 'male'],
            ['first_name' => 'Mia', 'middle_name' => 'Gonzales', 'last_name' => 'Rivera', 'gender' => 'female'],
            ['first_name' => 'Luis', 'middle_name' => 'Castro', 'last_name' => 'Morales', 'gender' => 'male'],
            ['first_name' => 'Ana', 'middle_name' => 'Valdez', 'last_name' => 'Ortega', 'gender' => 'female'],
            ['first_name' => 'Carlos', 'middle_name' => 'Navarro', 'last_name' => 'Ramos', 'gender' => 'male'],
            ['first_name' => 'Elena', 'middle_name' => 'Domingo', 'last_name' => 'Fernandez', 'gender' => 'female'],
            ['first_name' => 'Diego', 'middle_name' => 'Santiago', 'last_name' => 'Lopez', 'gender' => 'male'],
            ['first_name' => 'Camila', 'middle_name' => 'Aquino', 'last_name' => 'Martinez', 'gender' => 'female'],
            ['first_name' => 'Rafael', 'middle_name' => 'Alvarez', 'last_name' => 'Perez', 'gender' => 'male'],
            ['first_name' => 'Valentina', 'middle_name' => 'Jimenez', 'last_name' => 'Sanchez', 'gender' => 'female'],
            ['first_name' => 'Adrian', 'middle_name' => 'Ramirez', 'last_name' => 'Gomez', 'gender' => 'male'],
            ['first_name' => 'Luna', 'middle_name' => 'Castillo', 'last_name' => 'Torres', 'gender' => 'female'],
            ['first_name' => 'Sebastian', 'middle_name' => 'Herrera', 'last_name' => 'Diaz', 'gender' => 'male'],
            ['first_name' => 'Chloe', 'middle_name' => 'Vargas', 'last_name' => 'Aguilar', 'gender' => 'female'],
            ['first_name' => 'Nathan', 'middle_name' => 'Mendez', 'last_name' => 'Castro', 'gender' => 'male'],
        ];

        echo "\n========================================\n";
        echo "Creating 20 STEM Student Accounts\n";
        echo "========================================\n\n";

        foreach ($students as $index => $studentData) {
            $lastSeq++;
            $studentNumber = $year . '-' . str_pad($lastSeq, 5, '0', STR_PAD_LEFT);
            
            // Generate email and mobile
            $emailName = strtolower($studentData['first_name'] . '.' . $studentData['last_name']);
            $email = $emailName . rand(100, 999) . '@stem.student.test';
            $mobileNumber = '+639' . rand(100000000, 999999999);
            
            // Generate guardian info
            $guardianName = ($studentData['gender'] === 'male' ? 'Mr. ' : 'Mrs. ') . $studentData['last_name'];
            $guardianContact = '+639' . rand(100000000, 999999999);
            $guardianEmail = strtolower($studentData['last_name']) . '.guardian' . rand(100, 999) . '@test.com';
            
            // Generate random birthdate (15-18 years old)
            $birthdate = Carbon::now()->subYears(rand(15, 18))->subDays(rand(1, 365))->format('Y-m-d');
            
            // Generate password
            $plainPassword = 'Stem@' . rand(1000, 9999);
            $encryptedPassword = Crypt::encryptString($plainPassword);
            
            // Create student record
            $student = Student::create([
                'student_number' => $studentNumber,
                'first_name' => $studentData['first_name'],
                'middle_name' => $studentData['middle_name'],
                'last_name' => $studentData['last_name'],
                'suffix' => null,
                'gender' => $studentData['gender'],
                'birthdate' => $birthdate,
                'email' => $email,
                'mobile_number' => $mobileNumber,
                'address' => 'Sample Address, City, Province',
                'guardian_name' => $guardianName,
                'guardian_contact' => $guardianContact,
                'guardian_email' => $guardianEmail,
                'program' => 'STEM',
                'academic_year' => $academicYear->name,
                'academic_year_id' => $academicYear->id,
                'status' => 'active',
                'profile_picture' => null,
                'generated_password_encrypted' => $encryptedPassword,
            ]);

            // Create corresponding user account
            User::create([
                'name' => $studentData['first_name'] . ' ' . $studentData['last_name'],
                'email' => $email,
                'password' => Hash::make($plainPassword),
                'type' => 'student',
                'user_pk_id' => $student->id,
                'email_verified_at' => now(),
            ]);

            echo sprintf(
                "%2d. %-20s | %s | %s | %s\n",
                $index + 1,
                $studentData['first_name'] . ' ' . $studentData['last_name'],
                $studentNumber,
                $email,
                'Password: ' . $plainPassword
            );
        }

        echo "\n========================================\n";
        echo "✓ Successfully created 20 STEM students!\n";
        echo "========================================\n";
        echo "\nLogin Credentials:\n";
        echo "- Use the email and password shown above\n";
        echo "- All students are in STEM program\n";
        echo "- All students have 'active' status\n";
        echo "- Academic Year: " . $academicYear->name . "\n\n";
    }
}
