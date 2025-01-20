<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Languageable>
 */
class LanguageableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'language_id' => Language::factory()->create(),
        ];
    }
}
