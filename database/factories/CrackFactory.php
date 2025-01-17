<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use App\Models\{Game, Cracker, Protection, Status};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Crack>
 */
class CrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_id' => Game::factory()->create(),
            'status_id' => Status::factory()->create(),
            'cracker_id' => Cracker::factory()->create(),
            'protection_id' => Protection::factory()->create(),
            'cracked_at' => Carbon::today()->subDays(fake()->numberBetween(1, 30)),
        ];
    }
}
