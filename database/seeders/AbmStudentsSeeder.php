<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Strand;
use Illuminate\Support\Facades\Hash;

class AbmStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active academic year or first available
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        
        if (!$academicYear) {
            $this->command->error('No academic year found. Please create an academic year first.');
            return;
        }

        // Get ABM strand
        $abmStrand = Strand::where('code', 'ABM')->first();
        
        if (!$abmStrand) {
            $this->command->error('ABM strand not found. Please create ABM strand first.');
            return;
        }

        // Filipino first names
        $maleFirstNames = [
            'Juan', 'Jose', 'Miguel', 'Gabriel', 'Rafael', 'Carlos', 'Antonio', 'Manuel', 
            'Pedro', 'Luis', 'Angelo', 'Mark', 'John', 'Joshua', 'James', 'Daniel',
            'Christian', 'Ryan', 'Kenneth', 'Michael', 'David', 'Joseph', 'Paulo', 'Rico',
            'Carlo', 'Andrei', 'Kyle', 'Jericho', 'Emmanuel', 'Francis', 'Jerome', 'Adrian'
        ];

        $femaleFirstNames = [
            'Maria', 'Ana', 'Sofia', 'Isabella', 'Gabriela', 'Andrea', 'Camila', 'Valeria',
            'Angela', 'Christine', 'Michelle', 'Nicole', 'Sarah', 'Ashley', 'Angel', 'Kim',
            'Kristine', 'Catherine', 'Princess', 'Queen', 'Jamaica', 'Precious', 'Grace', 'Faith',
            'Hope', 'Joy', 'Love', 'Angelica', 'Patricia', 'Samantha', 'Stephanie', 'Jessica'
        ];

        $middleNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres',
            'Flores', 'Rivera', 'Gonzales', 'Ramos', 'Castillo', 'Aquino', 'Jimenez', 'Morales',
            'Villanueva', 'Domingo', 'Santiago', 'Hernandez', 'Gutierrez', 'Luna', 'Castro', 'Diaz'
        ];

        $lastNames = [
            'Dela Cruz', 'Garcia', 'Reyes', 'Ramos', 'Mendoza', 'Santos', 'Flores', 'Gonzales',
            'Bautista', 'Villanueva', 'Rivera', 'Santiago', 'Cruz', 'Torres', 'Castro', 'Morales',
            'Hernandez', 'Lopez', 'Perez', 'Rodriguez', 'Martinez', 'Fernandez', 'Valdez', 'Aquino',
            'Diaz', 'Navarro', 'Pascual', 'Tolentino', 'De Guzman', 'Salazar', 'Robles', 'Mercado',
            'Soriano', 'Valencia', 'Mejia', 'Aguilar', 'Ramirez', 'Francisco', 'Vargas', 'Marquez'
        ];

        $suffixes = [null, null, null, null, null, 'Jr.', 'II', 'III'];
        
        $gradeLevels = ['Grade 11', 'Grade 12'];
        $statuses = ['active', 'active', 'active', 'active', 'graduated', 'dropped'];

        // Streets for addresses
        $streets = [
            'Rizal Street', 'Bonifacio Avenue', 'Aguinaldo Road', 'Luna Street', 'Mabini Avenue',
            'Del Pilar Street', 'Quezon Boulevard', 'Burgos Street', 'Roxas Avenue', 'Magsaysay Street',
            'Bonifacio Street', 'Ayala Avenue', 'Taft Avenue', 'Espana Boulevard', 'Quirino Avenue'
        ];

        $barangays = [
            'Poblacion', 'San Isidro', 'San Antonio', 'Santa Cruz', 'San Jose', 'San Miguel',
            'Santa Maria', 'Santo Niño', 'Barangay 1', 'Barangay 2', 'Barangay 3', 'San Pedro'
        ];

        $cities = [
            'Manila', 'Quezon City', 'Caloocan', 'Makati', 'Pasig', 'Taguig', 'Marikina',
            'Parañaque', 'Las Piñas', 'Muntinlupa', 'Malabon', 'Navotas', 'Valenzuela', 'Mandaluyong'
        ];

        $this->command->info('Creating 21 ABM sample student accounts...');

        // Get the highest existing student number
        $latestStudent = Student::orderBy('student_number', 'desc')->first();
        $startNumber = 4; // Default starting number
        $currentYear = date('Y');
        
        if ($latestStudent && preg_match('/(\d{4})-(\d+)/', $latestStudent->student_number, $matches)) {
            $year = $matches[1];
            $number = (int)$matches[2];
            
            // If same year, increment from latest, otherwise start from 1
            if ($year == $currentYear) {
                $startNumber = $number + 1;
            } else {
                $startNumber = 1;
            }
        }

        for ($i = 0; $i < 21; $i++) {
            $studentNum = $startNumber + $i;
            
            // Determine gender (roughly 50/50 split)
            $gender = $i % 2 == 0 ? 'female' : 'male';
            
            // Select names based on gender
            $firstName = $gender === 'male' 
                ? $maleFirstNames[array_rand($maleFirstNames)]
                : $femaleFirstNames[array_rand($femaleFirstNames)];
            
            $middleName = $middleNames[array_rand($middleNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $suffix = $suffixes[array_rand($suffixes)];
            
            // Generate student number (format: YYYY-XXXXX with 5 digits)
            $studentNumber = $currentYear . '-' . str_pad($studentNum, 5, '0', STR_PAD_LEFT);
            
            // Generate birthdate (16-19 years old)
            $birthYear = date('Y') - rand(16, 19);
            $birthMonth = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $birthDay = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
            $birthdate = "{$birthYear}-{$birthMonth}-{$birthDay}";
            
            // Generate email
            $emailFirstName = strtolower(str_replace(' ', '', $firstName));
            $emailLastName = strtolower(str_replace(' ', '', $lastName));
            $email = "{$emailFirstName}.{$emailLastName}{$studentNum}@smac.edu.ph";
            
            // Generate mobile number (Philippine format)
            $mobileNumber = '09' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
            
            // Generate address
            $street = $streets[array_rand($streets)];
            $barangay = $barangays[array_rand($barangays)];
            $city = $cities[array_rand($cities)];
            $address = rand(1, 999) . " {$street}, {$barangay}, {$city}, Metro Manila";
            
            // Guardian information
            $guardianLastName = $lastName; // Usually same last name
            $guardianFirstNames = $gender === 'male' 
                ? ['Roberto', 'Ricardo', 'Ramon', 'Rodrigo', 'Reynaldo', 'Ruben', 'Romeo', 'Rene']
                : ['Rosa', 'Rosita', 'Rowena', 'Rebecca', 'Ruth', 'Remedios', 'Raquel', 'Regina'];
            $guardianFirstName = $guardianFirstNames[array_rand($guardianFirstNames)];
            $guardianName = "{$guardianFirstName} {$guardianLastName}";
            $guardianContact = '09' . str_pad(rand(200000000, 999999999), 9, '0', STR_PAD_LEFT);
            $guardianEmail = strtolower(str_replace(' ', '', $guardianFirstName)) . ".{$emailLastName}{$studentNum}@gmail.com";
            
            // Use ABM strand
            $program = $abmStrand->code;
            
            // Select grade level
            $gradeLevel = $gradeLevels[array_rand($gradeLevels)];
            
            // Select status (mostly active)
            $status = $statuses[array_rand($statuses)];
            
            // Create student
            Student::create([
                'student_number' => $studentNumber,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'suffix' => $suffix,
                'gender' => $gender,
                'birthdate' => $birthdate,
                'email' => $email,
                'mobile_number' => $mobileNumber,
                'address' => $address,
                'guardian_name' => $guardianName,
                'guardian_contact' => $guardianContact,
                'guardian_email' => $guardianEmail,
                'program' => $program,
                'academic_year' => $gradeLevel,
                'academic_year_id' => $academicYear->id,
                'status' => $status,
                'profile_picture' => null,
                'generated_password_encrypted' => Hash::make('student123'), // Default password
            ]);

            $count = $i + 1;
            if ($count % 5 == 0) {
                $this->command->info("Created {$count} ABM students...");
            }
        }

        $this->command->info('Successfully created 21 ABM sample student accounts!');
        $this->command->info('Default password for all students: student123');
        $this->command->info('All students are assigned to ABM strand');
    }
}
