<?php

namespace App\Services;

use Illuminate\Support\Str;
use Laravel\Socialite\Two\User;
use App\Models\User as ModelsUser;
use Illuminate\Http\RedirectResponse;
use App\Contracts\Repositories\SocialiteRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    CacheServiceInterface,
    JWTServiceInterface,
    ProfileServiceInterface,
    SocialiteServiceInterface,
    SocialScopeServiceInterface,
    SocialAccountServiceInterface,
};

class SocialiteService implements SocialiteServiceInterface
{
    /**
     * The socialite repository.
     *
     * @var \App\Contracts\Repositories\SocialiteRepositoryInterface
     */
    private SocialiteRepositoryInterface $socialiteRepository;

    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private CacheServiceInterface $cacheService;

    /**
     * The social account service.
     *
     * @var \App\Contracts\Services\SocialAccountServiceInterface
     */
    private SocialAccountServiceInterface $socialAccountService;

    /**
     * The social scope service.
     *
     * @var \App\Contracts\Services\SocialScopeServiceInterface
     */
    private SocialScopeServiceInterface $socialScopeService;

    /**
     * The profile service.
     *
     * @var \App\Contracts\Services\ProfileServiceInterface
     */
    private ProfileServiceInterface $profileService;

    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The jwt service.
     *
     * @var \App\Contracts\Services\JWTServiceInterface
     */
    private JWTServiceInterface $jwtService;

    /**
     * The state TTL in minutes.
     *
     * @var int
     */
    private const STATE_TTL = 5;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\SocialiteRepositoryInterface $socialiteRepository
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @param \App\Contracts\Services\SocialAccountServiceInterface $socialAccountService
     * @param \App\Contracts\Services\SocialScopeServiceInterface $socialScopeService
     * @param \App\Contracts\Services\ProfileServiceInterface $profileService
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\JWTServiceInterface $jwtService
     * @return void
     */
    public function __construct(
        SocialiteRepositoryInterface $socialiteRepository,
        CacheServiceInterface $cacheService,
        SocialAccountServiceInterface $socialAccountService,
        SocialScopeServiceInterface $socialScopeService,
        ProfileServiceInterface $profileService,
        AuthServiceInterface $authService,
        JWTServiceInterface $jwtService,
    ) {
        $this->socialiteRepository = $socialiteRepository;
        $this->cacheService = $cacheService;
        $this->socialAccountService = $socialAccountService;
        $this->socialScopeService = $socialScopeService;
        $this->profileService = $profileService;
        $this->authService = $authService;
        $this->jwtService = $jwtService;
    }

    /**
     * Redirect the users to the provider authentication server.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $provider): RedirectResponse
    {
        $state = $this->generateState();

        $this->setState($state);

        return $this->socialiteRepository->redirect($provider, $state);
    }

    /**
     * Generate state for provider.
     *
     * @return string
     */
    public function generateState(): string
    {
        return Str::random(40);
    }

    /**
     * Set the state cache for security between provider.
     *
     * @param string $state
     * @return void
     */
    public function setState(string $state): void
    {
        $key = 'oauth_state_' . $state;

        $this->cacheService->put($key, true, now()->addMinutes(self::STATE_TTL));
    }

    /**
     * Pull (retrieve and delete) state from cache.
     *
     * @param string $key
     * @return mixed
     */
    public function pullState(string $key): mixed
    {
        return $this->cacheService->pull($key);
    }

    /**
     * Receives the callback from authentication provider.
     *
     * @param string $provider
     * @return \Laravel\Socialite\Two\User
     */
    public function getCallbackUser(string $provider): User
    {
        return $this->socialiteRepository->getCallbackUser($provider);
    }

    /**
     * Transform user from provider to platform user data type.
     *
     * @param \Laravel\Socialite\Two\User $user
     * @return array<string, mixed>
     */
    public function formatSocialUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'nickname' => $user->getNickname(),
        ];
    }

    /**
     * Create socials for user if applicable.
     *
     * @param string $provider
     * @param \App\Models\User $user
     * @param \Laravel\Socialite\Two\User $socialUser
     * @return void
     */
    public function associateSocials(string $provider, ModelsUser $user, User $socialUser): void
    {
        $socialAccount = $this->socialAccountService->firstOrCreate([
            'user_id' => $user->id,
            'provider' => $provider,
        ], [
            'provider_id' => $socialUser->getId(),
        ]);

        if ($socialAccount->wasRecentlyCreated) {
            if ($socialUser->approvedScopes) {
                foreach ($socialUser->approvedScopes as $scope) {
                    $this->socialScopeService->firstOrCreate([
                        'scope' => $scope,
                        'social_account_id' => $socialAccount->id,
                    ], [
                        'scope' => $scope,
                    ]);
                }
            }
        }
    }

    /**
     * Update the user avatar according social avatar.
     *
     * @param \App\Models\User $user
     * @param \Laravel\Socialite\Two\User $socialUser
     * @return void
     */
    public function updateAvatar(ModelsUser $user, User $socialUser): void
    {
        if (!$user->profile?->photo) {
            $this->profileService->updateForUser($user, [
                'photo' => $socialUser->getAvatar(),
            ]);
        }
    }

    /**
     * Get path to redirect after handling provider callback.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function getRedirectablePath(ModelsUser $user): string
    {
        /** @var string $base */
        $base = config('gcstatus.front_base_url');

        /** @var string $path */
        $path = $user->wasRecentlyCreated ? $base .= 'register/complete' : $base;

        return $path;
    }

    /**
     * Authenticates the user through socialite.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function authenticate(ModelsUser $user): void
    {
        $token = $this->jwtService->tokenize($user);

        $this->authService->setAuthenticationCookies($token);
    }
}
