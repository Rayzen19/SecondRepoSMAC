<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;

class UpdateToPhilippineDataSeeder extends Seeder
{
    private $philippineFirstNames = [
        'male' => ['Juan', 'Jose', 'Carlos', 'Miguel', 'Rafael', 'Antonio', 'Francisco', 'Pedro', 'Luis', 'Gabriel', 'Daniel', 'Samuel', 'Mateo', 'Lucas', 'Diego', 'Pablo', 'Marco', 'Angelo', 'Christian', 'Joshua'],
        'female' => ['Maria', 'Ana', 'Isabel', 'Sofia', 'Carmen', 'Rosa', 'Elena', 'Patricia', 'Angela', 'Teresa', 'Monica', 'Lucia', 'Andrea', 'Cristina', 'Julia', 'Diana', 'Sandra', 'Laura', 'Camila', 'Gabriela']
    ];

    private $philippineLastNames = [
        'Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Lopez', 'Gonzales', 'Rodriguez',
        'Perez', 'Ramos', 'Flores', 'Rivera', 'Gomez', 'Fernandez', 'De Leon', 'Manalo', 'Villanueva', 'Castro',
        'Del Rosario', 'San Jose', 'Aquino', 'Mercado', 'Aguilar', 'Domingo', 'Santiago', 'Valdez', 'Soriano', 'Morales'
    ];

    private $caviteAddresses = [
        'Brgy. San Antonio, Dasmariñas, Cavite',
        'Brgy. Paliparan, Dasmariñas, Cavite',
        'Brgy. Salitran, Dasmariñas, Cavite',
        'Brgy. Langkaan, Dasmariñas, Cavite',
        'Brgy. Victoria Reyes, Dasmariñas, Cavite',
        'Brgy. Zone I, Dasmariñas, Cavite',
        'Brgy. Burol, Dasmariñas, Cavite',
        'Brgy. Emmanuel, Dasmariñas, Cavite',
        'Block 1 Lot 5, Villa Verde Subdivision, Dasmariñas, Cavite',
        'Block 3 Lot 12, Greenwoods Executive Village, Dasmariñas, Cavite',
        'Unit 204, Garden Homes, Dasmariñas, Cavite',
        'Phase 2 Block 8 Lot 15, Cambridge Village, Dasmariñas, Cavite',
        'Brgy. San Agustin, Dasmariñas, Cavite',
        'Brgy. H. Caguioa, Dasmariñas, Cavite',
        'Brgy. Sabang, Dasmariñas, Cavite',
        '123 Aguinaldo Highway, Dasmariñas, Cavite',
        'Purok 3, Brgy. San Isidro, Dasmariñas, Cavite',
        'Sitio Maligaya, Brgy. Sampaloc, Dasmariñas, Cavite',
        'Block 7 Lot 20, La Residencia, Dasmariñas, Cavite',
        'Phase 1 Block 4 Lot 8, The Prestige Subdivision, Dasmariñas, Cavite'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🇵🇭 Starting Philippine data update...');

        // Update Students
        $this->updateStudents();

        // Update Teachers
        $this->updateTeachers();

        // Update Guardians
        $this->updateGuardians();

        $this->command->info('✅ All data updated to Philippine format!');
    }

    private function updateStudents(): void
    {
        $this->command->info('Updating students...');
        
        $students = Student::all();
        
        foreach ($students as $student) {
            $gender = $student->gender === 'female' ? 'female' : 'male';
            
            $student->update([
                'first_name' => $this->randomFirstName($gender),
                'middle_name' => $this->randomFirstName($gender),
                'last_name' => $this->randomLastName(),
                'mobile_number' => $this->generatePhilippinePhone(),
                'address' => $this->randomAddress(),
                'guardian_name' => $this->randomFullName(),
                'guardian_contact' => $this->generatePhilippinePhone(),
            ]);
        }

        $this->command->info("✓ Updated {$students->count()} students");
    }

    private function updateTeachers(): void
    {
        $this->command->info('Updating teachers...');
        
        $teachers = Teacher::all();
        
        foreach ($teachers as $teacher) {
            $gender = $teacher->gender === 'female' ? 'female' : 'male';
            
            $teacher->update([
                'first_name' => $this->randomFirstName($gender),
                'middle_name' => $this->randomFirstName($gender),
                'last_name' => $this->randomLastName(),
                'phone' => $this->generatePhilippinePhone(),
                'address' => $this->randomAddress(),
            ]);
        }

        $this->command->info("✓ Updated {$teachers->count()} teachers");
    }

    private function updateGuardians(): void
    {
        $this->command->info('Updating guardians...');
        
        $guardians = Guardian::all();
        
        foreach ($guardians as $guardian) {
            $gender = $guardian->gender === 'female' ? 'female' : 'male';
            
            $guardian->update([
                'first_name' => $this->randomFirstName($gender),
                'middle_name' => $this->randomFirstName($gender),
                'last_name' => $this->randomLastName(),
                'mobile_number' => $this->generatePhilippinePhone(),
                'address' => $this->randomAddress(),
            ]);
        }

        $this->command->info("✓ Updated {$guardians->count()} guardians");
    }

    private function randomFirstName(string $gender): string
    {
        $names = $this->philippineFirstNames[$gender] ?? $this->philippineFirstNames['male'];
        return $names[array_rand($names)];
    }

    private function randomLastName(): string
    {
        return $this->philippineLastNames[array_rand($this->philippineLastNames)];
    }

    private function randomFullName(): string
    {
        $gender = ['male', 'female'][array_rand(['male', 'female'])];
        return $this->randomFirstName($gender) . ' ' . $this->randomLastName();
    }

    private function randomAddress(): string
    {
        return $this->caviteAddresses[array_rand($this->caviteAddresses)];
    }

    private function generatePhilippinePhone(): string
    {
        // Generate Philippine mobile number format: 09XX-XXX-XXXX
        $prefix = ['09' . rand(10, 99)]; // 0910-0999
        $middle = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        $last = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix[0] . $middle . $last;
    }
}
