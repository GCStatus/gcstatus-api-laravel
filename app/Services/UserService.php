<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exceptions\Password\CurrentPasswordDoesNotMatchException;
use App\Contracts\Services\{
    UserServiceInterface,
    HashServiceInterface,
};

class UserService extends AbstractService implements UserServiceInterface
{
    /**
     * Get the repository instance.
     *
     * @return \App\Contracts\Repositories\UserRepositoryInterface
     */
    public function repository(): UserRepositoryInterface
    {
        return app(UserRepositoryInterface::class);
    }

    /**
     * Get the first user or create if doesn't exist.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\User
     */
    public function firstOrCreate(array $searchable, array $creatable): User
    {
        return $this->repository()->firstOrCreate($searchable, $creatable);
    }

    /**
     * Update the user password.
     *
     * @param \App\Models\User $user
     * @param string $old_password
     * @param string $password
     * @return void
     */
    public function updatePassword(User $user, string $old_password, string $password): void
    {
        $hashService = app(HashServiceInterface::class);

        /** @var string $current */
        $current = $user->password;

        if (!$hashService->check($current, $old_password)) {
            throw new CurrentPasswordDoesNotMatchException();
        }

        $user->update([
            'password' => $password,
        ]);
    }
}
