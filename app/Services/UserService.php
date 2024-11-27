<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exceptions\Password\CurrentPasswordDoesNotMatchException;
use App\Contracts\Services\{
    AuthServiceInterface,
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
     * Update the user data.
     *
     * @param array<string, mixed> $data
     * @param mixed $id
     * @return \App\Models\User
     */
    public function update(array $data, mixed $id): User
    {
        /** @var \App\Models\User $user */
        $user = $this->repository()->update($data, $id);

        $this->forgetAuthUserCache($user);

        return $user;
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
        $this->assertPasswordMatch($user, $old_password);

        $user->update([
            'password' => $password,
        ]);

        $this->forgetAuthUserCache($user);
    }

    /**
     * Update the user sensitive data.
     *
     * @param \App\Models\User $user
     * @param array<string, string> $data
     * @return void
     */
    public function updateSensitives(User $user, array $data): void
    {
        $this->assertPasswordMatch($user, $data['password']);

        $user->update([
            'email' => $data['email'],
            'nickname' => $data['nickname'],
        ]);

        $this->forgetAuthUserCache($user);
    }

    /**
     * Forget user cache.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function forgetAuthUserCache(User $user): void
    {
        $authService = app(AuthServiceInterface::class);

        $authService->forgetAuthUserCache($user);
    }

    /**
     * Check if user password match with given password.
     *
     * @param \App\Models\User $user
     * @param string $password
     * @throws \App\Exceptions\Password\CurrentPasswordDoesNotMatchException
     * @return void
     */
    private function assertPasswordMatch(User $user, string $password): void
    {
        $hashService = app(HashServiceInterface::class);

        /** @var string $current */
        $current = $user->password;

        if (!$hashService->check($current, $password)) {
            throw new CurrentPasswordDoesNotMatchException();
        }
    }
}
