<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\MilitaryRank;
use App\Models\MilitaryRankType;

class MilitaryRankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MilitaryRank::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'military_rank' => $this->faker->word(),
            'military_rank_type_id' => MilitaryRankType::factory(),
        ];
    }
}
