<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\County;

class CountyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = County::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'short_description' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'long_description' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'abbreviation' => $this->faker->regexify('[A-Za-z0-9]{10}'),
            'sel_order' => $this->faker->numberBetween(-1000, 1000),
        ];
    }
}
