<?php

namespace Database\Factories;

use App\Models\RequirementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requirementable>
 */
class RequirementableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'os' => fake()->word(),
            'dx' => fake()->sentence(),
            'cpu' => fake()->word(),
            'gpu' => fake()->sentence(),
            'ram' => fake()->word(),
            'rom' => fake()->word(),
            'obs' => fake()->text(),
            'network' => fake()->text(),
            'requirement_type_id' => fake()->numberBetween(1, RequirementType::count()),
        ];
    }
}
