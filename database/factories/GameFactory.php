<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
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
            'cover' => fake()->imageUrl(),
            'about' => fake()->realText(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'age' => fake()->numberBetween(0, 18),
            'free' => fake()->boolean(),
            'great_release' => fake()->boolean(),
            'legal' => fake()->text(),
            'website' => fake()->url(),
            'release_date' => fake()->date(),
            'condition' => fake()->randomElement(['hot', 'popular', 'sale', 'common']),
        ];
    }
}
