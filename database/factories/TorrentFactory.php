<?php

namespace Database\Factories;

use App\Models\{Game, TorrentProvider};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Torrent>
 */
class TorrentFactory extends Factory
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
            'posted_at' => fake()->date(),
            'game_id' => Game::factory()->create(),
            'torrent_provider_id' => TorrentProvider::factory()->create(),
        ];
    }
}
