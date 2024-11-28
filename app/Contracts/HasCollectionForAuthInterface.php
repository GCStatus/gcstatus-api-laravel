<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface HasCollectionForAuthInterface
{
    /**
     * Get the collection of models for the given user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function allForAuth(User $user): Collection;
}
