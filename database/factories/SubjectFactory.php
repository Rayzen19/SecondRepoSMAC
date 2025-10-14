<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $types = ['core', 'applied', 'specialized'];
        $semesters = ['1st', '2nd'];

        return [
            'code' => $this->faker->unique()->bothify('SUBJ####'),
            'name' => $this->faker->words(2, true) . ' ' . $this->faker->randomElement(['Basics','Fundamentals','Advanced','Principles']),
            'description' => $this->faker->optional(0.7)->sentence(12),
            'units' => $this->faker->numberBetween(1, 5),
            'type' => $this->faker->randomElement($types),
            'semester' => $this->faker->randomElement($semesters),
        ];
    }
}
