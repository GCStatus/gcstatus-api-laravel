<?php

namespace App\Repositories;

use Laravel\Socialite\Two\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use App\Contracts\Repositories\SocialiteRepositoryInterface;

class SocialiteRepository implements SocialiteRepositoryInterface
{
    /**
     * Redirect the users to the provider authentication server.
     *
     * @param string $provider
     * @param string $state
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $provider, string $state): RedirectResponse
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $socialite */
        $socialite = Socialite::driver($provider);

        return $socialite->with([
            'state' => $state,
            'prompt' => 'select_account',
        ])->stateless()->redirect();
    }

    /**
     * Receives the callback from authentication provider.
     *
     * @param string $provider
     * @return \Laravel\Socialite\Two\User
     */
    public function getCallbackUser(string $provider): User
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $socialite */
        $socialite = Socialite::driver($provider);

        /** @var \Laravel\Socialite\Two\User $user */
        $user = $socialite->stateless()->user();

        return $user;
    }
}
