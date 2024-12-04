<?php

namespace Database\Factories;

use App\Models\{User, MissionRequirement};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMissionProgress>
 */
class UserMissionProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'completed' => fake()->boolean(),
            'user_id' => User::factory()->create(),
            'progress' => fake()->numberBetween(1, 10),
            'mission_requirement_id' => MissionRequirement::factory()->create(),
        ];
    }
}
