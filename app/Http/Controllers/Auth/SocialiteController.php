<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Contracts\Services\{
    UserServiceInterface,
    SocialiteServiceInterface,
};

class SocialiteController extends Controller
{
    /**
     * The socialite service.
     *
     * @var \App\Contracts\Services\SocialiteServiceInterface
     */
    private SocialiteServiceInterface $socialiteService;

    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private UserServiceInterface $userService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\SocialiteServiceInterface $socialiteService
     * @param \App\Contracts\Services\UserServiceInterface $userService
     * @return void
     */
    public function __construct(
        SocialiteServiceInterface $socialiteService,
        UserServiceInterface $userService,
    ) {
        $this->socialiteService = $socialiteService;
        $this->userService = $userService;
    }

    /**
     * Redirects users to provider authentication.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $provider): RedirectResponse
    {
        return $this->socialiteService->redirect($provider);
    }

    /**
     * Handle users from provider.
     *
     * @param string $provider
     * @return mixed
     */
    public function callback(string $provider): mixed
    {
        $socialUser = $this->socialiteService->getCallbackUser($provider);

        $transformedUser = $this->socialiteService->formatSocialUser($socialUser);

        $user = $this->userService->firstOrCreate([
            'email' => $transformedUser['email'],
        ], $transformedUser);

        $this->socialiteService->associateSocials($provider, $user, $socialUser);

        $this->socialiteService->updateAvatar($user, $socialUser);

        $this->socialiteService->authenticate($user);

        return redirect()->away(
            $this->socialiteService->getRedirectablePath($user),
        );
    }
}
