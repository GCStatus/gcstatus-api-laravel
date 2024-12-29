<?php

namespace App\Contracts\Services;

use App\Models\{User, Title, UserTitle};

interface UserTitleServiceInterface extends AbstractServiceInterface
{
    /**
     * Assign a title to given user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Title $title
     * @return \App\Models\UserTitle
     */
    public function assignTitleToUser(User $user, Title $title): UserTitle;

    /**
     * Buy a given title by id.
     *
     * @param mixed $id
     * @return \App\Models\UserTitle
     */
    public function buyTitle(mixed $id): UserTitle;

    /**
     * Toggle the title enable for user.
     *
     * @param mixed $id
     * @return void
     */
    public function toggle(mixed $id): void;
}
