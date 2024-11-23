<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PasswordReseted;
use App\Contracts\Repositories\ResetPasswordRepositoryInterface;
use App\Contracts\Services\{
    UserServiceInterface,
    ResetPasswordServiceInterface,
};
use App\Exceptions\ResetPassword\{
    InvalidTokenException,
    UserRecentlyCreatedTokenException,
};

class ResetPasswordService implements ResetPasswordServiceInterface
{
    /**
     * The reset password repository.
     *
     * @var \App\Contracts\Repositories\ResetPasswordRepositoryInterface
     */
    private $resetPasswordRepository;

    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private UserServiceInterface $userService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\UserServiceInterface $userService
     * @param \App\Contracts\Repositories\ResetPasswordRepositoryInterface $resetPasswordRepository
     * @return void
     */
    public function __construct(
        UserServiceInterface $userService,
        ResetPasswordRepositoryInterface $resetPasswordRepository,
    ) {
        $this->userService = $userService;
        $this->resetPasswordRepository = $resetPasswordRepository;
    }

    /**
     * Send reset notification.
     *
     * @param array<string, string> $data
     * @return void
     */
    public function sendResetNotification(array $data): void
    {
        /** @var \App\Models\User $user */
        $user = $this->userService->findBy('email', $data['email']);

        if ($this->resetPasswordRepository->recentlyCreatedToken($user)) {
            throw new UserRecentlyCreatedTokenException();
        }

        $token = $this->resetPasswordRepository->createResetToken($user);

        $user->sendPasswordResetNotification($token);
    }

    /**
     * Reset suer password.
     *
     * @param array<string, string> $data
     * @return void
     */
    public function resetPassword(array $data): void
    {
        /** @var \App\Models\User $user */
        $user = $this->userService->findBy('email', $data['email']);

        $this->canResetPassword($user, $data['token']);

        $user->update([
            'password' => $data['password'],
        ]);

        $this->resetPasswordRepository->delete($user);

        $user->notify(new PasswordReseted());
    }

    /**
     * Check if user can reset password.
     *
     * @param \App\Models\User $user
     * @param string $token
     * @return void
     */
    public function canResetPassword(User $user, string $token): void
    {
        if (!$this->resetPasswordRepository->exists($user, $token)) {
            throw new InvalidTokenException();
        }
    }
}
