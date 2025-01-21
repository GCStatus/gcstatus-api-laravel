<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reviewable>
 */
class ReviewableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rate' => fake()->numberBetween(1, 5),
            'review' => fake()->realText(),
            'consumed' => fake()->boolean(),
            'user_id' => User::factory()->create(),
        ];
    }
}
