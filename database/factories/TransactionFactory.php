<?php

namespace Database\Factories;

use App\Models\{TransactionType, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->numberBetween(10, 1000),
            'description' => fake()->realText(),
            'user_id' => User::factory()->create(),
            'transaction_type_id' => TransactionType::factory()->create(),
        ];
    }
}
