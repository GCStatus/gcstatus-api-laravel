<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Title>
 */
class TitleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'purchasable' => fake()->boolean(),
            'description' => fake()->realText(),
            'cost' => fake()->numberBetween(10, 99999),
            'status_id' => Status::AVAILABLE_STATUS_ID,
        ];
    }
}
