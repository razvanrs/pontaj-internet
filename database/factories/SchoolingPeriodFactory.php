<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SchoolingPeriod;

class SchoolingPeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SchoolingPeriod::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'short_description' => $this->faker->word(),
            'started_at' => $this->faker->date(),
            'finished_at' => $this->faker->date(),
            'sel_order' => $this->faker->numberBetween(-1000, 1000),
        ];
    }
}
