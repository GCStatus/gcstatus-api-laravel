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
}
