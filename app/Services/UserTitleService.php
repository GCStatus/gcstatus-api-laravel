<?php

namespace App\Services;

use App\Models\{User, Title, UserTitle};
use App\Contracts\Repositories\UserTitleRepositoryInterface;
use App\Exceptions\UserTitle\UserAlreadyHasGivenUserTitleException;
use App\Contracts\Services\{
    UserTitleServiceInterface,
    TitleNotificationServiceInterface,
};

class UserTitleService extends AbstractService implements UserTitleServiceInterface
{
    /**
     * The title notification service.
     *
     * @var \App\Contracts\Services\TitleNotificationServiceInterface
     */
    private TitleNotificationServiceInterface $titleNotificationService;

    /**
     * Get the repository instance.
     *
     * @return \App\Contracts\Repositories\UserTitleRepositoryInterface
     */
    public function repository(): UserTitleRepositoryInterface
    {
        return app(UserTitleRepositoryInterface::class);
    }

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->titleNotificationService = app(TitleNotificationServiceInterface::class);
    }

    /**
     * Assign a title to given user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Title $title
     * @return \App\Models\UserTitle
     */
    public function assignTitleToUser(User $user, Title $title): UserTitle
    {
        $user->load('titles');

        if ($user->titles->contains('pivot.title_id', $title->id)) {
            throw new UserAlreadyHasGivenUserTitleException();
        }

        $data = [
            'user_id' => $user->id,
            'title_id' => $title->id,
        ];

        /** @var \App\Models\UserTitle $result */
        $result = $this->repository()->create($data);

        $this->titleNotificationService->notifyNewTitle($user, $title);

        return $result;
    }
}
