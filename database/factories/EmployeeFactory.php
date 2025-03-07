<?php

namespace Database\Factories;

use App\Models\MilitaryRankType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->lastName();
        $surname = $this->faker->name();
        return [
            'user_id' => User::factory()->create(),
            'military_rank_type_id' => MilitaryRankType::factory()->create(),
            'social_number' => $this->faker->numerify('#############'),
            'name' => $name,
            'surname' => $surname,
            'full_name' => "{$name} {$this->faker->name($gender = 1)} {$surname}",
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
