<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequirementType>
 */
class RequirementTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'os' => fake()->randomElement(['windows', 'linux', 'mac']),
            'potential' => fake()->randomElement(['minimum', 'recommended', 'maximum']),
        ];
    }
}
