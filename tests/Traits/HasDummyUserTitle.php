<?php

namespace Tests\Traits;

use App\Models\{User, Title, UserTitle};

trait HasDummyUserTitle
{
    /**
     * Create a new user title to given user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Title $title
     * @param array<string, mixed> $data
     * @return \App\Models\UserTitle
     */
    public function createDummyUserTitleTo(User $user, Title $title, array $data = []): UserTitle
    {
        $payload = [
            'user_id' => $user->id,
            'title_id' => $title->id,
            ...$data,
        ];

        return UserTitle::factory()->create($payload);
    }
}
