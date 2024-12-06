<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'share' => fake()->boolean(),
            'photo' => fake()->imageUrl(),
            'phone' => fake()->phoneNumber(),
            'twitch' => fake()->url(),
            'github' => fake()->url(),
            'twitter' => fake()->url(),
            'youtube' => fake()->url(),
            'facebook' => fake()->url(),
            'instagram' => fake()->url(),
            'user_id' => User::factory()->create(),
        ];
    }
}
