<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use App\Models\{User, Mission};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMission>
 */
class UserMissionFactory extends Factory
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
            'mission_id' => Mission::factory()->create(),
            'last_completed_at' => Carbon::now()->subDays(fake()->numberBetween(1, 10)),
        ];
    }
}
