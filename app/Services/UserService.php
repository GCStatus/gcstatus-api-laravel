<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exceptions\Password\CurrentPasswordDoesNotMatchException;
use App\Contracts\Services\{
    UserServiceInterface,
    HashServiceInterface,
    LevelServiceInterface,
    LevelNotificationServiceInterface,
};

class UserService extends AbstractService implements UserServiceInterface
{
    /**
     * The level service.
     *
     * @var \App\Contracts\Services\LevelServiceInterface
     */
    private LevelServiceInterface $levelService;

    /**
     * The level notification service.
     *
     * @var \App\Contracts\Services\LevelNotificationServiceInterface
     */
    private LevelNotificationServiceInterface $levelNotificationService;

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
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->levelService = app(LevelServiceInterface::class);
        $this->levelNotificationService = app(LevelNotificationServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function firstOrCreate(array $searchable, array $creatable): User
    {
        return $this->repository()->firstOrCreate($searchable, $creatable);
    }

    /**
     * @inheritDoc
     */
    public function addExperience(User $user, int $amount): void
    {
        $this->repository()->addExperience($user, $amount);

        $this->levelService->handleLevelUp($user);

        $this->levelNotificationService->notifyExperienceGained($user, $amount);
    }

    /**
     * @inheritDoc
     */
    public function updatePassword(User $user, string $old_password, string $password): void
    {
        $this->assertPasswordMatch($user, $old_password);

        $user->update([
            'password' => $password,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function updateSensitives(User $user, array $data): void
    {
        $this->assertPasswordMatch($user, $data['password']);

        $user->update([
            'email' => $data['email'],
            'nickname' => $data['nickname'],
        ]);
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
