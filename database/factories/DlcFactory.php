<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dlc>
 */
class DlcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = fake()->sentence(),
            'slug' => Str::slug($title),
            'free' => fake()->boolean(),
            'cover' => fake()->imageUrl(),
            'about' => fake()->realText(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'legal' => fake()->text(),
            'game_id' => Game::factory()->create(),
            'release_date' => fake()->date(),
        ];
    }
}
