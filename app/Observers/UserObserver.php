<?php

namespace App\Observers;

use App\Models\{User, Level};

class UserObserver
{
    /**
     * Handle the Food "creating" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function creating(User $user): void
    {
        $level = Level::query()->orderBy('level')->firstOrFail();

        $user->level()->associate($level);
    }

    /**
     * Handle the User "created" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function created(User $user): void
    {
        $user->wallet()->create([
            'amount' => 0,
        ]);

        $user->profile()->create([
            'share' => false,
        ]);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function updated(User $user): void
    {
        $key = "auth.user.{$user->id}";

        cacher()->forget($key);
    }
}
