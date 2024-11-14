<?php

namespace App\Contracts\Services;

use Laravel\Socialite\Two\User;
use App\Models\User as ModelsUser;
use Illuminate\Http\RedirectResponse;

interface SocialiteServiceInterface
{
    /**
     * Redirect the users to the provider authentication server.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $provider): RedirectResponse;

    /**
     * Generate state for provider.
     *
     * @return string
     */
    public function generateState(): string;

    /**
     * Set the state cache for security between provider.
     *
     * @param string $state
     * @return void
     */
    public function setState(string $state): void;

    /**
     * Pull (retrieve and delete) state from cache.
     *
     * @param string $key
     * @return mixed
     */
    public function pullState(string $key): mixed;

    /**
     * Receives the callback from authentication provider.
     *
     * @param string $provider
     * @return \Laravel\Socialite\Two\User
     */
    public function getCallbackUser(string $provider): User;

    /**
     * Transform user from provider to platform user data type.
     *
     * @param \Laravel\Socialite\Two\User $user
     * @return array<string, mixed>
     */
    public function formatSocialUser(User $user): array;

    /**
     * Create socials for user if applicable.
     *
     * @param string $provider
     * @param \App\Models\User $user
     * @param \Laravel\Socialite\Two\User $socialUser
     * @return void
     */
    public function associateSocials(string $provider, ModelsUser $user, User $socialUser): void;

    /**
     * Update the user avatar according social avatar.
     *
     * @param \App\Models\User $user
     * @param \Laravel\Socialite\Two\User $socialUser
     * @return void
     */
    public function updateAvatar(ModelsUser $user, User $socialUser): void;

    /**
     * Get path to redirect after handling provider callback.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function getRedirectablePath(ModelsUser $user): string;

    /**
     * Authenticates the user through socialite.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function authenticate(ModelsUser $user): void;
}
