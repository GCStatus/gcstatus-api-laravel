<?php

namespace App\Contracts\Services;

use App\Models\User;

interface LevelServiceInterface extends AbstractServiceInterface
{
    /**
     * Handle level up the given user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function handleLevelUp(User $user): void;
}
