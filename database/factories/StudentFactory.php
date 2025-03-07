<?php

namespace Database\Factories;

use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_class_id' => StudentClass::factory()->create(),
            'erp_id' => $this->faker->numberBetween(1, 9999),
            'full_name' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
        ];
    }
}
