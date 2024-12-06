<?php

namespace Database\Factories;

use App\Models\{User, Title};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTitle>
 */
class UserTitleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enabled' => fake()->boolean(),
            'user_id' => User::factory()->create(),
            'title_id' => Title::factory()->create(),
        ];
    }
}
