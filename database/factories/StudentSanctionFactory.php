<?php

namespace Database\Factories;

use App\Models\Sanction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentSanction>
 */
class StudentSanctionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::inRandomOrder()->first()->id,
            'sanction_id' => Sanction::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
