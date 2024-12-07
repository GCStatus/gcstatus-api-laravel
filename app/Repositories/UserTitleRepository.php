<?php

namespace App\Repositories;

use App\Models\UserTitle;
use App\Contracts\Repositories\UserTitleRepositoryInterface;

class UserTitleRepository extends AbstractRepository implements UserTitleRepositoryInterface
{
    /**
     * The user title repository.
     *
     * @return \App\Models\UserTitle
     */
    public function model(): UserTitle
    {
        return new UserTitle();
    }
}
