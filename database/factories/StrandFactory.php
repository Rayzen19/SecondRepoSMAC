<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Strand>
 */
class StrandFactory extends Factory
{
    public function definition(): array
    {
        $codes = ['STEM', 'ABM', 'HUMSS', 'GAS', 'TVL-ICT', 'TVL-HE'];
        $code = $this->faker->unique()->randomElement($codes);

        $names = [
            'STEM' => 'Science, Technology, Engineering and Mathematics',
            'ABM' => 'Accountancy, Business and Management',
            'HUMSS' => 'Humanities and Social Sciences',
            'GAS' => 'General Academic Strand',
            'TVL-ICT' => 'Technical-Vocational-Livelihood - Information and Communications Technology',
            'TVL-HE' => 'Technical-Vocational-Livelihood - Home Economics',
        ];

        return [
            'code' => $code,
            'name' => $names[$code] ?? $this->faker->sentence(3),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
