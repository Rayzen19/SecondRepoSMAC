<?php

namespace Database\Factories;

use App\Models\SubjectEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SubjectRecord>
 */
class SubjectRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_enrollment_id' => SubjectEnrollment::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'score' => $this->faker->randomFloat(2, 0, 100),
            'max_score' => 100,
            'type' => $this->faker->randomElement(['written work', 'performance task', 'quarterly assessment']),
            'quarter' => $this->faker->randomElement(['1st', '2nd']),
            'date_given' => $this->faker->date(),
        ];
    }
}
