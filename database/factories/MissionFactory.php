<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mission>
 */
class MissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'for_all' => fake()->boolean(),
            'mission' => fake()->realText(),
            'description' => fake()->realText(),
            'coins' => fake()->numberBetween(1, 99999),
            'experience' => fake()->numberBetween(1, 99999),
            'frequency' => fake()->randomElement(['one_time', 'daily', 'weekly', 'monthly', 'yearly']),
            'status_id' => Status::AVAILABLE_STATUS_ID,
        ];
    }
}
