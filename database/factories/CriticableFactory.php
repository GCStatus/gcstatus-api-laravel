<?php

namespace Database\Factories;

use App\Models\Critic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Criticable>
 */
class CriticableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'rate' => fake()->randomFloat(1, 1, 10),
            'posted_at' => fake()->date(),
            'critic_id' => Critic::factory()->create(),
        ];
    }
}
