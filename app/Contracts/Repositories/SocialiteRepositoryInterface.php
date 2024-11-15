<?php

namespace App\Contracts\Repositories;

use Laravel\Socialite\Two\User;
use Illuminate\Http\RedirectResponse;

interface SocialiteRepositoryInterface
{
    /**
     * Redirect the users to the provider authentication server.
     *
     * @param string $provider
     * @param string $state
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $provider, string $state): RedirectResponse;

    /**
     * Receives the callback from authentication provider and return user.
     *
     * @param string $provider
     * @return \Laravel\Socialite\Two\User
     */
    public function getCallbackUser(string $provider): User;
}
