<?php

namespace App\Observers;

use App\Models\Profile;

class ProfileObserver
{
    /**
     * Handle the Profile "updated" event.
     *
     * @return void
     */
    public function updated(Profile $profile): void
    {
        /** @var \App\Models\User $user */
        $user = $profile->user;

        $key = "auth.user.{$user->id}";

        cacher()->forget($key);
    }
}
