<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $firstNames = [
            'male' => ['Juan', 'Jose', 'Carlos', 'Miguel', 'Rafael', 'Antonio', 'Francisco', 'Pedro', 'Luis', 'Gabriel'],
            'female' => ['Maria', 'Ana', 'Isabel', 'Sofia', 'Carmen', 'Rosa', 'Elena', 'Patricia', 'Angela', 'Teresa']
        ];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Lopez', 'Gonzales', 'Rodriguez'];
        $caviteAddresses = [
            'Brgy. San Antonio, Dasmariñas, Cavite',
            'Brgy. Paliparan, Dasmariñas, Cavite',
            'Block 5 Lot 10, Villa Verde Subdivision, Dasmariñas, Cavite',
            'Phase 1 Block 2 Lot 3, Cambridge Village, Dasmariñas, Cavite',
            '456 Aguinaldo Highway, Dasmariñas, Cavite'
        ];

        return [
            'employee_number' => strtoupper(Str::random(8)),
            'first_name' => $firstNames[$gender][array_rand($firstNames[$gender])],
            'middle_name' => $this->faker->optional()->passthrough($firstNames[$gender][array_rand($firstNames[$gender])]),
            'last_name' => $lastNames[array_rand($lastNames)],
            'suffix' => $this->faker->optional(0.1)->randomElement(['Jr.', 'Sr.', 'II', 'III']),
            'gender' => $gender,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '09' . $this->faker->numerify('#########'),
            'address' => $this->faker->optional()->passthrough($caviteAddresses[array_rand($caviteAddresses)]),
            'department' => $this->faker->randomElement(['Computer Science Dept', 'Math Dept', 'English Dept']),
            'specialization' => $this->faker->optional()->word(),
            'term' => $this->faker->randomElement([
                '2025-2026 (1st Semester)',
                '2025-2026 (2nd Semester)',
                '2024-2025 (1st Semester)',
                '2024-2025 (2nd Semester)'
            ]),
            'status' => $this->faker->randomElement(['active', 'inactive', 'retired', 'resigned']),
            'profile_picture' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
