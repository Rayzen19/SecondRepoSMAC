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
        return [
            'employee_number' => strtoupper(Str::random(8)),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'suffix' => $this->faker->optional()->suffix(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'address' => $this->faker->optional()->address(),
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
