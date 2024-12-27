<?php

namespace App\Observers;

use App\Models\Wallet;

class WalletObserver
{
    /**
     * Handle the Wallet "updated" event.
     *
     * @return void
     */
    public function updated(Wallet $wallet): void
    {
        /** @var \App\Models\User $user */
        $user = $wallet->user;

        $key = "auth.user.{$user->id}";

        cacher()->forget($key);
    }
}
