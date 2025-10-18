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
        $gender = $this->faker->randomElement(['male', 'female']);
        $firstNames = [
            'male' => ['Juan', 'Jose', 'Carlos', 'Miguel', 'Rafael', 'Antonio', 'Francisco', 'Pedro', 'Luis', 'Gabriel'],
            'female' => ['Maria', 'Ana', 'Isabel', 'Sofia', 'Carmen', 'Rosa', 'Elena', 'Patricia', 'Angela', 'Teresa']
        ];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Lopez', 'Gonzales', 'Rodriguez'];
        $caviteAddresses = [
            'Brgy. San Antonio, Dasmariñas, Cavite',
            'Brgy. Paliparan, Dasmariñas, Cavite',
            'Block 2 Lot 8, Villa Verde Subdivision, Dasmariñas, Cavite',
            'Phase 3 Block 5 Lot 12, Cambridge Village, Dasmariñas, Cavite',
            'Brgy. Salitran, Dasmariñas, Cavite'
        ];
        
        $first = $firstNames[$gender][array_rand($firstNames[$gender])];
        $last = $lastNames[array_rand($lastNames)];
        
        return [
            'guardian_number' => strtoupper(Str::random(10)),
            'first_name' => $first,
            'middle_name' => $this->faker->optional()->passthrough($firstNames[$gender][array_rand($firstNames[$gender])]),
            'last_name' => $last,
            'suffix' => $this->faker->optional(0.1)->randomElement(['Jr.', 'Sr.', 'II', 'III']),
            'gender' => $gender,
            'email' => strtolower($first.'.'.$last).'@example.com',
            'mobile_number' => $this->faker->unique()->numerify('09#########'),
            'address' => $this->faker->optional()->passthrough($caviteAddresses[array_rand($caviteAddresses)]),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'profile_picture' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
