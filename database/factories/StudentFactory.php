<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use App\Models\Student;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        $ay = AcademicYear::inRandomOrder()->first() ?? AcademicYear::factory()->create();
        
        $year = substr($ay->name, 0, 4);
        $prefix = $year . '-';
        // Use a static in-memory cache to keep per-year counters during a single process/batch run
        static $seqCache = [];
        if (!array_key_exists($year, $seqCache)) {
            // Compute next sequential number based on existing student_number values with the same year prefix
            $existing = Student::withTrashed()->where('student_number', 'like', $prefix . '%')->pluck('student_number')->all();
            $maxSeq = 0;
            foreach ($existing as $sn) {
                $num = (int) substr($sn, strlen($prefix));
                if ($num > $maxSeq) {
                    $maxSeq = $num;
                }
            }
            $seqCache[$year] = $maxSeq; // start from current max
        }
        // increment for this new record
        $seqCache[$year]++;
        $student_number = $prefix . str_pad($seqCache[$year], 5, '0', STR_PAD_LEFT);
        // Fallback guard: if for any reason this candidate already exists (e.g., concurrent/separate processes), bump until free
        while (Student::withTrashed()->where('student_number', $student_number)->exists()) {
            $seqCache[$year]++;
            $student_number = $prefix . str_pad($seqCache[$year], 5, '0', STR_PAD_LEFT);
        }

        $gender = $this->faker->randomElement(['male', 'female']);
        $firstNames = [
            'male' => ['Juan', 'Jose', 'Carlos', 'Miguel', 'Rafael', 'Antonio', 'Francisco', 'Pedro', 'Luis', 'Gabriel', 'Daniel', 'Samuel', 'Mateo', 'Lucas', 'Diego'],
            'female' => ['Maria', 'Ana', 'Isabel', 'Sofia', 'Carmen', 'Rosa', 'Elena', 'Patricia', 'Angela', 'Teresa', 'Monica', 'Lucia', 'Andrea', 'Cristina', 'Julia']
        ];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Lopez', 'Gonzales', 'Rodriguez', 'Perez', 'Ramos', 'Flores', 'Rivera', 'Del Rosario'];
        $caviteAddresses = [
            'Brgy. San Antonio, Dasmariñas, Cavite',
            'Brgy. Paliparan, Dasmariñas, Cavite',
            'Block 1 Lot 5, Villa Verde Subdivision, Dasmariñas, Cavite',
            'Block 3 Lot 12, Greenwoods Executive Village, Dasmariñas, Cavite',
            'Phase 2 Block 8 Lot 15, Cambridge Village, Dasmariñas, Cavite',
            '123 Aguinaldo Highway, Dasmariñas, Cavite',
            'Purok 3, Brgy. San Isidro, Dasmariñas, Cavite'
        ];

        return [
            'student_number' => strtoupper($student_number),
            'first_name' => $firstNames[$gender][array_rand($firstNames[$gender])],
            'middle_name' => $firstNames[$gender][array_rand($firstNames[$gender])],
            'last_name' => $lastNames[array_rand($lastNames)],
            'suffix' => $this->faker->optional(0.1)->randomElement(['Jr.', 'Sr.', 'II', 'III']),
            'gender' => $gender,
            'birthdate' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_number' => '09' . $this->faker->numerify('#########'),
            'address' => $caviteAddresses[array_rand($caviteAddresses)],
            'guardian_name' => $firstNames[array_rand(['male', 'female'])][0] . ' ' . $lastNames[array_rand($lastNames)],
            'guardian_contact' => '09' . $this->faker->numerify('#########'),
            'guardian_email' => $this->faker->unique()->safeEmail(),
            'program' => $this->faker->randomElement(['BSCS', 'BSIT', 'BSEd']),
            'academic_year' => function () {
                $startYear = $this->faker->numberBetween(2000, date('Y'));
                return $startYear . '-' . ($startYear + 1);
            },
            'academic_year_id' => $ay->id,
            'status' => $this->faker->randomElement(['active', 'graduated', 'dropped']),
            'profile_picture' => null,
            // Keep created_at within the computed year to maintain consistency
            'created_at' => $this->faker->dateTimeBetween($year.'-01-01 00:00:00', $year.'-12-31 23:59:59'),
            'updated_at' => now(),
        ];
    }
}
