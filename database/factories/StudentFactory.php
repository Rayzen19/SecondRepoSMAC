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

        return [
            'student_number' => strtoupper($student_number),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'suffix' => $this->faker->suffix(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birthdate' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_number' => $this->faker->unique()->e164PhoneNumber(),
            'address' => $this->faker->address(),
            'guardian_name' => $this->faker->name(),
            'guardian_contact' => $this->faker->unique()->e164PhoneNumber(),
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
