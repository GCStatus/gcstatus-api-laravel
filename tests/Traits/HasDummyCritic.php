<?php

namespace Tests\Traits;

use App\Models\Critic;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyCritic
{
    /**
     * Create a dummy critic.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Critic
     */
    public function createDummyCritic(array $data = []): Critic
    {
        return Critic::factory()->create($data);
    }

    /**
     * Create dummy critics.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Critic>
     */
    public function createDummyCritics(int $times, array $data = []): Collection
    {
        return Critic::factory($times)->create($data);
    }
}
