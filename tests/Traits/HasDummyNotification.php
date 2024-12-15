<?php

namespace Tests\Traits;

use App\Models\User;
use Tests\Notifications\DummyNotification;

trait HasDummyNotification
{
    /**
     * Create a dummy notification to given user.
     *
     * @param \App\Models\User $user
     * @param array<string, string> $data
     * @return void
     */
    public function createDummyNotificationTo(User $user, array $data = []): void
    {
        $user->notify(new DummyNotification($data));
    }
}
