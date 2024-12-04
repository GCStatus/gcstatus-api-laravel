<?php

namespace Database\Factories;

use App\Models\Mission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MissionRequirement>
 */
class MissionRequirementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->word(),
            'task' => fake()->realText(),
            'goal' => fake()->numberBetween(1, 100),
            'description' => fake()->realText(),
            'mission_id' => Mission::factory()->create(),
        ];
    }
}
