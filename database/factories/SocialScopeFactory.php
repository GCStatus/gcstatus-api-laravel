<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialScope>
 */
class SocialScopeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scope' => fake()->word(),
            'social_account_id' => SocialAccount::factory()->create(),
        ];
    }
}
