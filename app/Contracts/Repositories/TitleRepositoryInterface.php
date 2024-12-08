<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface TitleRepositoryInterface
{
    /**
     * Get all available titles for user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Title>
     */
    public function allForUser(): Collection;
}
