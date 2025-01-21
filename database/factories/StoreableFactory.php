<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Storeable>
 */
class StoreableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'price' => fake()->numberBetween(1000, 9999999),
            'store_id' => Store::factory()->create(),
            'store_item_id' => (string)fake()->randomDigit(),
        ];
    }
}
