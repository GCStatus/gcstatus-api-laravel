<?php

namespace App\Services;

use App\Models\{User, Title, UserTitle};
use App\Contracts\Services\UserTitleServiceInterface;
use App\Contracts\Repositories\UserTitleRepositoryInterface;
use App\Exceptions\UserTitle\UserAlreadyHasGivenUserTitleException;

class UserTitleService extends AbstractService implements UserTitleServiceInterface
{
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

        return $result;
    }
}
