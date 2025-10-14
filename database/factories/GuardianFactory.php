<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guardian>
 */
class GuardianFactory extends Factory
{
    public function definition(): array
    {
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();
        return [
            'guardian_number' => strtoupper(Str::random(10)),
            'first_name' => $first,
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $last,
            'suffix' => $this->faker->optional()->suffix(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'email' => strtolower($first.'.'.$last).'@example.com',
            'mobile_number' => $this->faker->unique()->numerify('09#########'),
            'address' => $this->faker->optional()->address(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'profile_picture' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
