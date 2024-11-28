<?php

namespace App\Contracts\Services;

use App\Models\User;
use App\Contracts\HasCollectionForAuthInterface;

interface TransactionServiceInterface extends AbstractServiceInterface, HasCollectionForAuthInterface
{
    /**
     * Delete a specific transaction for user.
     *
     * @param \App\Models\User $user
     * @param mixed $id
     * @return void
     */
    public function deleteForUser(User $user, mixed $id): void;
}
