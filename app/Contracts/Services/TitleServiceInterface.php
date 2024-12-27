<?php

namespace App\Contracts\Services;

use App\Models\Title;
use Illuminate\Database\Eloquent\Collection;

interface TitleServiceInterface
{
    /**
     * Get all titles for authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Title>
     */
    public function allForUser(): Collection;

    /**
     * Find or fail a given title by id.
     *
     * @param mixed $id
     * @return \App\Models\Title
     */
    public function findOrFail(mixed $id): Title;
}
