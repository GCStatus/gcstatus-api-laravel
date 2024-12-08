<?php

namespace Tests\Traits;

use App\Models\{User, Title};
use Illuminate\Database\Eloquent\Collection;

trait HasDummyTitle
{
    /**
     * Create a dummy title.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Title
     */
    public function createDummyTitle(array $data = []): Title
    {
        return Title::factory()->create($data);
    }

    /**
     * Create dummy titles.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Title>
     */
    public function createDummyTitles(int $times, array $data = []): Collection
    {
        return Title::factory($times)->create($data);
    }

    /**
     * Associate a title to an user.
     *
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\Title
     */
    public function createDummyTitleToUser(User $user, array $data = []): Title
    {
        $title = $this->createDummyTitle($data);

        $title->users()->save($user, [
            'enabled' => true,
        ])->save();

        return $title;
    }
}
