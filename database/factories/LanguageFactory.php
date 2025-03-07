<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Language;

class LanguageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Language::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'short_description' => $this->faker->word(),
            'long_description' => $this->faker->word(),
            'sel_order' => $this->faker->numberBetween(-1000, 1000),
        ];
    }
}
