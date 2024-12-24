<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Support\{Str, Carbon};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     *
     * @var string $password
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'experience' => 0,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'nickname' => fake()->unique()->userName(),
            'email_verified_at' => now(),
            'password' => 'admin1234',
            'remember_token' => Str::random(10),
            'birthdate' => Carbon::today()->subYears(14)->subDay()->toDateString(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Ensure user has `level_id = 1`, creating level if needed.
     *
     * @return static
     */
    public function withLevel(): static
    {
        $level = Level::firstOrCreate(['id' => 1], ['level' => '1']);

        return $this->state(fn () => [
            'level_id' => $level->id,
        ]);
    }
}
