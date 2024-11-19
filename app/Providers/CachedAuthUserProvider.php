<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Contracts\Services\CacheServiceInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class CachedAuthUserProvider extends EloquentUserProvider
{
    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private $cacheService;

    /**
     * Create a new class instance.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @return void
     */
    public function __construct(HasherContract $hasher, CacheServiceInterface $cacheService)
    {
        parent::__construct($hasher, User::class);

        $this->cacheService = $cacheService;
    }

    /**
     * Retrieve the user by id.
     *
     * @param mixed $identifier
     * @return ?\Illuminate\Contracts\Auth\Authenticatable
     */
    public function retrieveById(mixed $identifier): ?Authenticatable
    {
        /** @var string $identifier */
        $key = "auth.user.$identifier";

        /** @var ?\App\Models\User $user */
        $user = $this->cacheService->get($key);

        if (!$user) {
            /** @var ?\App\Models\User $user */
            $user = parent::retrieveById($identifier);

            if ($user) {
                $user->load('profile', 'level', 'wallet');

                if (!App::runningUnitTests()) {
                    $jwtTtl = config('jwt.ttl');

                    $this->cacheService->put(
                        $key,
                        $user,
                        $jwtTtl * 60,
                    );
                }
            }
        }

        return $user;
    }
}
