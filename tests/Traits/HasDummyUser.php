<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyUser
{
    /**
     * Create dummy user.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\User
     */
    public function createDummyUser(array $data = []): User
    {
        return User::factory()->withLevel()->create($data);
    }

    /**
     * Create dummy users.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function createDummyUsers(int $times, array $data = []): Collection
    {
        return User::factory($times)->withLevel()->create($data);
    }

    /**
     * Act as dummy user.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\User
     */
    public function actingAsDummyUser(array $data = []): User
    {
        $user = $this->createDummyUser($data);

        $this->actingAs($user);

        return $user;
    }
}
