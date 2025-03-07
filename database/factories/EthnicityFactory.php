<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Ethnicity;

class EthnicityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ethnicity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'short_description' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'sel_order' => $this->faker->numberBetween(-1000, 1000),
        ];
    }
}
